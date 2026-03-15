<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Пепе Шнель: тур «Фа на бис»',
            'Конструктивно: вечер акустики',
            'Сикс Севен — 67 городов',
            'Пожарник и Ко: «Сирена в ночи»',
            'Иван Золо: «Я в ахуе» (концерт)',
            'Рофл и база: что зашло в 2025',
            'Чекай вайб: live',
            'Респект и чилл: без базара',
            'Дроп в полночь (реально в 00:00)',
            'Гоу на концерт: по факту зайдёт',
            'Кринж отменён — только база',
            'В моменте: дядя с гитарой',
        ]);
        $start = fake()->dateTimeBetween('+1 week', '+3 months');
        $end = (clone $start)->modify('+3 hours');
        return [
            'venue_id' => Venue::factory(),
            'artist_id' => Artist::factory(),
            'created_by' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(4),
            'subtitle' => fake()->randomElement([
                'Конструктивно. По факту. Без базара.',
                'Чекай — зайдёт. Респект.',
                'Пожарник уже выехал (на сцену).',
            ]),
            'category' => fake()->randomElement(['concert', 'show', 'theater', 'other']),
            'description' => fake()->randomElement([
                'Пепе одобряет. Фа. Респект всем, кто в зале. Чекай вайб.',
                'Сикс севен причин прийти. Конструктивно лучший вечер. Не кринж.',
            ]),
            'start_at' => $start,
            'end_at' => $end,
            'sales_start_at' => now(),
            'sales_end_at' => (clone $start)->modify('-1 day'),
            'status' => fake()->randomElement(['draft', 'published', 'cancelled']),
            'poster_url' => null,
            'max_tickets' => fake()->numberBetween(50, 2000),
            'layout_type' => 'custom',
            'layout_config' => [],
            'meta' => [],
            'latitude' => fake()->latitude(55, 56),
            'longitude' => fake()->longitude(37, 38),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $a) => ['status' => 'published']);
    }
}
