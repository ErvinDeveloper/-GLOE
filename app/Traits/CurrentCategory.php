<?php

namespace App\Traits;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;

trait CurrentCategory
{
    public function setCurrentCategory(Category|Model $category):void
    {
        View::composer(['parts.menu'], function ($view) use ($category) {
            $view->with([
                'current_category' => $category
            ]);
        });
    }
}
