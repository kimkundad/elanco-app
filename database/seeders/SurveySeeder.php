<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;

class SurveySeeder extends Seeder
{
    public function run()
    {
        // สร้าง 5 Surveys พร้อมคำถามและคำตอบ
        Survey::factory()
            ->count(5) // จำนวน Survey
            ->has(
                SurveyQuestion::factory()
                    ->count(10) // จำนวนคำถามต่อ Survey
                    ->has(
                        SurveyAnswer::factory()->count(4), // จำนวนคำตอบต่อคำถาม
                        'answers'
                    ),
                'questions'
            )
            ->create();
    }
}
