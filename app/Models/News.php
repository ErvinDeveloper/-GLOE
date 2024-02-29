<?php

namespace App\Models;

use App\Actions\EditorJS;
use App\Traits\DateFormat;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class News extends Model
{
    use HasFactory, DateFormat, Translatable;

    protected $fillable = [
        'title',
        'title_lat',
        'body',
        'body_lat'
    ];

    public function getBodyViaEditorJSAttribute()
    {
        return (new EditorJS())->set((Lang::locale() === 'lat') ? $this->body_lat : $this->body)->render();
    }
}
