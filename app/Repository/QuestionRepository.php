<?php

namespace App\Repository;

use App\Actions\AudioPlayer;
use App\Actions\EditorJS;
use App\Actions\VideoPlayer;
use App\Actions\YoutubeIframe;
use App\Models\Question;
use App\Models\Test;

class QuestionRepository extends Repository
{
    public function __construct(
        private readonly YoutubeIframe $youtubeIframe,
        private readonly AudioPlayer   $audioPlayer,
        private readonly VideoPlayer   $videoPlayer,
        private readonly EditorJS $editorJS
    )
    {
        parent::__construct();
    }

    protected function getModelClass(): string
    {
        return Question::class;
    }

    public function get(Test $test, Question $question): Question
    {
        $question->youtubeIframe = $this->youtubeIframe->handler($question);
        $question->audioPlayer = $this->audioPlayer->handler($question);
        $question->videoPlayer = $this->videoPlayer->handler($question);
        $question->task = $this->editorJS->set($question->task)->render();
        $question->doesQuestionRelateToCurrentTesting($test);
        return $question;
    }

    public function getForEditing(int $questionId): false|array
    {
        $question = $this->startConditions()->query()->find($questionId);
        if (empty($question)) {
            return false;
        }

        $words_substitute = [];

        if ($question->question_type === 'words_substitute') {
            $option = $question->options()->first();
            $words_substitute = collect(explode(' ', $option->title))->map(function ($word) {

                $isFieldWrap = false;

                if (preg_match('~\[input\](.*)\[\/input\]~is', $word, $matches)) {
                    $isFieldWrap = true;
                    $word = $matches[1];
                }

                return [
                    'title' => $word,
                    'isFieldWrap' => $isFieldWrap
                ];
            });
        }

        return [
            'question' => $question,
            'words_substitute' => $words_substitute
        ];
    }
}
