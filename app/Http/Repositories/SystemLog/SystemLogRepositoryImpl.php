<?php

namespace App\Http\Repositories\SystemLog;

use App\Models\SystemLog\SystemLog;

class SystemLogRepositoryImpl implements SystemLogRepository
{
    public function findAll()
    {
        return SystemLog::with('user.countryDetails')->get();
    }

    public function save($data)
    {
        return SystemLog::create($data);
    }
}
