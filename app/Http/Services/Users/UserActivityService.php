<?php

namespace App\Http\Services\Users;

use App\Http\Repositories\Users\UserActivityRepository;

class UserActivityService
{
    private UserActivityRepository $userActivityRepository;

    public function __construct(UserActivityRepository $userActivityRepository)
    {
        $this->userActivityRepository = $userActivityRepository;
    }

    public function logActivity($userId, $activity, $detail, $ipAddress, $device, $browser)
    {
        $data = [
            'user_id' => $userId,
            'activity_type' => $activity,
            'activity_detail' => $detail,
            'ip_address' => $ipAddress,
            'device_type' => $device,
            'browser_type' => $browser,
            'activity_timestamp' => now(),
        ];

        $this->userActivityRepository->save($data);
    }
}
