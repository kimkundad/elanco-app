<?php

namespace App\Exports;

use App\Models\CourseAction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CourseReviewExport implements FromCollection, WithHeadings
{
    private $id;

    /**
     * Set course ID for the export.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Return data to export.
     */
    public function collection()
    {
        return CourseAction::with(['user.countryDetails'])
            ->where('course_id', $this->id)
            ->get()
            ->map(function ($review) {
                return [
                    'Country Name' => $review->user->countryDetails->name ?? 'N/A',
                    'Name' => "{$review->user->firstName} {$review->user->lastName}",
                    'Clinic / Hospital Name' => $review->user->clinic ?? 'N/A',
                    'Type' => $review->user->userType ?? 'N/A',
                    'Email' => $review->user->email ?? 'N/A',
                    'Rating' => (string)($review->rating ?? 'N/A'),
                    'Time Stamp' => $review->updated_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Add headers to the export file.
     */
    public function headings(): array
    {
        return ['Country Name', 'Name', 'Clinic / Hospital Name', 'Type', 'Email', 'Rating', 'Time Stamp'];
    }
}
