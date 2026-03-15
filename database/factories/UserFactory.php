<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Пепе Шнель',
            'Иван Золо',
            'Конструктивно Мемович',
            'Пожарник Вызовович',
            'Чекай Вайбов',
            'Рофл Базаров',
            'Чилл Респектов',
            'Сикс Севен',
            'Гоу НаКонцерт',
            'Кринж Отменён',
            'Дроп ВПолночь',
            'Вайб Норм',
        ]) ?: fake()->name();
        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(['user', 'organizer', 'artist', 'admin']),
            'artist_id' => null,
            'city_id' => City::factory(),
            'is_admin' => false,
            'avatar' => null,
            'notify_email' => fake()->boolean(70),
            'notify_push' => fake()->boolean(50),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    public function organizer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'organizer']);
    }

    public function artist(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'artist']);
    }
}
