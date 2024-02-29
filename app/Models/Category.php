<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Category extends Model
{
    use HasFactory, Translatable;

    protected $fillable = [
        'title',
        'title_lat',
        'slug',
        'parent_id',
        'description',
        'description_lat',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getParamsForFiltering(Request $request): array
    {
        $params = [];
        $paramsKeys = ['level_id', 'theme_id'];
        foreach ($paramsKeys as $key) {
            if ($request->exists($key)) {
                $params[$key] = (int)abs($request->input($key));
            }
        }
        return $params;
    }
}
