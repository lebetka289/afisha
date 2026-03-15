<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venue>
 */
class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Клуб «Конструктивно»',
            'Зал «Пепе Шнель приветствует» (фа)',
            'Бар «67» (сикс севен)',
            'ДК «Пожарник выезжает»',
            'Лофт «Зашло / Не зашло»',
            'Арена «Без базара»',
            'Подвал «По факту»',
            'Сцена «В моменте»',
            'Клуб «Чилл до рассвета»',
            'Зал «Респект пацаны»',
            'Бар «Чекай вайб»',
            'ДК «Рофл и база»',
        ]);
        $city = fake()->randomElement(['Москва', 'Питер', 'Мухосранск', 'Нижние Пупки', 'Бобруйск']);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'city' => $city,
            'address' => fake()->randomElement([
                'ул. Пепе Шнеля, 67',
                'пр. Конструктивно, 1',
                'пер. Пожарника, 13',
                'наб. Зашло, д. 0',
            ]),
            'latitude' => fake()->latitude(55, 56),
            'longitude' => fake()->longitude(37, 38),
            'description' => fake()->randomElement([
                'Конструктивно лучшая площадка. По факту.',
                'Пожарник уже выехал (на сцену). Чекай.',
                'Вайб норм, зайдёшь — не кринж. Без базара.',
            ]),
            'max_capacity' => fake()->randomElement([50, 100, 200, 666, 1488, 3000]),
            'layout_type' => fake()->randomElement(['rectangle', 'arena', 'custom']),
            'layout_config' => ['width' => 800, 'height' => 500],
        ];
    }
}
