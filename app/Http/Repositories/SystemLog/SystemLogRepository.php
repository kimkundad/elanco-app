<?php

namespace App\Http\Repositories\SystemLog;

interface SystemLogRepository
{
    public function findAll();

    public function save($data);
}
