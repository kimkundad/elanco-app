<?php

namespace App\Http\Repositories\SystemLogs;

use App\Models\SystemLogs\SystemLog;

class SystemLogRepositoryImpl implements SystemLogRepository
{
    public function findAll()
    {
        return SystemLog::with('user.countryDetails')->get();
    }

    public function findPaginated(array $queryParams)
    {
        $filterableColumns = ['ip_address', 'action', 'status', 'error_reason', 'created_at', 'updated_at'];

        $query = SystemLog::with('user.countryDetails');

        foreach ($queryParams as $key => $value) {
            if (in_array($key, $filterableColumns)) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        return $query;
    }

    public function save($data)
    {
        return SystemLog::create($data);
    }
}
