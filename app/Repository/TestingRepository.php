<?php

namespace App\Repository;

use App\Models\Test as Model;
use Illuminate\Database\Eloquent\Collection;

class TestingRepository extends Repository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function all(): Collection
    {
        return $this->startConditions()->all();
    }

}
