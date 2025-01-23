<?php

namespace App\Http\Repositories\Users;

use App\Models\Users\UserActivity;

class UserActivityRepositoryImpl implements UserActivityRepository
{
    public function findAll()
    {
        return UserActivity::with('user.countryDetails')->get();
    }

    public function findById(int $id)
    {
        return UserActivity::with('user.countryDetails')->findOrFail($id);
    }

    public function findPaginated(array $queryParams)
    {
        $query = UserActivity::with('user.countryDetails');

        if (isset($queryParams['search']) && $queryParams['search']) {
            $search = $queryParams['search'];
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('activity_detail', 'LIKE', "%{$search}%")
                    ->orWhere('device_type', 'LIKE', "%{$search}%")
                    ->orWhere('browser_type', 'LIKE', "%{$search}%");
            });
        }

        if (isset($queryParams['select_activity']) && $queryParams['select_activity']) {
            $query->where('activity_type', $queryParams['select_activity']);
        }

        return $query;
    }

    public function findTypes()
    {
        return UserActivity::distinct()
            ->orderBy('activity_type', 'asc')
            ->pluck('activity_type');
    }

    public function save(array $data)
    {
        $activity = new UserActivity();
        $activity->fill($data);
        $activity->save();

        return $activity;
    }
}
