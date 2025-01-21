<?php

namespace App\Exports;

use App\Http\Utils\TimeDurationCalculator;
use App\Models\SurveyResponse;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SurveyParticipantsExport implements FromCollection, WithHeadings
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
        return SurveyResponse::with(['user.countryDetails'])
            ->where('survey_id', $this->id)
            ->get()
            ->map(function ($participant) {
                $startDate = Carbon::parse($participant->created_at);
                $completionDate = Carbon::parse($participant->updated_at);

                $duration = TimeDurationCalculator::calculateTimeDuration($startDate, $completionDate);

                return [
                    'Country Name' => $participant->user->countryDetails->name ?? 'N/A',
                    'Name' => "{$participant->user->firstName} {$participant->user->lastName}",
                    'Clinic / Hospital Name' => $participant->user->clinic ?? 'N/A',
                    'Email' => $participant->user->email ?? 'N/A',
                    'Start Date' => $startDate->format('Y-m-d H:i:s'),
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
            'Email',
            'Start Date',
            'Completion Date',
            'Duration',
        ];
    }
}
