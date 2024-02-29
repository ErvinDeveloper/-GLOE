<?php

namespace App\Services;

use App\Http\Requests\Admin\Category\StoreRequest;
use App\Http\Requests\Admin\Category\UpdateRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryService
{
    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        return Category::query()->create($data);
    }

    public function update(Category $category, UpdateRequest $request): bool
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        return $category->update($data);
    }

    public function delete(Category $category): ?bool
    {
        return $category->delete();
    }
}
