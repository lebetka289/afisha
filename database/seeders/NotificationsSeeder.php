<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();
        if (empty($userIds)) {
            return;
        }

        $titles = [
            'Пепе Шнель в твоём городе. Фа.',
            'Конструктивно напоминаем: концерт через час',
            'Сикс севен билетов осталось. Не кринж.',
            'Пожарник выехал (на сцену). Чекай.',
            'Зашло. Твой ивент через 15 минут. Без базара.',
            'Иван Золо ждёт. По факту зайдёт.',
            'Респект! Концерт сегодня. Чилл.',
            'Твоё место заняли. Шучу. Пока. Гоу покупать.',
        ];

        $bodies = [
            'Конструктивно советуем выйти из дома. По факту зайдёт.',
            'Пепе одобряет твой выбор. Фа. Чекай вайб на концерте.',
            'Пожарник уже в пути (мы про артиста). Без базара, будет огонь.',
        ];

        for ($i = 0; $i < 25; $i++) {
            Notification::factory()->create([
                'user_id' => $userIds[array_rand($userIds)],
                'title' => $titles[array_rand($titles)],
                'body' => $bodies[array_rand($bodies)],
                'read_at' => fake()->optional(0.5)->dateTimeThisMonth(),
            ]);
        }
    }
}
