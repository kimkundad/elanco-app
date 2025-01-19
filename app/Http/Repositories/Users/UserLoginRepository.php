<?php

namespace App\Http\Repositories\Users;

interface UserLoginRepository
{
    public function save(array $data);
}
