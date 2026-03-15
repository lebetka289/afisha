<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArtistRequest;
use App\Models\Artist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    public function index(): Response
    {
        $artists = Artist::latest()->paginate(15);

        return Inertia::render('Admin/Artists/Index', compact('artists'));
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Artists/Create', ['artist' => new Artist()]);
    }

    public function store(ArtistRequest $request): RedirectResponse
    {
        $data = $this->prepareArtistData($request, new Artist());
        Artist::create($data);

        return redirect()->route('admin.artists.index')->with('status', 'Исполнитель создан');
    }

    public function edit(Artist $artist): Response
    {
        return Inertia::render('Admin/Artists/Edit', compact('artist'));
    }

    public function update(ArtistRequest $request, Artist $artist): RedirectResponse
    {
        $data = $this->prepareArtistData($request, $artist);
        $artist->update($data);

        return redirect()->route('admin.artists.edit', $artist)->with('status', 'Исполнитель обновлён');
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        if ($artist->photo) {
            Storage::disk('public')->delete($artist->photo);
        }

        $artist->delete();

        return redirect()->route('admin.artists.index')->with('status', 'Исполнитель удалён');
    }

    protected function prepareArtistData(ArtistRequest $request, ?Artist $artist = null): array
    {
        $data = $request->validated();

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $links = [];
        if (! blank($data['links_json'] ?? null)) {
            $decoded = json_decode($data['links_json'], true);
            $links = is_array($decoded) ? $decoded : [];
        }
        $data['links'] = $links ?: null;
        unset($data['links_json']);

        if ($request->hasFile('photo')) {
            if ($artist?->photo) {
                Storage::disk('public')->delete($artist->photo);
            }
            $data['photo'] = $request->file('photo')->store('artists', 'public');
        } elseif ($artist) {
            $data['photo'] = $artist->photo;
        }

        return $data;
    }
}
