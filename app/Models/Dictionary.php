<?php

namespace App\Models;

use App\Actions\EditorJS;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'body'
    ];

    public function getExcerptAttribute()
    {
        return (new EditorJS())->set($this->body)->excerpt();
    }
}
