<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventSection>
 */
class EventSectionFactory extends Factory
{
    protected $model = EventSection::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['standard', 'vip', 'meet_greet', 'dancefloor', 'balcony']);
        $name = match ($type) {
            'vip' => fake()->randomElement(['VIP «Пепе одобряет»', 'Ложа «Конструктивно»', 'Респект-зона']),
            'dancefloor' => fake()->randomElement(['Танцпол «Чекай вайб»', 'Зона «Зашло»']),
            'balcony' => 'Балкон «Чилл» (откуда видно, но не слышно)',
            default => fake()->randomElement(['Партер «Без базара»', 'Сектор 67', 'Обычные места «По факту»']),
        };
        $seating = in_array($type, ['dancefloor', 'balcony'], true) ? 'standing' : 'seated';
        $rows = $seating === 'seated' ? fake()->numberBetween(3, 10) : 0;
        $cols = $seating === 'seated' ? fake()->numberBetween(4, 12) : 0;
        $capacity = $seating === 'standing' ? fake()->numberBetween(100, 500) : $rows * $cols;
        return [
            'event_id' => Event::factory(),
            'name' => $name,
            'type' => $type,
            'seating_mode' => $seating,
            'capacity' => $capacity,
            'price' => fake()->randomElement([500, 1500, 3000, 5000, 15000]),
            'rows' => $rows,
            'cols' => $cols,
            'position' => ['x' => 100, 'y' => 100, 'width' => 200, 'height' => 150],
            'color' => fake()->randomElement(['#3b82f6', '#22c55e', '#f97316', '#ef4444']),
            'sort_order' => fake()->numberBetween(0, 10),
            'meta' => null,
        ];
    }
}
