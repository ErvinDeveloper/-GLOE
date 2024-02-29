<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'title_lat' => 'nullable',
            'slug' => 'nullable|string',
            'description' => 'nullable',
            'description_lat' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Название',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле ":attribute" обязательно к заполнению',
            'string' => 'Поле ":attribute" должно быть строкой',
        ];
    }
}
