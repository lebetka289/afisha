<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\EventAddon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingAddon>
 */
class BookingAddonFactory extends Factory
{
    protected $model = BookingAddon::class;

    public function definition(): array
    {
        $addon = EventAddon::factory();
        $qty = fake()->numberBetween(1, 3);
        $unitPrice = fake()->randomElement([300, 500, 1000, 2500]);
        return [
            'booking_id' => Booking::factory(),
            'event_addon_id' => $addon,
            'quantity' => $qty,
            'price' => $unitPrice * $qty,
        ];
    }
}
