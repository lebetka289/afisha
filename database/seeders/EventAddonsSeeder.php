<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventAddon;
use Illuminate\Database\Seeder;

class EventAddonsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['name' => 'Фото с Пепе Шнелем (фа в подарок)', 'price' => 2500],
            ['name' => 'Мерч «Конструктивно был там»', 'price' => 1500],
            ['name' => 'Стакан воды за 67₽ (сикс севен)', 'price' => 67],
            ['name' => 'Встреча «Пожарник жмёт руку»', 'price' => 500],
            ['name' => 'Футболка «Зашло / Не зашло»', 'price' => 1200],
        ];

        Event::published()->inRandomOrder()->limit(8)->get()->each(function (Event $event) use ($addons) {
            $pick = $addons[array_rand($addons)];
            EventAddon::firstOrCreate(
                ['event_id' => $event->id, 'name' => $pick['name']],
                [
                    'price' => $pick['price'],
                    'description' => 'Конструктивно зайдёт. Без базара. По факту.',
                    'sort_order' => 0,
                ]
            );
        });
    }
}
