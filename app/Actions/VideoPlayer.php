<?php

namespace App\Actions;

use App\Models\Question;

class VideoPlayer
{
    public function handler(Question $question): ?string
    {
        if (empty($question->video)) {
            return null;
        }

        return '<div><video src="'.asset('storage/' . $question->video).'" controls></video></div>';
    }
}
