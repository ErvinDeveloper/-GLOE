<?php

namespace App\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    abstract protected function getModelClass();

    protected function startConditions()
    {
        return clone $this->model;
    }

    public function getWithPagination(): LengthAwarePaginator
    {
        return $this->startConditions()->query()->orderBy('id', 'desc')->paginate(config('app.per_page'));
    }
}
