<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory, Translatable;

    protected $fillable = [
        'name',
        'name_lat',
        'position',
        'position_lat'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
