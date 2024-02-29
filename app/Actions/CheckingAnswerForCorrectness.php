<?php

namespace App\Actions;

use App\Http\Requests\Testing\ReplyRequest;
use App\Models\Level;
use App\Models\Question;
use App\Models\Test;
use App\Traits\Response;
use Illuminate\Support\Facades\Auth;

class CheckingAnswerForCorrectness
{
    use Response;

    private function updateLevel(): void
    {
        $answereds = auth()->user()->answereds()->with('question:id,scores')->get();
        $scores = 0;

        $answereds
            ->filter(function ($answered) {
                return $answered->correct_answer === 1;
            })
            ->each(function ($answered) use (&$scores) {
                $scores += $answered->question->scores;
            });

        $level = Level::query()
            ->where('scores_min', '<=', $scores)
            ->where('scores_max', '>=', $scores)
            ->orderBy('id', 'desc')
            ->first();

        if ($level) {
            auth()->user()->update([
                'level_id' => $level->id
            ]);
        }
    }

    public function getNextQuestionLink(Test $test): ?string
    {
        $answerdIds = auth()->user()->answereds()->where('correct_answer', 1)->get()->pluck('question_id')->toArray();

        $question = $test->questions()->whereNotIn('id', $answerdIds)->first();

        if ($question) {
            return route('testing.show', [$test, $question]);
        }

        return null;
    }

    public function handler(Test $test, Question $question, ReplyRequest $request)
    {
        $data = $request->validated();

        switch ($question->question_type) {

            case 'default':
                if ($question->answer === $data['reply']) {
                    if (Auth::check()) {
                        if (!auth()->user()->answereds()->where('test_id', $test->id)->where('question_id', $question->id)->exists()) {
                            \auth()->user()->answereds()->create([
                                'test_id' => $test->id,
                                'question_id' => $question->id,
                                'correct_answer' => true
                            ]);

                            $this->updateLevel();
                        }

                    }
                    return true;
                }
                break;

            case 'words_chunk':
                if (mb_strtolower($question->answer) === mb_strtolower($data['reply'])) {
                    if (Auth::check()) {
                        if (!auth()->user()->answereds()->where('test_id', $test->id)->where('question_id', $question->id)->exists()) {
                            \auth()->user()->answereds()->create([
                                'test_id' => $test->id,
                                'question_id' => $question->id,
                                'correct_answer' => true
                            ]);

                            $this->updateLevel();
                        }
                    }
                    return true;
                }
                break;

            case 'words_substitute':
                $option = $question->options()->first();

                $containsEmpty = count($data['reply']) != count(array_filter($data['reply']));

                if ($containsEmpty === true) {
                    return $this->responseError(['message' => 'Заполните все поля']);
                }


                $pattern = [];
                for ($i = 1; $i <= count($data['reply']); $i++) {
                    $pattern[] = '#\[input\](.*?)\[/input\]#i';
                }
                $task = preg_replace($pattern, $data['reply'], $option->title, 1);

                $task = mb_strtolower($task);
                $answer = mb_strtolower($question->answer);

                if ($task === $answer) {
                    if (Auth::check()) {
                        if (!auth()->user()->answereds()->where('test_id', $test->id)->where('question_id', $question->id)->exists()) {
                            \auth()->user()->answereds()->create([
                                'test_id' => $test->id,
                                'question_id' => $question->id,
                                'correct_answer' => true
                            ]);

                            $this->updateLevel();
                        }
                    }
                    return true;
                }
        }

        if (Auth::check()) {
            if (!auth()->user()->answereds()->where('test_id', $test->id)->where('question_id', $question->id)->exists()) {
                \auth()->user()->answereds()->create([
                    'test_id' => $test->id,
                    'question_id' => $question->id,
                    'correct_answer' => false,
                ]);

                $this->updateLevel();
            }
        }

        return $this->responseError(['message' => 'Можно лучше! Попробуйте ещё раз']);
    }
}
