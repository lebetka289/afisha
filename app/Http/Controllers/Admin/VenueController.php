<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VenueRequest;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VenueController extends Controller
{
    public function index(): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление площадками доступно только администраторам.');
        $venues = Venue::latest()->paginate(15);

        return Inertia::render('Admin/Venues/Index', compact('venues'));
    }

    public function create(): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление площадками доступно только администраторам.');
        return Inertia::render('Admin/Venues/Create', ['venue' => new Venue()]);
    }

    public function store(VenueRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление площадками доступно только администраторам.');
        $data = $this->prepareVenueData($request->validated());
        Venue::create($data);

        return redirect()->route('admin.venues.index')->with('status', 'Площадка создана');
    }

    public function edit(Venue $venue): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление площадками доступно только администраторам.');
        return Inertia::render('Admin/Venues/Edit', compact('venue'));
    }

    public function update(VenueRequest $request, Venue $venue): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление площадками доступно только администраторам.');
        $data = $this->prepareVenueData($request->validated());
        $venue->update($data);

        return redirect()->route('admin.venues.edit', $venue)->with('status', 'Площадка обновлена');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление площадками доступно только администраторам.');
        $venue->delete();

        return redirect()->route('admin.venues.index')->with('status', 'Площадка удалена');
    }

    protected function prepareVenueData(array $data): array
    {
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
