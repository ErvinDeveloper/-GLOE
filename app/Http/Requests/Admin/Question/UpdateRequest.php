<?php

namespace App\Http\Requests\Admin\Question;

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
            'task' => 'required|string',
            'words' => 'nullable|string',
            'words_chunk' => 'nullable|array',
            'question_type' => 'required|string',
            'default' => 'nullable|array',
            'correct_answer' => 'nullable',
            'words_substitute' => 'nullable',
            'wrap_in_field' => 'nullable|array',
            'youtube_url' => 'nullable',
            'words_substitute_fulltext' => 'nullable',

            'audio' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            //'video' => 'nullable|file|mimetypes:video/mp4',
            'video' => 'nullable',
            'scores' => 'required|numeric',
        ];
    }
}
