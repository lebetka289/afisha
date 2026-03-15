<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\ArtistAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArtistAlbum>
 */
class ArtistAlbumFactory extends Factory
{
    protected $model = ArtistAlbum::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Конструктивно (альбом)',
            '67 треков (сикс севен)',
            'Пепе одобряет. Фа.',
            'Зашло / Не зашло',
            'Чекай вайб (EP)',
            'Респект пацаны (сборник)',
            'Чилл до рассвета',
            'Без базара (по факту)',
        ]);
        return [
            'artist_id' => Artist::factory(),
            'title' => $title,
            'year' => fake()->numberBetween(2015, 2025),
            'type' => fake()->randomElement([ArtistAlbum::TYPE_ALBUM, ArtistAlbum::TYPE_SINGLE, ArtistAlbum::TYPE_EP]),
            'cover_url' => null,
            'link' => fake()->optional(0.6)->url(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
