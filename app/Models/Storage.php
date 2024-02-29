<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = [
        'storageable_type',
        'storageable_id',
        'path',
        'extension',
        'filesize',
        'count_downloads',
        'user_id',
        'original_name'
    ];

    public function storageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOnlyExtension($query, array|string $params)
    {
        $query->select('id', 'path', 'filesize', 'count_downloads', 'original_name', 'extension');

        if (is_array($params)) {
            $query->whereIn('extension', $params);
        } else {
            $query->where('extension', $params);
        }

        return $query;
    }
}
