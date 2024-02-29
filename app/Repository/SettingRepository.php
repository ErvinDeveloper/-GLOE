<?php

namespace App\Repository;

use App\Models\Setting as Model;

class SettingRepository extends Repository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function all(): object
    {
        return $this->startConditions()->query()->first();
    }

}
