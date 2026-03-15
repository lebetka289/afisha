<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingsSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::published()->inRandomOrder()->limit(5)->get();
        $users = User::inRandomOrder()->limit(10)->get();

        if ($events->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($events as $event) {
            Booking::factory()->count(2)->create([
                'event_id' => $event->id,
                'user_id' => $users->random()->id,
                'status' => fake()->randomElement(['confirmed', 'pending']),
            ]);
        }
    }
}
