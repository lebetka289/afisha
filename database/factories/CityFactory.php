<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Мухосранск',
            'Нижние Пупки',
            'Бобруйск (туда прилетел Пепе)',
            'Дно',
            'Выселки',
            'Гадюкино',
            'Урюпинск',
            'Пошехонье',
            'Мытищи (не смейся)',
            'Сикс-Севенск',
            'Конструктивск',
            'Чилл-Сити',
            'Базаград',
            'Кринжово',
        ]);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'sort_order' => fake()->numberBetween(0, 999),
        ];
    }
}
