<?php

namespace Database\Seeders;

use App\Models\Artist;
use Illuminate\Database\Seeder;

class ArtistsSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Пепе Шнель & The Fa',
            'Иван Золо',
            'Сикс Севен feat. Конструктивно',
            'Пожарник на басу',
            'Дядя с гитарой «В моменте»',
            'Краш души оркестр',
            'Вайб Чекер',
            'Рофл-коллектив',
            'База и Кринж',
            'Чекай Звук',
            'Респект Пацаны',
            'Чилл до рассвета',
            'Гоу на сцену',
            'Дроп в полночь',
        ];

        $descriptions = [
            'Конструктивно играем. По факту зашло. Без базара.',
            'Пепе одобряет. Фа. Респект всем, кто в зале.',
            'Пожарник выехал — мы на сцене. Чекай вайб.',
            '67 треков в сет-листе. Сикс севен. Не кринж.',
        ];

        foreach ($names as $name) {
            Artist::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $descriptions[array_rand($descriptions)],
                    'photo' => null,
                    'links' => [],
                ]
            );
        }

        Artist::factory()->count(5)->create();
    }
}
