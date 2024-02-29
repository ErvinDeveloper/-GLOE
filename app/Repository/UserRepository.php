<?php

namespace App\Repository;

use App\Http\Requests\Profile\UpdateLevelRequest;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository extends Repository
{
    protected function getModelClass(): string
    {
        return User::class;
    }
    public function myAnswers(Test $test)
    {
        if (Auth::check()) {
            return auth()
                ->user()
                ->answereds()
                ->where('test_id', $test->id)
                ->get();
        }

        return collect();
    }

    public function resetAnswereds()
    {
        return auth()->user()->answereds()->delete();
    }

    public function againAnswered(Question $question)
    {
        return auth()->user()->answereds()->where('question_id', $question->id)->where('correct_answer', 0)->delete();
    }

    public function resetLevel(): true
    {
        auth()->user()->update([
            'level_id' => null,
            'is_level_selected' => false
        ]);

        $this->resetAnswereds();

        return true;
    }

    public function setLevel(UpdateLevelRequest $request): true
    {
        $data = $request->validated();
        auth()->user()->update($data);
        auth()->user()->answereds()->delete();

        return true;
    }

    public function favorites()
    {
        return auth()->user()->favorites()->whereHas('favoriteable')->get();
    }
}
