<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArtistRequest;
use App\Models\Artist;
use App\Models\ArtistAlbum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    public function index(): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление артистами доступно только администраторам.');
        $artists = Artist::latest()->paginate(15);

        return Inertia::render('Admin/Artists/Index', compact('artists'));
    }

    public function create(): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление артистами доступно только администраторам.');
        return Inertia::render('Admin/Artists/Create', ['artist' => new Artist()]);
    }

    public function store(ArtistRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление артистами доступно только администраторам.');
        $data = $this->prepareArtistData($request, new Artist());
        Artist::create($data);

        return redirect()->route('admin.artists.index')->with('status', 'Исполнитель создан');
    }

    public function edit(Artist $artist): Response
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление артистами доступно только администраторам.');
        $artist->load('albums');
        return Inertia::render('Admin/Artists/Edit', compact('artist'));
    }

    public function update(ArtistRequest $request, Artist $artist): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление артистами доступно только администраторам.');
        $data = $this->prepareArtistData($request, $artist);
        $albums = $data['albums'] ?? null;
        unset($data['albums']);
        $artist->update($data);
        if ($albums !== null) {
            $this->syncAlbums($artist, $albums);
        }

        return redirect()->route('admin.artists.edit', $artist)->with('status', 'Исполнитель обновлён');
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Управление артистами доступно только администраторам.');
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

    protected function syncAlbums(?Artist $artist, array $albums): void
    {
        if (! $artist) {
            return;
        }

        $keepIds = [];
        foreach ($albums as $index => $row) {
            $title = trim($row['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $id = $row['id'] ?? null;
            $attrs = [
                'title' => $title,
                'year' => ! empty($row['year']) ? (int) $row['year'] : null,
                'type' => $row['type'] ?? 'album',
                'link' => trim($row['link'] ?? '') ?: null,
                'cover_url' => trim($row['cover_url'] ?? '') ?: null,
                'sort_order' => $index,
            ];
            if ($id && $artist->albums()->where('id', $id)->exists()) {
                $artist->albums()->where('id', $id)->update($attrs);
                $keepIds[] = $id;
            } else {
                $album = $artist->albums()->create($attrs);
                $keepIds[] = $album->id;
            }
        }
        $artist->albums()->whereNotIn('id', $keepIds)->delete();
    }
}
