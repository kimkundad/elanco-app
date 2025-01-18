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
        return User::select('id', 'name', 'email', 'created_at', 'updated_at')->get(); // เลือกข้อมูลที่ต้องการ
    }

    /**
     * Add headers to the Excel file.
     */
    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Created At', 'Updated At'];
    }
}

