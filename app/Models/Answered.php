<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Answered extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'question_id',
        'correct_answer',
        'user_id'
    ];

    public function question(): HasOne
    {
        return $this->hasOne(Question::class, 'id', 'question_id');
    }
}
