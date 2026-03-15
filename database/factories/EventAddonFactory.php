<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventAddon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventAddon>
 */
class EventAddonFactory extends Factory
{
    protected $model = EventAddon::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Фото с Пепе Шнелем (фа в подарок)',
            'Мерч «Конструктивно был там»',
            'Стакан воды за 67₽ (сикс севен)',
            'Встреча «Пожарник жмёт руку»',
            'Браслет «Чекай вайб»',
            'Футболка «Зашло / Не зашло»',
            'Значок «Респект пацаны»',
            'Слип «Чилл до рассвета»',
        ]);
        return [
            'event_id' => Event::factory(),
            'name' => $name,
            'price' => fake()->randomElement([67, 500, 1000, 2500, 5000]),
            'description' => fake()->optional(0.7)->randomElement([
                'Конструктивно зайдёт. Без базара.',
                'Пепе одобряет. По факту must have.',
            ]),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
