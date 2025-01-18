<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'expire_date', 'questions_title', 'pass_percentage', 'certificate', 'point_cpd'
    ];

    public function questions()
    {
        return $this->hasMany(question::class, 'quiz_id');
    }

    public function courses()
    {
        return $this->hasMany(course::class, 'id_quiz', 'id'); // เชื่อมโยงกับ courses ผ่าน id_quiz
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    public function countries()
    {
        return $this->hasManyThrough(
            Country::class,     // โมเดลปลายทาง
            course::class,      // โมเดลตัวกลาง
            'id_quiz',          // Foreign key ใน Course ที่ชี้ไปยัง Quiz
            'id',               // Primary key ใน Country
            'id',               // Primary key ใน Quiz
            'id'                // Foreign key ใน Course ที่ชี้ไปยัง Country (ผ่าน Pivot)
        );
    }

}
