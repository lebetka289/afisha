<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:8',
                'max:30',
                'regex:/[a-zA-Zа-яА-Я]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#^()_\-+=\[\]{}|\\:;"\'<>,.\/~`]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите имя.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не должно быть длиннее 255 символов.',

            'email.required' => 'Введите email.',
            'email.string' => 'Email должен быть строкой.',
            'email.email' => 'Введите корректный email.',
            'email.max' => 'Email не должен быть длиннее 255 символов.',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован.',

            'password.required' => 'Введите пароль.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.max' => 'Пароль не должен быть длиннее 30 символов.',
            'password.regex' => 'Пароль должен содержать буквы, цифры и спецсимволы (@$!%*?& и т.д.).',
        ];
    }
}

