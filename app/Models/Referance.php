<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referance extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'image',
        'file',
        'description'
    ];

    // ความสัมพันธ์กับ Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

