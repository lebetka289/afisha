<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\EventSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingItem>
 */
class BookingItemFactory extends Factory
{
    protected $model = BookingItem::class;

    public function definition(): array
    {
        $section = EventSection::factory();
        $row = fake()->numberBetween(1, 5);
        $col = fake()->numberBetween(1, 8);
        $label = chr(64 + $row) . $col;
        return [
            'booking_id' => Booking::factory(),
            'event_section_id' => $section,
            'event_seat_id' => null,
            'seat_label' => $label,
            'price' => fake()->randomFloat(2, 500, 10000),
            'meta' => null,
        ];
    }
}
