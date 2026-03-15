<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class VenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $venueId = $this->route('venue')?->id ?? null;

        $uniqueSlugRule = 'unique:venues,slug';
        if ($venueId) {
            $uniqueSlugRule .= ',' . $venueId;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlugRule],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_capacity' => ['nullable', 'integer', 'min:0'],
            'layout_type' => ['required', 'string', 'max:50'],
            'layout_config' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название площадки.',
            'name.string' => 'Название площадки должно быть строкой.',
            'name.max' => 'Название площадки не должно быть длиннее 255 символов.',

            'slug.string' => 'Слаг должен быть строкой.',
            'slug.max' => 'Слаг не должен быть длиннее 255 символов.',
            'slug.unique' => 'Такой слаг уже используется для другой площадки.',

            'city.string' => 'Город должен быть строкой.',
            'city.max' => 'Город не должен быть длиннее 255 символов.',

            'address.string' => 'Адрес должен быть строкой.',
            'address.max' => 'Адрес не должен быть длиннее 255 символов.',

            'description.string' => 'Описание должно быть строкой.',

            'max_capacity.integer' => 'Вместимость должна быть числом.',
            'max_capacity.min' => 'Вместимость не может быть отрицательной.',

            'layout_type.required' => 'Укажите тип схемы зала.',
            'layout_type.string' => 'Тип схемы зала должен быть строкой.',
            'layout_type.max' => 'Тип схемы зала не должен быть длиннее 50 символов.',
        ];
    }
}

