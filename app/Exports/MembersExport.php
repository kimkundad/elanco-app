<?php

namespace App\Exports;

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromCollection, WithHeadings
{
    /**
     * Return data to export.
     */
    public function collection()
    {
        return User::with(['countryDetails', 'latestLogin'])->get()->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name' => "{$user->firstName} {$user->lastName}",
                'Clinic / Hospital Name' => $user->clinic ?? 'N/A',
                'Email' => $user->email,
                'Type' => $user->userType,
                'Country Name' => $user->countryDetails->name ?? 'N/A',
                'Last Active' => $user->latestLogin ? $user->latestLogin->login_at->format('Y-m-d H:i:s') : 'N/A',
            ];
        });
    }

    /**
     * Add headers to the Excel file.
     */
    public function headings(): array
    {
        return ['ID', 'Name', 'Clinic / Hospital Name', 'Email', 'Type', 'Country Name', 'Last Active'];
    }
}

