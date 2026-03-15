<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $titles = [
            'Пепе Шнель в твоём городе. Фа.',
            'Конструктивно напоминаем: концерт через час',
            'Сикс севен билетов осталось. Не кринж.',
            'Пожарник выехал (на сцену). Чекай.',
            'Зашло. Твой ивент через 15 минут. Без базара.',
            'Иван Золо ждёт. По факту зайдёт.',
            'Респект! Напоминание: концерт сегодня. Чилл.',
            'Твоё место заняли. Шучу. Пока. Гоу покупать.',
        ];
        $bodies = [
            'Конструктивно советуем выйти из дома. По факту зайдёт.',
            'Пепе одобряет твой выбор. Фа. Чекай вайб на концерте.',
            'Пожарник уже в пути (мы про артиста). Без базара, будет огонь.',
        ];
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['reminder', 'promo', 'booking', 'system']),
            'title' => fake()->randomElement($titles),
            'body' => fake()->randomElement($bodies),
            'url' => fake()->optional(0.5)->url(),
            'data' => [],
            'read_at' => fake()->optional(0.4)->dateTimeThisMonth(),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $a) => ['read_at' => null]);
    }
}
