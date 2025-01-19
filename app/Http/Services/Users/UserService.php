<?php

namespace App\Http\Services\Users;

use App\Http\Repositories\Users\UserLoginRepository;
use Illuminate\Support\Facades\Auth;

class UserService
{
    private UserLoginRepository $userLoginRepository;

    public function __construct(UserLoginRepository $userLoginRepository)
    {
        $this->userLoginRepository = $userLoginRepository;
    }

    public function saveUserLogin(string $ipAddress, ?string $device = null)
    {

        $user = Auth::user();

        return $this->userLoginRepository->save([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => $ipAddress,
            'device' => $device,
        ]);
    }
}
