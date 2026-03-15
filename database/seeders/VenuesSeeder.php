<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VenuesSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            [
                'name' => 'Клуб «Конструктивно»',
                'city' => 'Москва',
                'address' => 'ул. Пепе Шнеля, 67',
                'description' => 'Конструктивно лучшая площадка. По факту. Чекай вайб.',
                'max_capacity' => 150,
            ],
            [
                'name' => 'Зал «Пепе Шнель приветствует»',
                'city' => 'Санкт-Петербург',
                'address' => 'пр. Конструктивно, 1',
                'description' => 'Пепе одобряет. Фа. Респект всем, кто зайдёт.',
                'max_capacity' => 80,
            ],
            [
                'name' => 'Бар «67» (сикс севен)',
                'city' => 'Нижние Пупки',
                'address' => 'пер. Пожарника, 13',
                'description' => 'Сикс севен причин зайти. Не кринж. Без базара.',
                'max_capacity' => 67,
            ],
            [
                'name' => 'ДК «Пожарник выезжает»',
                'city' => 'Екатеринбург',
                'address' => 'наб. Зашло, д. 0',
                'description' => 'Пожарник уже на сцене. Чекай. По факту зайдёт.',
                'max_capacity' => 50,
            ],
            [
                'name' => 'Лофт «Зашло / Не зашло»',
                'city' => 'Казань',
                'address' => 'ул. Респект, 1',
                'description' => 'Вайб норм. Зайдёшь — не кринж. Чилл до рассвета.',
                'max_capacity' => 20,
            ],
        ];

        foreach ($venues as $v) {
            Venue::firstOrCreate(
                ['slug' => Str::slug($v['name'])],
                array_merge($v, [
                    'latitude' => fake()->latitude(55, 56),
                    'longitude' => fake()->longitude(37, 38),
                    'layout_type' => 'rectangle',
                    'layout_config' => null,
                ])
            );
        }

        $cityName = City::inRandomOrder()->first()?->name ?? 'Мухосранск';
        Venue::factory()->count(5)->create(['city' => $cityName]);
    }
}
