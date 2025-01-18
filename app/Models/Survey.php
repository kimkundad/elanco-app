<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = ['survey_id', 'survey_title', 'survey_detail', 'expire_date', 'created_by'];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function courses()
    {
        return $this->hasMany(course::class, 'survey_id', 'id'); // เชื่อมโยงกับ courses ผ่าน id_quiz
    }


    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'survey_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
