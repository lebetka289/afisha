<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CabinetUpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:51200'],
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
            'email.unique' => 'Пользователь с таким email уже существует.',

            'password.confirmed' => 'Пароли не совпадают.',

            'avatar.file' => 'Аватар должен быть файлом.',
            'avatar.mimes' => 'Недопустимый формат файла для аватара.',
            'avatar.max' => 'Размер файла аватара не должен превышать 50 МБ.',
        ];
    }
}

