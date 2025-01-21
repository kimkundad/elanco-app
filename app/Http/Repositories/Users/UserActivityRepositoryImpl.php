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
        $filterableColumns = [
            'activity_type',
            'activity_detail',
            'device_type',
            'browser_type',
            'ip_address',
            'activity_timestamp',
        ];

        $query = UserActivity::with('user.countryDetails');

        foreach ($queryParams as $key => $value) {
            if (in_array($key, $filterableColumns)) {
                $query->where($key, 'LIKE', '%' . $value . '%');
            }
        }

        return $query;
    }

    public function save(array $data)
    {
        $activity = new UserActivity();
        $activity->fill($data);
        $activity->save();

        return $activity;
    }
}
