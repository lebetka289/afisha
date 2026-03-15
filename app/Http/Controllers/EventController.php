<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Event;
use App\Models\EventView;
use App\Models\Artist;
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

        $favoriteArtists = [];
        if (auth()->check()) {
            $favoriteArtists = auth()->user()
                ->favoriteArtists()
                ->withCount('events')
                ->orderBy('name')
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'slug' => $a->slug,
                    'photo_src' => $a->photo_src,
                    'photo_is_video' => $a->photo_is_video,
                    'events_count' => $a->events_count,
                ]);
        }

        return Inertia::render('Events/Index', [
            'events' => $events,
            'cities' => $cities,
            'favoriteArtists' => $favoriteArtists,
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
        $results = [];
        $term = $q !== '' ? mb_strtolower($q, 'UTF-8') : '';
        $searchLike = $term !== '' ? '%' . addcslashes($term, '%_\\') . '%' : '';

        $eventQuery = Event::query()->published()->with('venue', 'artist')->orderBy('start_at');
        if ($searchLike !== '') {
            $eventQuery->where(function ($builder) use ($searchLike) {
                $builder->whereRaw('LOWER(title) LIKE ?', [$searchLike])
                    ->orWhereRaw('LOWER(subtitle) LIKE ?', [$searchLike])
                    ->orWhereHas('artist', fn ($a) => $a->whereRaw('LOWER(name) LIKE ?', [$searchLike]))
                    ->orWhereHas('venue', function ($v) use ($searchLike) {
                        $v->whereRaw('LOWER(name) LIKE ?', [$searchLike])
                            ->orWhereRaw('LOWER(city) LIKE ?', [$searchLike]);
                    });
            });
        }
        $events = $eventQuery->limit(6)->get()->map(fn (Event $event) => [
            'type' => 'event',
            'title' => $event->title,
            'subtitle' => $event->subtitle,
            'url' => route('events.show', $event),
            'date' => $event->start_at?->format('d.m.Y H:i'),
            'venue' => $event->venue?->name,
            'artist' => $event->artist?->name,
        ]);
        foreach ($events as $e) {
            $results[] = $e;
        }

        if ($searchLike !== '') {
            $artists = Artist::whereRaw('LOWER(name) LIKE ?', [$searchLike])
                ->orderBy('name')->limit(4)->get();

            foreach ($artists as $artist) {
                $results[] = [
                    'type' => 'artist',
                    'title' => $artist->name,
                    'subtitle' => 'Артист',
                    'url' => route('artists.show', $artist),
                    'date' => null,
                    'venue' => null,
                    'artist' => null,
                ];
                $artistEvents = Event::query()->published()->with('venue')
                    ->where('artist_id', $artist->id)
                    ->orderBy('start_at')
                    ->limit(6)
                    ->get();
                foreach ($artistEvents as $ev) {
                    $results[] = [
                        'type' => 'event',
                        'title' => $ev->title,
                        'subtitle' => $ev->subtitle,
                        'url' => route('events.show', $ev),
                        'date' => $ev->start_at?->format('d.m.Y H:i'),
                        'venue' => $ev->venue?->name,
                        'artist' => $artist->name,
                    ];
                }
            }

            $cities = City::whereRaw('LOWER(name) LIKE ?', [$searchLike])
                ->orderBy('name')->limit(4)->get();
            foreach ($cities as $city) {
                $results[] = [
                    'type' => 'city',
                    'title' => $city->name,
                    'subtitle' => 'Город',
                    'url' => route('events.index', ['city' => $city->slug]),
                    'date' => null,
                    'venue' => null,
                    'artist' => null,
                ];
            }
        }

        return response()->json([
            'items' => $results,
            'title' => $q === '' ? 'Популярно сейчас' : 'Результаты поиска',
        ]);
    }

    public function show(Event $event): Response
    {
        $event->load('sections.seats', 'addons', 'venue');
        $isFavorited = auth()->check() && auth()->user()->favoriteEvents()->where('event_id', $event->id)->exists();
        $mapLat = $event->latitude ?? $event->venue?->latitude ?? 55.7558;
        $mapLng = $event->longitude ?? $event->venue?->longitude ?? 37.6173;

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
