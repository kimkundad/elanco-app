<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizUserAnswer extends Model
{
    use HasFactory;

    protected $table = 'quiz_user_answers'; // กำหนดชื่อ Table
    protected $fillable = [
        'user_id',
        'quiz_id',
        'question_id',
        'answer_id'
    ];

    // สัมพันธ์กับ Users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // สัมพันธ์กับ Quizzes
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    // สัมพันธ์กับ Questions
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    // สัมพันธ์กับ Answers
    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
