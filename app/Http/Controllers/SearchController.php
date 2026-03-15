<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\City;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([
                'events' => [],
                'artists' => [],
                'cities' => [],
            ]);
        }

        $events = Event::query()
            ->published()
            ->with('venue')
            ->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('subtitle', 'like', "%{$q}%");
            })
            ->orderBy('start_at')
            ->limit(10)
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->title,
                'subtitle' => $event->subtitle,
                'date' => $event->start_at?->format('d.m.Y H:i'),
                'venue' => $event->venue?->name,
                'url' => route('events.show', $event),
            ]);

        $artists = Artist::query()
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (Artist $artist) => [
                'id' => $artist->id,
                'name' => $artist->name,
                'url' => route('artists.show', $artist),
            ]);

        $cities = City::query()
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn (City $city) => [
                'id' => $city->id,
                'name' => $city->name,
                'slug' => $city->slug,
            ]);

        return response()->json([
            'events' => $events,
            'artists' => $artists,
            'cities' => $cities,
        ]);
    }
}

