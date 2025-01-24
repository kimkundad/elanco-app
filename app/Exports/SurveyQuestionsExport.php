<?php

namespace App\Exports;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SurveyQuestionsExport implements FromCollection, WithHeadings
{
    private $id;
    private $maxChoices = 0;
    private $survey;

    public function __construct($id)
    {
        $this->id = $id;
        $this->survey = Survey::with(['questions.answers.surveyResponseAnswers'])->find($this->id);
        if ($this->survey) {
            $this->calculateMaxChoices();
        }
    }

    private function calculateMaxChoices()
    {
        $this->maxChoices = $this->survey->questions->reduce(function ($max, $question) {
            return max($max, $question->answers->count());
        }, 0);
    }

    /**
     * Return data to export.
     */
    public function collection()
    {
        if (!$this->survey) {
            return collect([]);
        }

        $totalParticipants = SurveyResponse::where('survey_id', $this->id)
            ->distinct('user_id')
            ->count('user_id');

        return $this->survey->questions->map(function ($question) use ($totalParticipants) {
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
