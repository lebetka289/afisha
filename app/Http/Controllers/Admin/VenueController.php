<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        $venues = Venue::latest()->paginate(15);

        return view('admin.venues.index', compact('venues'));
    }

    public function create(): View
    {
        return view('admin.venues.create', ['venue' => new Venue()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateVenue($request);
        Venue::create($data);

        return redirect()->route('admin.venues.index')->with('status', 'Площадка создана');
    }

    public function edit(Venue $venue): View
    {
        return view('admin.venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $data = $this->validateVenue($request, $venue->id);
        $venue->update($data);

        return redirect()->route('admin.venues.edit', $venue)->with('status', 'Площадка обновлена');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $venue->delete();

        return redirect()->route('admin.venues.index')->with('status', 'Площадка удалена');
    }

    protected function validateVenue(Request $request, ?int $venueId = null): array
    {
        $uniqueSlugRule = 'unique:venues,slug';
        if ($venueId) {
            $uniqueSlugRule .= ',' . $venueId;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlugRule],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_capacity' => ['nullable', 'integer', 'min:0'],
            'layout_type' => ['required', 'string', 'max:50'],
            'layout_config' => ['nullable'],
        ]);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $layoutConfig = $data['layout_config'] ?? null;
        if (is_string($layoutConfig) && $layoutConfig !== '') {
            $decoded = json_decode($layoutConfig, true);
            $data['layout_config'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return $data;
    }
}
