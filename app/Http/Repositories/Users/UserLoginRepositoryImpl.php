<?php

namespace App\Http\Repositories\Users;

use App\Models\Users\UserLogin;

class UserLoginRepositoryImpl implements UserLoginRepository
{
    public function save(array $data)
    {
        $userLogin = new UserLogin();
        $userLogin->fill($data);
        $userLogin->save();

        return $userLogin;
    }
}
