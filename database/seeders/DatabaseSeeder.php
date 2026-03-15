<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSeat;
use App\Models\EventSection;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(CitiesSeeder::class);

        $venue = Venue::firstOrCreate(
            ['slug' => 'aurora-hall'],
            [
            'name' => 'Aurora Hall',
            'city' => 'Москва',
            'address' => 'Ул. Светлая, 12',
            'description' => 'Многофункциональная площадка для концертов и шоу',
            'max_capacity' => 3500,
            'layout_type' => 'arena',
            'layout_config' => [
                'width' => 900,
                'height' => 600,
                'shape' => 'rounded-rect',
            ],
            ]
        );

        $event = Event::firstOrCreate(
            ['slug' => 'neon-lights-tour'],
            [
            'venue_id' => $venue->id,
            'title' => 'Neon Lights Tour',
            'slug' => 'neon-lights-tour',
            'subtitle' => 'Большое шоу света и музыки',
            'description' => 'Иммерсивное шоу с живым вокалом, световыми инсталляциями и танцевальными постановками.',
            'start_at' => now()->addDays(20)->setTime(20, 0),
            'end_at' => now()->addDays(20)->setTime(23, 0),
            'sales_start_at' => now()->subDays(5),
            'sales_end_at' => now()->addDays(19),
            'status' => 'published',
            'poster_url' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?auto=format&fit=crop&w=1000&q=80',
            'max_tickets' => 2000,
            'layout_type' => 'custom',
            'layout_config' => [
                'stage' => ['x' => 350, 'y' => 40, 'width' => 200, 'height' => 60],
            ],
            ]
        );

        $vip = EventSection::firstOrCreate(
            ['event_id' => $event->id, 'name' => 'VIP Сцена'],
            [
            'event_id' => $event->id,
            'name' => 'VIP Сцена',
            'type' => 'vip',
            'seating_mode' => 'seated',
            'capacity' => 24,
            'price' => 15000,
            'rows' => 4,
            'cols' => 6,
            'color' => '#f97316',
            'position' => ['x' => 320, 'y' => 140, 'width' => 260, 'height' => 140],
            'sort_order' => 1,
            ]
        );

        $seats = [];
        for ($row = 1; $row <= $vip->rows; $row++) {
            for ($col = 1; $col <= $vip->cols; $col++) {
                $seats[] = [
                    'event_section_id' => $vip->id,
                    'label' => chr(64 + $row) . $col,
                    'row_number' => $row,
                    'col_number' => $col,
                    'status' => 'available',
                    'price' => $vip->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($vip->wasRecentlyCreated) {
            EventSeat::insert($seats);
        }

        EventSection::firstOrCreate(
            ['event_id' => $event->id, 'name' => 'Танцпол'],
            [
            'event_id' => $event->id,
            'name' => 'Танцпол',
            'type' => 'dancefloor',
            'seating_mode' => 'standing',
            'capacity' => 800,
            'price' => 4500,
            'color' => '#22c55e',
            'position' => ['x' => 160, 'y' => 320, 'width' => 560, 'height' => 200],
            'sort_order' => 2,
            ]
        );

        EventSection::firstOrCreate(
            ['event_id' => $event->id, 'name' => 'Балкон Relax'],
            [
            'event_id' => $event->id,
            'name' => 'Балкон Relax',
            'type' => 'balcony',
            'seating_mode' => 'standing',
            'capacity' => 400,
            'price' => 6500,
            'color' => '#0ea5e9',
            'position' => ['x' => 160, 'y' => 80, 'width' => 150, 'height' => 160],
            'sort_order' => 3,
            ]
        );

        $venue2 = Venue::firstOrCreate(
            ['slug' => 'maksimilians'],
            [
            'name' => 'Максимилианс',
            'slug' => 'maksimilians',
            'city' => 'Набережные Челны',
            'address' => 'пр. Мира, 1',
            'description' => 'Концертная площадка',
            'max_capacity' => 800,
            'layout_type' => 'rectangle',
            ]
        );

        Event::firstOrCreate(
            ['slug' => 'neverlove-concert'],
            [
            'venue_id' => $venue2->id,
            'title' => 'Концерт Neverlove',
            'slug' => 'neverlove-concert',
            'subtitle' => 'Живая музыка',
            'description' => 'Концерт группы Neverlove.',
            'start_at' => now()->addDays(5)->setTime(19, 0),
            'end_at' => now()->addDays(5)->setTime(22, 0),
            'sales_start_at' => now(),
            'sales_end_at' => now()->addDays(4),
            'status' => 'published',
            'poster_url' => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1000&q=80',
            'max_tickets' => 500,
            'layout_type' => 'custom',
            ]
        );

        Event::firstOrCreate(
            ['slug' => 'vals-boston'],
            [
            'venue_id' => $venue->id,
            'title' => 'Мюзикл «Вальс-бостон»',
            'slug' => 'vals-boston',
            'subtitle' => 'Москвич',
            'description' => 'Мюзикл в театре Москвич.',
            'start_at' => now()->addDays(2)->setTime(19, 0),
            'end_at' => now()->addDays(2)->setTime(21, 30),
            'sales_start_at' => now(),
            'sales_end_at' => now()->addDays(1),
            'status' => 'published',
            'poster_url' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1000&q=80',
            'max_tickets' => 600,
            'layout_type' => 'custom',
            ]
        );
    }
}
