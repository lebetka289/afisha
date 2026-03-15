<?php

namespace Database\Factories;

use App\Models\EventSeat;
use App\Models\EventSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventSeat>
 */
class EventSeatFactory extends Factory
{
    protected $model = EventSeat::class;

    public function definition(): array
    {
        $row = fake()->numberBetween(1, 10);
        $col = fake()->numberBetween(1, 12);
        return [
            'event_section_id' => EventSection::factory(),
            'label' => chr(64 + $row) . $col,
            'row_number' => $row,
            'col_number' => $col,
            'status' => fake()->randomElement(['available', 'sold', 'reserved', 'blocked']),
            'price' => fake()->randomFloat(2, 500, 10000),
            'position' => null,
            'meta' => null,
        ];
    }
}
