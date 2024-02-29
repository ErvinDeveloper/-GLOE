<?php

namespace App\Services;

use App\Http\Requests\Admin\Question\GetWordsChunkRequest;
use App\Http\Requests\Admin\Question\StoreDefaultRequest;
use App\Http\Requests\Admin\Question\StoreRequest;
use App\Http\Requests\Admin\Question\StoreWordsChunkRequest;
use App\Http\Requests\Admin\Question\StoreWordsSubstitute;
use App\Http\Requests\Admin\Question\UpdateDefaultRequest;
use App\Http\Requests\Admin\Question\UpdateRequest;
use App\Http\Requests\Admin\Question\UpdateWordsChunkRequest;
use App\Http\Requests\Admin\Question\UpdateWordsSubstitute;
use App\Models\Option;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class QuestionService
{

    public const ERROR_RESPONSES = [
        'OPTIONS_NOT_PASSED' => 'Не переданы варианты ответа',
        'CORRECT_ANSWER_IS_NOT' => 'Не указан правильный ответ'
    ];

    public function storeDefault(Test $test, StoreDefaultRequest $request): Model|string
    {
        $data = $request->validated();

        $options = json_decode($data['options'], true);
        $options = collect($options);

        $questionType = 'default';

        if (empty($data['options'])) {
            return self::ERROR_RESPONSES['OPTIONS_NOT_PASSED'];
        }

        $isCorrectSelected = $options->filter(function ($option) {
            return $option['isCorrect'] == 1;
        })->first();

        if (empty($isCorrectSelected)) {
            return self::ERROR_RESPONSES['CORRECT_ANSWER_IS_NOT'];
        }

        $question = $test->questions()->create([
            'task' => $data['task'],
            'scores' => $data['scores'],
            'question_type' => $questionType,
            'answer' => ''
        ]);

        $options->map(function ($option) use (&$question) {
            $optionCreated = $question->options()->create([
                'title' => $option['value'],
                'hash' => $option['hash']
            ]);

            if ($option['isCorrect']) {
                $question->update([
                    'answer' => $optionCreated->id
                ]);
            }
        });

        return $question;
    }

    public function updateDefault(
        Question             $question,
        UpdateDefaultRequest $request
    ): true|string
    {
        $data = $request->validated();

        $deletedOptions = json_decode($data['deleted_options'], true);
        $options = json_decode($data['options'], true);
        $options = collect($options);

        if (empty($data['options'])) {
            return self::ERROR_RESPONSES['OPTIONS_NOT_PASSED'];
        }

        $isCorrectSelected = $options->filter(function ($option) {
            return $option['isCorrect'] == 1;
        })->first();

        if (empty($isCorrectSelected)) {
            return self::ERROR_RESPONSES['CORRECT_ANSWER_IS_NOT'];
        }

        Option::query()->whereIn('hash', $deletedOptions)->delete();

        $question->update([
            'task' => $data['task'],
            'scores' => $data['scores']
        ]);

        $options
            ->filter(function ($option) {
                return !empty($option['hash']);
            })
            ->each(function ($option) use ($question) {
                $item = Option::query()->where('hash', $option['hash']);

                $item->update([
                    'title' => $option['value']
                ]);

                if ($option['isCorrect']) {
                    $question->update([
                        'answer' => $item->first()->id
                    ]);
                }
            });

        $options
            ->filter(function ($option) {
                return empty($option['hash']);
            })
            ->each(function ($option) use ($question) {
                $optionCreated = $question->options()->create([
                    'hash' => Str::random(),
                    'title' => $option['value']
                ]);

                if ($option['isCorrect']) {
                    $question->update([
                        'answer' => $optionCreated->id
                    ]);
                }
            });

        return true;
    }

    public function updateWordsChunk(
        Question                $question,
        UpdateWordsChunkRequest $request
    ): true|string
    {
        $data = $request->validated();

        $deletedOptions = json_decode($data['deleted_options'], true);

        $options = json_decode($data['options'], true);
        $options = collect($options);

        if (empty($data['options'])) {
            return self::ERROR_RESPONSES['OPTIONS_NOT_PASSED'];
        }

        $question->update([
            'task' => $data['task'],
            'scores' => $data['scores'],
            'answer' => $data['answer']
        ]);


        Option::query()->whereIn('hash', $deletedOptions)->delete();

        $options
            ->filter(function ($option) {
                return !empty($option['hash']);
            })
            ->each(function ($option) use ($question) {
                $item = Option::query()->where('hash', $option['hash']);

                $item->update([
                    'title' => $option['value']
                ]);
            });

        $options
            ->filter(function ($option) {
                return empty($option['hash']) || !Option::query()->where('hash', $option['hash'])->count();
            })
            ->each(function ($option) use ($question) {
                $question->options()->create([
                    'hash' => Str::random(),
                    'title' => $option['value']
                ]);
            });

        return true;
    }

    public function updateWordsSubstitute(
        Question $question,
        UpdateWordsSubstitute $request
    ): true|string
    {
        $data = $request->validated();

        $options = json_decode($data['options'], true);
        $options = collect($options);

        if (empty($data['options'])) {
            return self::ERROR_RESPONSES['OPTIONS_NOT_PASSED'];
        }

        $question->update([
            'task' => $data['task'],
            'scores' => $data['scores'],
            'answer' => $data['answer']
        ]);

        $option = collect($options)->map(function ($option) {
            return ($option['isInput'] ? '[input]' . $option['value'] . '[/input]' : $option['value']);
        })->implode(' ');

        $question->options()->first()->update([
            'title' => $option
        ]);

        return true;
    }

    public function storeWordsSubstitute(
        Test                 $test,
        StoreWordsSubstitute $request
    ): Model|string
    {
        $data = $request->validated();

        $options = json_decode($data['options'], true);
        $options = collect($options);

        $questionType = 'words_substitute';

        if (empty($data['options'])) {
            return self::ERROR_RESPONSES['OPTIONS_NOT_PASSED'];
        }

        $question = $test->questions()->create([
            'task' => $data['task'],
            'scores' => $data['scores'],
            'question_type' => $questionType,
            'answer' => $data['answer']
        ]);

        $option = collect($options)->map(function ($option) {
            return ($option['isInput'] ? '[input]' . $option['value'] . '[/input]' : $option['value']);
        })->implode(' ');

        $question->options()->create([
            'title' => $option,
            'hash' => Str::random()
        ]);

        return $question;
    }

    public function storeWordsChunk(
        Test                   $test,
        StoreWordsChunkRequest $request
    ): Model|string
    {
        $data = $request->validated();

        $options = json_decode($data['options'], true);
        $options = collect($options);

        $questionType = 'words_chunk';

        if (empty($data['options'])) {
            return self::ERROR_RESPONSES['OPTIONS_NOT_PASSED'];
        }

        $question = $test->questions()->create([
            'task' => $data['task'],
            'scores' => $data['scores'],
            'question_type' => $questionType,
            'answer' => $data['answer']
        ]);

        $options->map(function ($option) use (&$question) {
            $question->options()->create([
                'title' => $option['value'],
                'hash' => $option['hash']
            ]);
        });

        return $question;

    }

    public function delete(Question $question): bool
    {
        try {
            DB::beginTransaction();

            $question->answereds()->delete();
            $question->options()->delete();

            if (File::exists('storage/' . $question->audio)) {
                File::delete('storage/' . $question->audio);
            }

            $question->delete();

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();
            return false;
        }

        return true;
    }

    public function deleteVideo(Question $question): bool
    {
        if (File::exists('storage/' . $question->video)) {
            $isDeleted = File::delete('storage/' . $question->video);
            if ($isDeleted) {
                $question->update([
                    'video' => null
                ]);
                return $isDeleted;
            }
        }

        return false;
    }

    public function getWordsChunkHTML(GetWordsChunkRequest $request)
    {
        $words = $request->validated('words');
        $words = explode(' ', $words);

        $html = '';

        collect($words)->each(function ($word) use (&$html) {
            $html .= view('admin.question.words_chunk', compact('word'))->render();
        });

        return $html;
    }

    public function getWordsSubstituteHTML(GetWordsChunkRequest $request)
    {
        $words = $request->validated('words');
        $words = explode(' ', $words);

        $html = '';

        collect($words)->each(function ($text) use (&$html) {

            $word['title'] = $text;
            $html .= view('admin.question.words_substitute', compact('word'))->render();
        });

        return $html;
    }

    private function uploadFile($file)
    {
        $fileName = time() . '_' . Str::random() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs(date('Y/m'), $fileName);
    }

    private function updateAnswerForDefault($question, array $data): void
    {
        $getCurrentOption = $question->options->where('title', $data['answer'])->first();
        if (!is_null($getCurrentOption)) {
            $question->update([
                'answer' => $getCurrentOption->id
            ]);
        }
    }

    private function getAnswer(array $data): ?string
    {
        $questionType = $data['question_type'];

        if (isset($data['correct_answer'])) {
            return ($questionType === 'default' && isset($data[$questionType][$data['correct_answer']]))
                ? $data[$questionType][$data['correct_answer']] : $data['correct_answer'];
        } elseif (isset($data['words'])) {
            return $data['words'];
        }

        return null;
    }

    private function isEmptyOptions(array $data): bool
    {
        $questionType = $data['question_type'];

        if (!isset($data[$questionType]) || !is_array($data[$questionType])) {
            return true;
        }

        return false;
    }
}
