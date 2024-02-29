<?php

namespace App\Http\Requests\Admin\Level;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'title_lat' => 'nullable|string',
            'scores_min' => 'required|numeric',
            'scores_max' => 'required|numeric|gt:scores_min',
            'color' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Заголовок',
            'scores_min' => 'Минимальная планка по баллам',
            'scores_max' => 'Максимальная планка по баллам',
            'color' => 'Цвет'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
            'string' => 'Поле ":attribute" должно быть строкой',
            'numeric' => 'Поле ":attribute" должно быть числом',
            'gt' => 'Поле ":attribute" должно быть больше :value'
        ];
    }
}
