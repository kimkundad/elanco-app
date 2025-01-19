<?php

namespace App\Http\Repositories\SystemLogs;

interface SystemLogRepository
{
    public function findAll();

    public function findPaginated();

    public function save($data);
}
