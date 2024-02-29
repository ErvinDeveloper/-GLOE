<?php

namespace App\Repository;

use App\Models\Level as Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LevelRepository extends Repository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function dashboardGetViewStatistics(): array
    {
        $levels = $this->startConditions()->with(['posts' => function($query) {
            $query->withSum('views', 'views');
        }])->get();

        $total = 0;
        $viewsByLevel = $levels->map(function ($level) use (&$total) {
            $sum = $level->posts->sum('views_sum_views');
            $total += $sum;
            return [
                'title' => $level['title'],
                'color' => $level['color'],
                'views' => $sum
            ];
        });

        return [
            'total' => $total,
            'viewsByLevel' => $viewsByLevel
        ];
    }
}
