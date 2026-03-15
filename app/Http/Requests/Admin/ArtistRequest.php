<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ArtistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $artistId = $this->route('artist')?->id ?? null;

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
            'albums' => ['nullable', 'array'],
            'albums.*.id' => ['nullable', 'integer', 'exists:artist_albums,id'],
            'albums.*.title' => ['required_with:albums.*', 'string', 'max:255'],
            'albums.*.year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'albums.*.type' => ['nullable', 'string', 'in:album,single,ep'],
            'albums.*.link' => ['nullable', 'string', 'max:500'],
            'albums.*.cover_url' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите имя исполнителя.',
            'name.string' => 'Имя исполнителя должно быть строкой.',
            'name.max' => 'Имя исполнителя не должно быть длиннее 255 символов.',

            'slug.string' => 'Слаг должен быть строкой.',
            'slug.max' => 'Слаг не должен быть длиннее 255 символов.',
            'slug.unique' => 'Такой слаг уже используется для другого исполнителя.',

            'description.string' => 'Описание должно быть строкой.',

            'photo.file' => 'Фотография должна быть файлом.',
            'photo.mimes' => 'Недопустимый формат файла для фото.',
            'photo.max' => 'Размер файла фото не должен превышать 50 МБ.',

            'links_json.string' => 'Ссылки должны быть переданы в текстовом формате.',
        ];
    }
}

