<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_response_id', // ฟิลด์ survey_response_id
        'survey_question_id', // ฟิลด์ survey_question_id
        'survey_answer_id',   // ฟิลด์ survey_answer_id
        'custom_answer',      // ฟิลด์ custom_answer
    ];

    public function surveyResponse()
    {
        return $this->belongsTo(SurveyResponse::class, 'survey_response_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
