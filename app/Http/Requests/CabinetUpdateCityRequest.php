<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CabinetUpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'city_id.required' => 'Выберите город.',
            'city_id.integer' => 'Некорректный идентификатор города.',
            'city_id.exists' => 'Выбранный город не найден.',
        ];
    }
}

