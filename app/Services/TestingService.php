<?php

namespace App\Services;

use App\Http\Requests\Admin\Test\StoreRequest;
use App\Http\Requests\Admin\Test\UpdateRequest;
use App\Models\Test;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class TestingService
{
    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();
        return Test::query()->create($data);
    }

    public function delete(Test $test): true
    {
        $test->questions()->each(function ($question) {
            $question->answereds()->delete();
            $question->options()->delete();

            if (File::exists('storage/' . $question->audio)) {
                File::delete('storage/' . $question->audio);
            }
        });

        $test->questions()->delete();

        $test->delete();

        return true;
    }

    public function update(Test $test, UpdateRequest $request): bool
    {
        $data = $request->validated();
        return $test->update($data);
    }
}
