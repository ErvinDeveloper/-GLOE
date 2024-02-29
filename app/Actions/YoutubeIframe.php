<?php

namespace App\Actions;

use App\Models\Question;

class YoutubeIframe
{
    public function handler(Question $question): ?string
    {
        if (empty($question->youtube_url)) {
            return null;
        }

        $data = parse_url($question->youtube_url);

        if (empty($data['host']) && $data['host'] !== 'www.youtube.com') {
            return null;
        }

        parse_str($data['query'], $params);

        return '<iframe src="https://www.youtube.com/embed/'.$params['v'].'?controls=0" frameborder="0" width="100%" height="315"></iframe>';
    }
}
