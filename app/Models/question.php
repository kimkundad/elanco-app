<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'detail'
    ];

    public function answers()
    {
        return $this->hasMany(answer::class, 'questions_id');
    }

    public function quizUserAnswers()
    {
        return $this->hasMany(QuizUserAnswer::class, 'question_id', 'id');
    }
}
