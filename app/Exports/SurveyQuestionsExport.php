<?php

namespace App\Exports;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SurveyQuestionsExport implements FromCollection, WithHeadings
{
    private $surveyId;
    private $maxChoices;

    public function __construct($surveyId)
    {
        $this->surveyId = $surveyId;
        $this->calculateMaxChoices();
    }

    private function calculateMaxChoices()
    {
        $survey = Survey::with(['questions.answers'])->findOrFail($this->surveyId);

        $this->maxChoices = $survey->questions->reduce(function ($max, $question) {
            return max($max, $question->answers->count());
        }, 0);
    }

    /**
     * Return data to export.
     */
    public function collection()
    {
        $survey = Survey::with(['questions.answers.surveyResponseAnswers'])->findOrFail($this->surveyId);

        $totalParticipants = SurveyResponse::where('survey_id', $this->surveyId)
            ->distinct('user_id')
            ->count('user_id');

        return $survey->questions->map(function ($question) use ($totalParticipants) {
            $row = [
                'Question' => $question->question_detail,
            ];

            foreach ($question->answers as $index => $answer) {
                $selectedCount = $answer->surveyResponseAnswers->count();
                $percentage = $totalParticipants > 0
                    ? round(($selectedCount / $totalParticipants) * 100, 2)
                    : 0;

                $row["Choice " . ($index + 1)] = $answer->answer_text;
                $row["Percentage " . ($index + 1)] = "{$percentage}%";
            }

            for ($i = $question->answers->count() + 1; $i <= $this->maxChoices; $i++) {
                $row["Choice $i"] = '';
                $row["Percentage $i"] = '';
            }

            return $row;
        });
    }

    /**
     * Add headers to the Excel file.
     */
    public function headings(): array
    {
        $baseHeaders = ['Question'];

        $choiceHeaders = [];
        for ($i = 1; $i <= $this->maxChoices; $i++) {
            $choiceHeaders[] = "Choice $i";
            $choiceHeaders[] = "Percentage $i";
        }

        return array_merge($baseHeaders, $choiceHeaders);
    }
}
