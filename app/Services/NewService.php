<?php

namespace App\Services;

use App\Http\Requests\Admin\News\StoreRequest;
use App\Http\Requests\Admin\News\UpdateRequest;
use App\Models\News;

class NewService
{
    public function store(StoreRequest $request)
    {
        return auth()->user()->news()->create($request->validated());
    }

    public function update(News $new, UpdateRequest $request): bool
    {
        return $new->update($request->validated());
    }

    public function delete(News $new): ?bool
    {
        return $new->delete();
    }
}
