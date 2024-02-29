<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_lat',
        'slug',
        'scores_min',
        'scores_max',
        'color',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
