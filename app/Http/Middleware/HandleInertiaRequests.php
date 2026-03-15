<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cities = \Illuminate\Support\Facades\Cache::remember('header_cities', 3600, function () {
            return \App\Models\City::orderBy('sort_order')->orderBy('name')->get();
        });

        $currentCityName = 'Выберите город';
        $citySlug = $request->query('city');
        if ($citySlug) {
            $city = $cities->firstWhere('slug', $citySlug);
            if ($city) {
                $currentCityName = $city->name;
            }
        } elseif (auth()->check() && auth()->user()->city_id) {
            $userCity = $cities->firstWhere('id', auth()->user()->city_id);
            if ($userCity) {
                $currentCityName = $userCity->name;
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'favoriteArtistIds' => auth()->check() ? auth()->user()->favoriteArtists()->pluck('artists.id')->all() : [],
            ],
            'flash' => [
                'status' => session('status'),
                'errors' => session('errors') ? session('errors')->getMessages() : [],
            ],
            'currentCityName' => $currentCityName,
            'headerCities' => $cities,
            'unreadNotificationsCount' => auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
            'ziggy' => array_merge((new \Tighten\Ziggy\Ziggy)->toArray(), [
                'location' => $request->url(),
                'query' => $request->query(),
            ]),
        ];
    }
}
