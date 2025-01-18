<?php

namespace App\Http\Services\SystemLog;

use App\Http\Repositories\SystemLog\SystemLogRepository;
use Illuminate\Http\Request;

class SystemLogService
{

    private SystemLogRepository $systemLogRepository;

    public function __construct(SystemLogRepository $systemLogRepository)
    {
        $this->systemLogRepository = $systemLogRepository;
    }

    public function saveAction(Request $request, string $status = 'access', string $errorReason = null)
    {
        $data = [
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'action' => $this->getAction($request),
            'status' => $status,
            'error_reason' => $errorReason,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->systemLogRepository->save($data);
    }

    private function getAction(Request $request): string
    {
        $route = ltrim($request->path(), 'api/');
        $method = $request->method();

        $map = [
            'login' => [
                'POST' => 'Login',
            ],
            'logout' => [
                'POST' => 'Logout',
            ],
            'admin/course' => [
                'POST' => 'Add Course',
                'PUT' => 'Edit Course',
                'DELETE' => 'Delete Course',
            ],
            'admin/quiz' => [
                'POST' => 'Add Quiz',
                'PUT' => 'Edit Quiz',
                'DELETE' => 'Delete Quiz',
            ],
            'admin/survey' => [
                'POST' => 'Add Survey',
                'PUT' => 'Edit Survey',
                'DELETE' => 'Delete Survey',
            ],
        ];

        foreach ($map as $pattern => $methods) {
            if (fnmatch($pattern, $route) && isset($methods[$method])) {
                return $methods[$method];
            }
        }

        return 'Unknown Action';
    }
}
