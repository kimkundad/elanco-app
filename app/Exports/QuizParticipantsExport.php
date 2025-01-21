<?php

namespace App\Exports;

use App\Models\CourseAction;
use App\Models\QuizUserAnswer;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuizParticipantsExport implements FromCollection, WithHeadings
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Return data to export.
     */
    public function collection()
    {
        return CourseAction::with(['user.countryDetails', 'course'])
            ->whereHas('course', function ($query) {
                $query->where('id_quiz', $this->id);
            })
            ->get()
            ->map(function ($participant) {
                $quizId = $participant->course->id_quiz;

                $correctAnswers = QuizUserAnswer::where('quiz_id', $quizId)
                    ->where('user_id', $participant->user_id)
                    ->whereHas('answer', function ($query) {
                        $query->where('answers_status', 1);
                    })
                    ->count();

                $incorrectAnswers = QuizUserAnswer::where('quiz_id', $quizId)
                    ->where('user_id', $participant->user_id)
                    ->whereHas('answer', function ($query) {
                        $query->where('answers_status', 0);
                    })
                    ->count();

                $totalQuestions = $correctAnswers + $incorrectAnswers;
                $passPercentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

                $startDate = Carbon::parse($participant->created_at);
                $completionDate = Carbon::parse($participant->updated_at);

                $days = $startDate->diffInDays($completionDate);
                $hours = $startDate->diffInHours($completionDate) % 24;
                $minutes = $startDate->diffInMinutes($completionDate) % 60;

                $duration = sprintf('%dD %dHrs %dMins', $days, $hours, $minutes);

                return [
                    'Country Name' => $participant->user->countryDetails->name ?? 'N/A',
                    'Name' => "{$participant->user->firstName} {$participant->user->lastName}",
                    'Clinic / Hospital Name' => $participant->user->clinic ?? 'N/A',
                    'Attempts' => 1,
                    'Correct' => $correctAnswers,
                    'Incorrect' => $incorrectAnswers,
                    'Pass Percentage' => $passPercentage . '%',
                    'Start Learning Date' => $startDate->format('Y-m-d H:i:s'),
                    'Completion Date' => $completionDate->format('Y-m-d H:i:s'),
                    'Duration' => $duration,
                ];
            });
    }

    /**
     * Add headers to the Excel file.
     */
    public function headings(): array
    {
        return [
            'Country Name',
            'Name',
            'Clinic / Hospital Name',
            'Attempts',
            'Correct',
            'Incorrect',
            'Pass Percentage',
            'Start Learning Date',
            'Completion Date',
            'Duration',
        ];
    }
}
