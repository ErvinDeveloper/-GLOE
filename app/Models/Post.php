<?php

namespace App\Models;

use App\Services\ResizeImageService;
use App\Traits\DateFormat;
use App\Traits\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\File;

class Post extends Model
{
    use HasFactory, Translatable, DateFormat;

    protected $fillable = [
        'title',
        'title_lat',
        'slug',
        'body',
        'body_lat',
        'user_id',
        'category_id',
        'level_id',
        'image',
        'theme_id',
        'is_published',
        'is_video',
        'excerpt',
        'excerpt_lat',
        'author_id'
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image) || !File::exists('storage/' . $this->image)) {
            return null;
        }

        return asset('storage/' . $this->image);
    }

    public function getImageUrlsAttribute(): array
    {
        return (new ResizeImageService)->getResizeImages($this->image);
    }

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function scopeIsVideo($query)
    {
        return $query->where('is_video', 1);
    }

    public function scopeFilterByThemeAndLevel($query, $params)
    {
        foreach ($params as $field => $param) {
            $query->where($field, $param);
        }

        return $query;
    }

    public function scopeSearchFilter($query, $searchValue)
    {
        return $query->where('title', 'LIKE', '%' . $searchValue . '%')->orWhere('body', 'LIKE', '%' . $searchValue . '%');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function theme(): HasOne
    {
        return $this->hasOne(Theme::class, 'id', 'theme_id');
    }

    public function author(): HasOne
    {
        return $this->hasOne(Author::class, 'id', 'author_id');
    }

    public function level(): HasOne
    {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }

    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function storage(): MorphMany
    {
        return $this->morphMany(Storage::class, 'storageable');
    }
}
