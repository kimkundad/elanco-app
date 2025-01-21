<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['survey_question_id', 'answer_text', 'sort_order'];

    public function surveyResponseAnswers()
    {
        return $this->hasMany(SurveyResponseAnswer::class, 'survey_answer_id');
    }
}
