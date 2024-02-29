<?php

namespace App\Services;

use App\Http\Requests\Admin\Author\StoreRequest;
use App\Http\Requests\Admin\Author\UpdateRequest;
use App\Models\Author;
use Illuminate\Database\Eloquent\Model;

class AuthorService
{
    public function store(StoreRequest $request): Model
    {
        return Author::query()->create($request->validated());
    }

    public function update(UpdateRequest $request, Author $author): bool
    {
        return $author->update($request->validated());
    }

    public function delete(Author $author): ?bool
    {
        return $author->delete();
    }
}
