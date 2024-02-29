<?php

namespace App\Models;

use App\Actions\EditorJS;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    use HasFactory;

    public ?string $youtubeIframe;
    public ?string $audioPlayer;
    public ?string $videoPlayer;


    protected $fillable = [
        'task',
        'test_id',
        'question_type',
        'answer',
        'default',
        'correct_answer',
        'youtube_url',
        'audio',
        'video',
        'words',
        'words_chunk',
        'scores',
    ];

    public function test(): HasOne
    {
        return $this->hasOne(Test::class, 'id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function answereds(): HasMany
    {
        return $this->hasMany(Answered::class);
    }

    public function doesQuestionRelateToCurrentTesting(Test $test): void
    {
        if (isset($this->id) && $this->test_id !== $test->id) {
            abort(404);
        }
    }

    public function nextQuestionId(): ?int
    {
        return Question::where('id', '>', $this->id)->min('id');
    }

    public function getExcerptAttribute()
    {
        return (new EditorJS())->set($this->task)->excerpt();
    }

    public function getTaskTypeAttribute()
    {
        switch ($this->question_type) {
            case 'default':
                return 'Выбор варианта';
            case 'words_chunk':
                return 'Правильный порядок слов';
            case 'words_substitute':
                return 'Недостающее слово';
        }
    }
}
