<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id ?? null;

        $uniqueSlugRule = 'unique:events,slug';
        if ($eventId) {
            $uniqueSlugRule .= ',' . $eventId;
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlugRule],
            'venue_id' => ['nullable', 'integer', 'exists:venues,id'],
            'artist_id' => ['nullable', 'integer', 'exists:artists,id'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'in:concert,theater,show,standup'],
            'description' => ['nullable', 'string'],
            'poster_url' => ['nullable', 'string', 'max:2048'],
            'poster_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:51200'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'sales_start_at' => ['nullable', 'date'],
            'sales_end_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,published,archived'],
            'max_tickets' => ['nullable', 'integer', 'min:0'],
            'layout_type' => ['required', 'string', 'max:50'],
            'layout_config' => ['nullable'],
            'meta' => ['nullable'],
            'sections_payload' => ['required'],
            'addons_payload' => ['nullable'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];

        if ($this->filled('start_at')) {
            $rules['end_at'][] = 'after_or_equal:start_at';
        }
        if ($this->filled('sales_start_at')) {
            $rules['sales_end_at'][] = 'after_or_equal:sales_start_at';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Введите название события.',
            'title.string' => 'Название события должно быть строкой.',
            'title.max' => 'Название события не должно быть длиннее 255 символов.',

            'slug.string' => 'Слаг должен быть строкой.',
            'slug.max' => 'Слаг не должен быть длиннее 255 символов.',
            'slug.unique' => 'Такой слаг уже используется для другого события.',

            'venue_id.integer' => 'Некорректный идентификатор площадки.',
            'venue_id.exists' => 'Выбранная площадка не найдена.',

            'artist_id.integer' => 'Некорректный идентификатор исполнителя.',
            'artist_id.exists' => 'Выбранный исполнитель не найден.',

            'subtitle.string' => 'Подзаголовок должен быть строкой.',
            'subtitle.max' => 'Подзаголовок не должен быть длиннее 255 символов.',

            'category.required' => 'Укажите категорию события.',
            'category.in' => 'Выбрана некорректная категория события.',

            'description.string' => 'Описание должно быть строкой.',

            'poster_url.string' => 'Ссылка на постер должна быть строкой.',
            'poster_url.max' => 'Ссылка на постер не должна быть длиннее 2048 символов.',

            'poster_upload.file' => 'Постер должен быть файлом.',
            'poster_upload.mimes' => 'Недопустимый формат файла для постера.',
            'poster_upload.max' => 'Размер файла постера не должен превышать 50 МБ.',

            'start_at.date' => 'Дата начала указана в неверном формате.',
            'end_at.date' => 'Дата окончания указана в неверном формате.',
            'end_at.after_or_equal' => 'Дата окончания не может быть раньше даты начала.',

            'sales_start_at.date' => 'Дата начала продаж указана в неверном формате.',
            'sales_end_at.date' => 'Дата окончания продаж указана в неверном формате.',
            'sales_end_at.after_or_equal' => 'Дата окончания продаж не может быть раньше даты начала продаж.',

            'status.required' => 'Укажите статус события.',
            'status.in' => 'Указан некорректный статус события.',

            'max_tickets.integer' => 'Максимальное количество билетов должно быть числом.',
            'max_tickets.min' => 'Максимальное количество билетов не может быть отрицательным.',

            'layout_type.required' => 'Укажите тип схемы зала.',
            'layout_type.string' => 'Тип схемы зала должен быть строкой.',
            'layout_type.max' => 'Тип схемы зала не должен быть длиннее 50 символов.',

            'sections_payload.required' => 'Необходимо добавить хотя бы одну зону/сектор.',
        ];
    }
}

