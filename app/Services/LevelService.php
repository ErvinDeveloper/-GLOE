<?php

namespace App\Services;

use App\Http\Requests\Admin\Level\StoreRequest;
use App\Http\Requests\Admin\Level\UpdateRequest;
use App\Models\Level;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LevelService
{
    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        return Level::query()->create($data);
    }

    public function update(UpdateRequest $request, Level $level): bool
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        return $level->update($data);
    }
}
