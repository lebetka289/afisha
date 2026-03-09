<?php

namespace App\Providers;

use App\Models\City;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $cities = City::orderBy('sort_order')->orderBy('name')->get();
            $currentCityName = 'Выберите город';
            $citySlug = request('city');
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
            $view->with('headerCities', $cities)->with('currentCityName', $currentCityName);
        });
    }
}
