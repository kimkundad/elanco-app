<?php

namespace App\Exports;

use App\Models\SystemLogs\SystemLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SystemLogsExport implements FromCollection, WithHeadings
{
    /**
     * Return data to export.
     */
    public function collection()
    {
        return SystemLog::with('user.countryDetails')->get()->map(function ($log) {
            return [
                'Date' => $log->created_at->format('Y-m-d H:i:s'),
                'Country Name' => $log->user->countryDetails->name ?? 'N/A',
                'Username' => $log->user->name,
                'Login IP' => $log->ip_address,
                'Action' => $log->action,
            ];
        });
    }

    /**
     * Add headers to the CSV file.
     */
    public function headings(): array
    {
        return ['Date', 'Country Name', 'Username', 'Login IP', 'Action'];
    }
}
