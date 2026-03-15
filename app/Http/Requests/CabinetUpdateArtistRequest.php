<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CabinetUpdateArtistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isArtist() && $this->user()->artist_id;
    }

    public function rules(): array
    {
        $artist = $this->user()->artist;
        $artistId = $artist?->id;

        $uniqueSlugRule = 'unique:artists,slug';
        if ($artistId) {
            $uniqueSlugRule .= ',' . $artistId;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlugRule],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:51200'],
            'links_json' => ['nullable', 'string'],
        ];
    }
}
