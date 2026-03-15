<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecommendationController extends Controller
{
    public function index(Request $request): Response
    {
        $events = collect();

        if (auth()->check()) {
            $userId = auth()->id();
            $user = auth()->user()->load('city');

            $viewedIds = EventView::query()
                ->where('user_id', $userId)
                ->orderByDesc('viewed_at')
                ->limit(30)
                ->pluck('event_id')
                ->toArray();

            $favoriteEventIds = $user->favoriteEvents()->get()->pluck('id')->all();
            $favoriteArtistIds = $user->favoriteArtists()->get()->pluck('id')->all();
            $userCityName = $user->city_id ? $user->city?->name : null;

            $baseEvents = Event::query()
                ->whereIn('id', array_merge($viewedIds, $favoriteEventIds))
                ->with(['venue', 'artist'])
                ->get();

            $artistIds = array_unique(array_merge(
                $baseEvents->pluck('artist_id')->filter()->values()->all(),
                $favoriteArtistIds
            ));
            $cityNames = $baseEvents->pluck('venue.city')->filter()->unique()->values()->all();
            if ($userCityName) {
                $cityNames[] = $userCityName;
            }
            $cityNames = array_unique($cityNames);
            $categories = $baseEvents->pluck('category')->filter()->unique()->values()->all();

            $excludeIds = array_unique(array_merge($viewedIds, $favoriteEventIds));

            $candidates = Event::query()
                ->published()
                ->with(['venue', 'artist', 'sections'])
                ->whereNotIn('id', $excludeIds)
                ->where('start_at', '>=', now())
                ->orderBy('start_at')
                ->limit(50)
                ->get();

            $scores = [];
            foreach ($candidates as $event) {
                $score = 0;
                if (in_array($event->artist_id, $artistIds)) {
                    $score += 30;
                }
                if ($event->venue && in_array($event->venue->city, $cityNames)) {
                    $score += 25;
                }
                if (in_array($event->category, $categories)) {
                    $score += 15;
                }
                $scores[$event->id] = $score;
            }

            $events = $candidates
                ->sortByDesc(fn ($e) => $scores[$e->id] ?? 0)
                ->take(12)
                ->values();
        }

        return Inertia::render('Recommendations/Index', [
            'events' => $events,
        ]);
    }
}

