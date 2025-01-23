<?php

namespace App\Exports;

use App\Models\quiz;
use App\Models\QuizUserAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizQuestionsExport implements FromCollection, WithHeadings
{
    private $id;
    private $maxChoices = 0;
    private $quiz;

    public function __construct($id)
    {
        $this->id = $id;
        $this->quiz = quiz::with('questions.answers.quizUserAnswers')->find($this->id);
        if ($this->quiz) {
            $this->calculateMaxChoices();
        }
    }

    private function calculateMaxChoices()
    {
        $this->maxChoices = $this->quiz->questions->reduce(function ($max, $question) {
            return max($max, $question->answers->count());
        }, 0);
    }

    /**
     * Return data to export.
     */
    public function collection()
    {
        if (!$this->quiz) {
            return collect([]);
        }

        $totalParticipants = QuizUserAnswer::where('quiz_id', $this->id)
            ->distinct('user_id')
            ->count('user_id');

        return $this->quiz->questions->map(function ($question) use ($totalParticipants) {
            $row = [
                'Question' => strip_tags($question->detail),
            ];

            foreach ($question->answers as $index => $answer) {
                $selectedCount = $answer->quizUserAnswers->count() ?? 0;
                $percentage = $totalParticipants > 0
                    ? round(($selectedCount / $totalParticipants) * 100, 2)
                    : 0;

                $row["Choice " . ($index + 1)] = strip_tags($answer->answers); // กำจัด HTML tags จากคำตอบ
                $row["Correct " . ($index + 1)] = $answer->answers_status === 1 ? 'Correct' : 'Incorrect';
                $row["Percentage " . ($index + 1)] = "{$percentage}%";
            }

            for ($i = $question->answers->count() + 1; $i <= $this->maxChoices; $i++) {
                $row["Choice $i"] = '';
                $row["Correct $i"] = '';
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
            $choiceHeaders[] = "Correct $i";
            $choiceHeaders[] = "Percentage $i";
        }

        return array_merge($baseHeaders, $choiceHeaders);
    }
}
