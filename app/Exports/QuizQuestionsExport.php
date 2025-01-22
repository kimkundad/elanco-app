<?php

namespace App\Exports;

use App\Models\quiz;
use App\Models\QuizUserAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizQuestionsExport implements FromCollection, WithHeadings
{
    private $quizId;
    private $maxChoices;

    public function __construct($quizId)
    {
        $this->quizId = $quizId;
        $this->calculateMaxChoices();
    }

    private function calculateMaxChoices()
    {
        $quiz = quiz::with('questions.answers')->findOrFail($this->quizId);
        $this->maxChoices = $quiz->questions->reduce(function ($max, $question) {
            return max($max, $question->answers->count());
        }, 0);
    }

    /**
     * Return data to export.
     */
    public function collection()
    {
        $quiz = quiz::with(['questions.answers.quizUserAnswers'])->findOrFail($this->quizId);

        $totalParticipants = QuizUserAnswer::where('quiz_id', $this->quizId)
            ->distinct('user_id')
            ->count('user_id');

        return $quiz->questions->map(function ($question) use ($totalParticipants) {
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
