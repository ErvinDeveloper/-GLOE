<?php

namespace App\Repository;

use App\Models\Theme as Model;

class ThemeRepository extends Repository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

}
