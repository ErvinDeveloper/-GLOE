<?php

namespace App\Repository;

use App\Models\Category as Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends Repository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getParentCategories(): Collection
    {
        return $this->startConditions()->query()
            ->where('parent_id', '=', null)
            ->get();
    }

    public function getBySlug(string $slug)
    {
        return $this->startConditions()->query()->whereSlug($slug)->first();
    }

    public function getWithPagination(): LengthAwarePaginator
    {
        return $this->startConditions()->query()->withCount('posts')->paginate(config('main.per_page'));
    }


}
