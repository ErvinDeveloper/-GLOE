<?php

namespace App\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public mixed $image;

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
            'slug' => 'nullable|string',
            'level_id' => 'nullable|numeric',
            'category_id' => 'required|numeric',
            'body' => 'required|string',
            'body_lat' => 'nullable|string',
            'image' => 'nullable',
            'theme_id' => 'nullable|numeric',
            'is_published' => 'boolean',
            'is_video' => 'boolean',
            'excerpt' => 'nullable|string',
            'excerpt_lat' => 'nullable|string',
            'files' => 'required',
            'author_id' => 'required'
            //'image' => 'file|mimes:jpeg,jpg,png,gif'
        ];
    }
}
