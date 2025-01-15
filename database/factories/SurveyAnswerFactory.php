<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyAnswer>
 */
class SurveyAnswerFactory extends Factory
{
    protected $model = \App\Models\SurveyAnswer::class;

    public function definition()
    {
        return [
            'survey_question_id' => \App\Models\SurveyQuestion::factory(), // สร้างคำถามใหม่หรือเชื่อมกับที่มีอยู่
            'answer_text' => $this->faker->sentence,
            'sort_order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
