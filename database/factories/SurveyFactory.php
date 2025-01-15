<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    protected $model = \App\Models\Survey::class;

    public function definition()
    {
        return [
            'survey_id' => $this->faker->unique()->regexify('S[0-9]{3}'),
            'survey_title' => $this->faker->sentence,
            'survey_detail' => $this->faker->paragraph,
            'expire_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}
