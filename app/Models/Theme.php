<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory, Translatable;

    protected $fillable = [
        'title',
        'title_lat',
        'slug'
    ];
}
