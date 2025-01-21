<?php

namespace App\Http\Services\Users;

use App\Http\Repositories\Users\UserActivityRepository;
use App\Http\Utils\ArrayKeyConverter;

class UserActivityService
{
    private UserActivityRepository $userActivityRepository;

    public function __construct(UserActivityRepository $userActivityRepository)
    {
        $this->userActivityRepository = $userActivityRepository;
    }

    public function findAll(array $queryParams)
    {
        $paginationParams = array_filter($queryParams, function ($key) {
            return in_array($key, ['page', 'per_page']);
        }, ARRAY_FILTER_USE_KEY);

        $queryParams = ArrayKeyConverter::convertToSnakeCase($queryParams);

        return $this->userActivityRepository->findPaginated($queryParams)
            ->customPaginate(function ($items) {
                return collect($items)->map->format(); // ใช้ format จาก Model
            }, $paginationParams);
    }

    public function findById($id)
    {
        return $this->userActivityRepository->findById($id)->format();
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
