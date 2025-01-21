<?php

namespace App\Http\Repositories\Users;

interface UserActivityRepository
{
    public function findAll();

    public function findById(int $id);

    public function findPaginated(array $queryParams);

    public function save(array $data);
}
