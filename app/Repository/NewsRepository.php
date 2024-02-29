<?php

namespace App\Repository;

use App\Models\News as Model;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository extends Repository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getAllNews(): Collection
    {
        return $this->startConditions()->query()->orderBy('id', 'desc')->get();
    }

}
