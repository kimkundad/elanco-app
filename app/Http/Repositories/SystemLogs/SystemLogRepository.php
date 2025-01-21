<?php

namespace App\Http\Repositories\SystemLogs;

interface SystemLogRepository
{
    public function findAll();

    public function findPaginated(array $queryParams);

    public function save($data);
}
