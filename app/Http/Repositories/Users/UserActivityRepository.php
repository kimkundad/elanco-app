<?php

namespace App\Http\Repositories\Users;

interface UserActivityRepository
{
    public function findAll();

    public function findById(int $id);

    public function findPaginated();

    public function save(array $data);
}
