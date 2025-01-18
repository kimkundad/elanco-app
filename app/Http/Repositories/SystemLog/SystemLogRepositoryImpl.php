<?php

namespace App\Http\Repositories\SystemLog;

use App\Models\SystemLog;

class SystemLogRepositoryImpl implements SystemLogRepository
{

    public function save($data)
    {
        return SystemLog::create($data);
    }
}
