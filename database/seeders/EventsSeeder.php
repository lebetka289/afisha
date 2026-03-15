<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Event;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();
        $artists = Artist::all();
        $users = User::all();

        if ($venues->isEmpty() || $artists->isEmpty() || $users->isEmpty()) {
            return;
        }

        $titles = [
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
        ];

        foreach ($titles as $title) {
            Event::factory()->published()->create([
                'title' => $title,
                'venue_id' => $venues->random()->id,
                'artist_id' => $artists->random()->id,
                'created_by' => $users->random()->id,
            ]);
        }

        Event::factory()->count(5)->create([
            'venue_id' => $venues->random()->id,
            'artist_id' => $artists->random()->id,
            'created_by' => $users->random()->id,
        ]);
    }
}
