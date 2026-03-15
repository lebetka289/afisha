<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artist>
 */
class ArtistFactory extends Factory
{
    protected $model = Artist::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
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
            'Агрись и слушай',
        ]);
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . \Illuminate\Support\Str::random(4),
            'description' => fake()->randomElement([
                'Конструктивно играем. По факту зашло. Без базара.',
                'Пепе одобряет. Фа. Респект всем, кто в зале.',
                'Пожарник выехал — мы на сцене. Чекай вайб.',
                '67 треков в сет-листе. Сикс севен. Не кринж.',
            ]),
            'photo' => null,
            'links' => ['bandcamp' => 'https://example.com', 'vk' => 'https://vk.com/example'],
        ];
    }
}
