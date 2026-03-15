<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventView>
 */
class EventViewFactory extends Factory
{
    protected $model = EventView::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'viewed_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
        ];
    }
}
