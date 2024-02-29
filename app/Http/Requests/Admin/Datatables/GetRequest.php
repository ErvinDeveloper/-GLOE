<?php

namespace App\Http\Requests\Admin\Datatables;

use Illuminate\Foundation\Http\FormRequest;

class GetRequest extends FormRequest
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
            'draw' => 'required|numeric',
            'columns.*.data' => 'required|string',
            'columns.*.name' => 'nullable',
            'columns.*.searchable' => 'required|string',
            'columns.*.orderable' => 'required|string',
            'columns.*.search.value' => 'nullable',
            'columns.*.search.regex' => 'required|string',
            'order.*.column' => 'required|numeric',
            'order.*.dir' => 'required|string',
            'start' => 'required|numeric',
            'length' => 'required|numeric',
            'search.value' => 'nullable',
            'search.regex' => 'required|string',
            'categoryId' => 'nullable|exists:categories,id'
        ];
    }
}
