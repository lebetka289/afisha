<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Event;
use App\Models\EventView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Event::with(['venue', 'sections'])
            ->published()
            ->orderBy('start_at');

        if ($request->filled('date_from')) {
            $query->whereDate('start_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_at', '<=', $request->date_to);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('city')) {
            $cities = Cache::remember('all_cities', 3600, fn () => City::all());
            $city = $cities->firstWhere('slug', $request->city) ?? $cities->firstWhere('id', $request->city);
            if ($city) {
                $query->whereHas('venue', fn ($q) => $q->where('city', $city->name));
            }
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhereHas('venue', fn ($v) => $v->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%"));
            });
        }

        $events = $query->paginate(6)->withQueryString();
        $cities = Cache::remember('all_cities_sorted', 3600, fn () => City::orderBy('sort_order')->orderBy('name')->get());

        $today = now()->startOfDay();
        $weekdays = ['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];
        $monthsRu = [1 => 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
        $rouletteDates = collect();
        for ($i = 0; $i < 60; $i++) {
            $date = $today->copy()->addDays($i);
            $rouletteDates->push([
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'weekday' => $weekdays[(int) $date->format('w')],
                'month' => $monthsRu[(int) $date->format('n')],
                'is_weekend' => $date->isWeekend(),
            ]);
        }

        return Inertia::render('Events/Index', [
            'events' => $events,
            'cities' => $cities,
            'rouletteDates' => $rouletteDates,
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'city' => $request->city,
                'category' => $request->category,
                'q' => $request->q,
            ],
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Event::query()
            ->published()
            ->with('venue')
            ->orderBy('start_at');

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('subtitle', 'like', "%{$q}%");
            });
        }

        $events = $query->limit(8)->get()->map(fn (Event $event) => [
            'title' => $event->title,
            'subtitle' => $event->subtitle,
            'url' => route('events.show', $event),
            'date' => $event->start_at?->format('d.m.Y H:i'),
            'venue' => $event->venue?->name,
        ]);

        return response()->json([
            'items' => $events,
            'title' => $q === '' ? 'Популярно сейчас' : 'Найденные мероприятия',
        ]);
    }

    public function show(Event $event): Response
    {
        $event->load('sections.seats', 'addons', 'venue');
        $isFavorited = auth()->check() && auth()->user()->favoriteEvents()->where('event_id', $event->id)->exists();
        $mapLat = $event->venue?->latitude ?? 55.7558;
        $mapLng = $event->venue?->longitude ?? 37.6173;

        if (auth()->check()) {
            EventView::create([
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'viewed_at' => now(),
            ]);
        }

        return Inertia::render('Events/Show', [
            'event' => $event,
            'isFavorited' => $isFavorited,
            'mapLat' => $mapLat,
            'mapLng' => $mapLng,
        ]);
    }
}
