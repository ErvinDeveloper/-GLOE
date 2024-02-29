<?php

namespace App\Repository;

use App\Models\Author as Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AuthorRepository extends Repository
{

    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function findByName(string $name, int $limit = 10): Collection
    {
        return $this->startConditions()->query()
            ->select('id', 'name')
            ->where('name', 'LIKE', '%'.$name.'%')
            ->orWhere('name_lat', 'LIKE', '%'.$name.'%')
            ->limit($limit)->get();
    }

    public function getWithPagination(): LengthAwarePaginator
    {
        return $this->startConditions()->query()
            ->orderBy('id', 'desc')
            ->withCount('posts')
            ->paginate(config('app.per_page'));
    }
}
