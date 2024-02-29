<?php

namespace App\Actions;

use App\Models\Question;

class AudioPlayer
{
    public function handler(Question $question): ?string
    {
        if (empty($question->audio)) {
            return null;
        }

        return '<div><audio src="'.asset('storage/' . $question->audio).'" controls></audio></div>';
    }
}
