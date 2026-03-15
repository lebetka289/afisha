<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'tickets_payload' => ['required', 'string'],
            'addons_payload' => ['nullable', 'string'],
            'test_mode' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Введите имя покупателя.',
            'customer_name.string' => 'Имя покупателя должно быть строкой.',
            'customer_name.max' => 'Имя покупателя не должно быть длиннее 255 символов.',

            'customer_email.required' => 'Введите email покупателя.',
            'customer_email.email' => 'Укажите корректный email.',

            'customer_phone.string' => 'Телефон должен быть строкой.',
            'customer_phone.max' => 'Телефон не должен быть длиннее 50 символов.',

            'tickets_payload.required' => 'Не переданы выбранные билеты.',
            'tickets_payload.string' => 'Неверный формат данных по билетам.',

            'addons_payload.string' => 'Неверный формат данных по доп. услугам.',

            'test_mode.boolean' => 'Поле тестового режима должно быть булевым значением.',
        ];
    }
}

