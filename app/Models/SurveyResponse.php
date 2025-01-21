<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',  // ฟิลด์ survey_id
        'user_id',    // ฟิลด์ user_id
    ];

    public function surveyResponseAnswers()
    {
        return $this->hasMany(SurveyResponseAnswer::class, 'survey_response_id');
    }
}
