<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $tickets = fake()->numberBetween(1, 5);
        $amount = fake()->randomFloat(2, 1000, 50000);
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'reference' => strtoupper(\Illuminate\Support\Str::random(8)),
            'customer_name' => fake()->randomElement([
                'Иван Золо',
                'Пепе Шнель',
                'Конструктивно Купилов',
                'Чекай Вайбов',
                'Респект Пацанов',
            ]) ?: fake()->name(),
            'customer_email' => fake()->unique()->safeEmail(),
            'customer_phone' => fake()->optional(0.8)->phoneNumber(),
            'tickets_count' => $tickets,
            'total_amount' => $amount,
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'refunded']),
            'payment_method' => fake()->randomElement(['card', 'sbp', 'cash']),
            'booked_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'notes' => fake()->optional(0.3)->randomElement([
                'Конструктивно приду. По факту. Без базара.',
                'Буду в мерче «Зашло». Чекай вайб.',
                'Пепе одобряет. Фа. Респект.',
            ]),
            'meta' => [],
        ];
    }
}
