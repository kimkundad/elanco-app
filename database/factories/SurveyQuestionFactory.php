<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyQuestion>
 */
class SurveyQuestionFactory extends Factory
{
    protected $model = \App\Models\SurveyQuestion::class;

    public function definition()
    {
        return [
            'survey_id' => \App\Models\Survey::factory(), // สร้าง Survey ใหม่หรือเชื่อมกับที่มีอยู่
            'question_detail' => $this->faker->sentence,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
