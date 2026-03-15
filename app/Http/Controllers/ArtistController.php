<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    public function index(): Response
    {
        $artists = Artist::query()
            ->withCount(['events' => function ($q) {
                $q->published();
            }])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Artists/Index', [
            'artists' => $artists,
        ]);
    }

    public function show(Artist $artist): Response
    {
        $artist->load(['events' => function ($q) {
            $q->published()
                ->with('venue')
                ->orderBy('start_at');
        }]);

        return Inertia::render('Artists/Show', [
            'artist' => $artist,
            'upcomingEvents' => $artist->events,
        ]);
    }
}

