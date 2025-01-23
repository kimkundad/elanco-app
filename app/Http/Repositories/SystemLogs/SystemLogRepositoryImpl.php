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
        $query = SystemLog::with('user.countryDetails');

        if (!empty($queryParams['search'])) {
            $query->where(function ($subQuery) use ($queryParams) {
                $search = $queryParams['search'];
                $subQuery->where('action', 'LIKE', '%' . $search . '%')
                    ->orWhere('ip_address', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('firstName', 'LIKE', '%' . $search . '%')
                            ->orWhere('lastName', 'LIKE', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%$search%"]);
                    });
            });
        }

        return $query;
    }

    public function save($data)
    {
        return SystemLog::create($data);
    }
}
