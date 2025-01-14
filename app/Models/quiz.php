<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quiz extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->hasMany(question::class, 'quiz_id');
    }

    public function courses()
    {
        return $this->hasMany(course::class, 'id_quiz', 'id'); // เชื่อมโยงกับ courses ผ่าน id_quiz
    }
}
