<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAction extends Model
{
    use HasFactory;

    // ระบุฟิลด์ที่อนุญาตให้ทำงานกับ mass assignment
    protected $fillable = [
        'course_id',
        'user_id',
        'isFinishCourse',
        'lastTimestamp',
        'isFinishVideo',
        'isFinishQuiz',
        'isDownloadCertificate',
        'isReview',
        'rating',
    ];
}
