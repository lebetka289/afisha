<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Artist;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventSection;
use App\Models\EventSeat;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        $events = Event::query()->with('venue')->latest()->paginate(10);

        return Inertia::render('Admin/Events/Index', compact('events'));
    }

    public function create(): Response
    {
        $venues = Venue::orderBy('name')->get();
        $artists = Artist::orderBy('name')->get();

        return Inertia::render('Admin/Events/Create', [
            'event' => new Event(),
            'venues' => $venues,
            'artists' => $artists,
        ]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $data = $this->validateEvent($request);

        $event = null;

        DB::transaction(function () use ($data, &$event) {
            $sectionsPayload = $data['sections_payload'];
            $addonsPayload = $data['addons_payload'] ?? [];
            unset($data['sections_payload'], $data['addons_payload']);

            $event = Event::create($data);

            $this->syncSections($event, $sectionsPayload);
            $this->syncAddons($event, $addonsPayload);
        });

        return redirect()->route('admin.events.edit', $event)->with('status', 'Событие создано');
    }

    public function edit(Event $event): Response
    {
        $event->load('sections.seats', 'addons');
        $venues = Venue::orderBy('name')->get();
        $artists = Artist::orderBy('name')->get();

        return Inertia::render('Admin/Events/Edit', compact('event', 'venues', 'artists'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $this->validateEvent($request, $event->id);

        DB::transaction(function () use ($data, $event) {
            $sectionsPayload = $data['sections_payload'];
            $addonsPayload = $data['addons_payload'] ?? [];
            unset($data['sections_payload'], $data['addons_payload']);

            $event->update($data);

            $this->syncSections($event, $sectionsPayload);
            $this->syncAddons($event, $addonsPayload);
        });

        return redirect()->route('admin.events.edit', $event)->with('status', 'Событие обновлено');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('status', 'Событие удалено');
    }

    protected function validateEvent(EventRequest $request, ?int $eventId = null): array
    {
        $validated = $request->validated();

        $layoutConfig = $this->decodeJsonField($validated['layout_config'] ?? null);
        $meta = $this->decodeJsonField($validated['meta'] ?? null);
        $sectionsPayload = $this->decodeJsonField($validated['sections_payload']);
        $validated['addons_payload'] = $this->decodeJsonField($validated['addons_payload'] ?? null) ?? [];

        if (! is_array($sectionsPayload) || empty($sectionsPayload)) {
            throw ValidationException::withMessages([
                'sections_payload' => 'Необходимо добавить хотя бы одну зону/сектор',
            ]);
        }

        $validated['layout_config'] = $layoutConfig;
        $validated['meta'] = $meta;
        $validated['sections_payload'] = $sectionsPayload;

        if ($request->hasFile('poster_upload')) {
            if ($eventId) {
                $existingEvent = Event::find($eventId);
                if ($existingEvent && filled($existingEvent->poster_url) && ! filter_var($existingEvent->poster_url, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($existingEvent->poster_url);
                }
            }

            $validated['poster_url'] = $request->file('poster_upload')->store('events', 'public');
        } elseif (($validated['poster_url'] ?? '') === '' && $eventId) {
            $existingEvent = Event::find($eventId);
            if ($existingEvent && filled($existingEvent->poster_url) && ! filter_var($existingEvent->poster_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($existingEvent->poster_url);
            }
            $validated['poster_url'] = null;
        }

        unset($validated['poster_upload']);

        return $validated;
    }

    protected function syncSections(Event $event, array $sectionsPayload): void
    {
        $existingIds = $event->sections()->pluck('id')->all();
        $keepIds = [];

        foreach ($sectionsPayload as $index => $sectionData) {
            $sectionAttributes = Arr::only($sectionData, [
                'name',
                'type',
                'seating_mode',
                'capacity',
                'price',
                'rows',
                'cols',
                'seat_map',
                'position',
                'color',
                'sort_order',
                'meta',
            ]);

            $sectionAttributes['sort_order'] = $sectionAttributes['sort_order'] ?? $index;
            $sectionAttributes['capacity'] = $sectionAttributes['capacity'] ?? 0;
            $sectionAttributes['price'] = $sectionAttributes['price'] ?? 0;

            if (! empty($sectionData['id']) && in_array($sectionData['id'], $existingIds, true)) {
                $section = EventSection::find($sectionData['id']);
                $section?->update($sectionAttributes);
            } else {
                $section = $event->sections()->create($sectionAttributes);
            }

            if (! $section) {
                continue;
            }

            $keepIds[] = $section->id;

            if ($section->seating_mode === 'seated') {
                $this->syncSeats($section, $sectionData);
            } else {
                $section->seats()->delete();
            }
        }

        $event->sections()->whereNotIn('id', $keepIds)->delete();
    }

    protected function syncAddons(Event $event, array $addonsPayload): void
    {
        $keepIds = [];
        foreach ($addonsPayload as $index => $row) {
            $name = trim($row['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $price = (float) ($row['price'] ?? 0);
            $description = trim($row['description'] ?? '');
            $id = $row['id'] ?? null;

            if ($id && $event->addons()->where('id', $id)->exists()) {
                $addon = EventAddon::find($id);
                $addon?->update(['name' => $name, 'price' => $price, 'description' => $description ?: null, 'sort_order' => $index]);
                $keepIds[] = $addon->id;
            } else {
                $addon = $event->addons()->create(['name' => $name, 'price' => $price, 'description' => $description ?: null, 'sort_order' => $index]);
                $keepIds[] = $addon->id;
            }
        }
        $event->addons()->whereNotIn('id', $keepIds)->delete();
    }

    protected function syncSeats(EventSection $section, array $sectionData): void
    {
        $rows = (int) ($sectionData['rows'] ?? 0);
        $cols = (int) ($sectionData['cols'] ?? 0);
        $seatMap = $sectionData['seat_map'] ?? [];

        $section->seats()->delete();

        if ($rows <= 0 || $cols <= 0) {
            return;
        }

        $bulk = [];
        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $cols; $col++) {
                $status = Arr::get($seatMap, "{$row}.{$col}.status", 'available');
                $label = Arr::get($seatMap, "{$row}.{$col}.label", chr(64 + $row) . $col);
                $price = Arr::get($seatMap, "{$row}.{$col}.price", $section->price);

                if ($status === 'blocked') {
                    continue;
                }

                $bulk[] = [
                    'event_section_id' => $section->id,
                    'label' => $label,
                    'row_number' => $row,
                    'col_number' => $col,
                    'status' => $status,
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (! empty($bulk)) {
            EventSeat::insert($bulk);
        }
    }

    protected function decodeJsonField(mixed $value): mixed
    {
        if (is_array($value) || is_null($value) || $value === '') {
            return $value ?: null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
