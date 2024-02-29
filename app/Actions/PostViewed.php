<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PostViewed
{
    public function handler(Model $post): void
    {
        if (Auth::check()) {
            $view = $post->views()->updateOrCreate([
                'user_id' => Auth::id()
            ]);
            $view->increment('views');
        }
    }
}
