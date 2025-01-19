<?php

namespace App\Http\Repositories\SystemLogs;

use App\Models\SystemLogs\SystemLog;

class SystemLogRepositoryImpl implements SystemLogRepository
{
    public function findAll()
    {
        return SystemLog::with('user.countryDetails')->get();
    }

    public function findPaginated()
    {
        return SystemLog::with('user.countryDetails');
    }

    public function save($data)
    {
        return SystemLog::create($data);
    }
}
