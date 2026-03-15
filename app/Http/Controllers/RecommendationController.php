<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventView;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    public function index(): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['events' => []]);
        }

        $userId = auth()->id();

        $recentEventIds = EventView::query()
            ->where('user_id', $userId)
            ->orderByDesc('viewed_at')
            ->limit(20)
            ->pluck('event_id')
            ->toArray();

        if (empty($recentEventIds)) {
            return response()->json(['events' => []]);
        }

        $baseEvents = Event::query()
            ->whereIn('id', $recentEventIds)
            ->with(['venue'])
            ->get();

        $artistIds = $baseEvents->pluck('artist_id')->filter()->unique()->values()->all();
        $cityNames = $baseEvents->pluck('venue.city')->filter()->unique()->values()->all();
        $categories = $baseEvents->pluck('category')->filter()->unique()->values()->all();

        $recommendedQuery = Event::query()
            ->published()
            ->with('venue')
            ->whereNotIn('id', $recentEventIds)
            ->where(function ($q) use ($artistIds, $cityNames, $categories) {
                $q->when($artistIds, fn ($sq) => $sq->orWhereIn('artist_id', $artistIds))
                    ->when($cityNames, fn ($sq) => $sq->orWhereHas('venue', fn ($v) => $v->whereIn('city', $cityNames)))
                    ->when($categories, fn ($sq) => $sq->orWhereIn('category', $categories));
            })
            ->orderBy('start_at')
            ->limit(10);

        $events = $recommendedQuery->get()->map(fn (Event $event) => [
            'id' => $event->id,
            'title' => $event->title,
            'subtitle' => $event->subtitle,
            'date' => $event->start_at?->format('d.m.Y H:i'),
            'venue' => $event->venue?->name,
            'url' => route('events.show', $event),
        ]);

        return response()->json(['events' => $events]);
    }
}

