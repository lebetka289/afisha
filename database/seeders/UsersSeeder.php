<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $cityIds = City::pluck('id')->toArray();
        if (empty($cityIds)) {
            return;
        }

        User::firstOrCreate(
            ['email' => 'admin@afisha.test'],
            [
                'name' => 'Пепе Шнель (админ). Фа.',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_admin' => true,
                'city_id' => $cityIds[array_rand($cityIds)],
                'notify_email' => true,
                'notify_push' => false,
            ]
        );

        User::firstOrCreate(
            ['email' => 'organizer@afisha.test'],
            [
                'name' => 'Организатор «Конструктивно»',
                'password' => Hash::make('password'),
                'role' => 'organizer',
                'city_id' => $cityIds[array_rand($cityIds)],
                'notify_email' => true,
                'notify_push' => true,
            ]
        );

        $funnyNames = [
            'Пепе Шнель',
            'Иван Золо',
            'Конструктивно Мемович',
            'Пожарник Вызовович',
            'Чекай Вайбов',
            'Рофл Базаров',
            'Чилл Респектов',
            'Сикс Севен',
        ];

        foreach ($funnyNames as $name) {
            User::factory()->create([
                'name' => $name,
                'city_id' => $cityIds[array_rand($cityIds)],
            ]);
        }

        User::factory()->count(7)->create(['city_id' => $cityIds[array_rand($cityIds)]]);
    }
}
