<?php

namespace App\Http\Repositories\Users;

use App\Models\Users\UserActivity;

class UserActivityRepositoryImpl implements UserActivityRepository
{
    public function findAll()
    {
        return UserActivity::with('user.countryDetails')->get();
    }

    public function findPaginated()
    {
        return UserActivity::with('user.countryDetails');
    }

    public function save(array $data)
    {
        $activity = new UserActivity();
        $activity->fill($data);
        $activity->save();

        return $activity;
    }
}
