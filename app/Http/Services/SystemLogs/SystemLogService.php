<?php

namespace App\Http\Services\SystemLogs;

use App\Http\Repositories\SystemLogs\SystemLogRepository;
use App\Http\Utils\ArrayKeyConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemLogService
{
    private SystemLogRepository $systemLogRepository;

    public function __construct(SystemLogRepository $systemLogRepository)
    {
        $this->systemLogRepository = $systemLogRepository;
    }

    public function findAll(array $queryParams)
    {
        $paginationParams = array_filter($queryParams, function ($key) {
            return in_array($key, ['page', 'per_page']);
        }, ARRAY_FILTER_USE_KEY);

        $queryParams = ArrayKeyConverter::convertToSnakeCase($queryParams);

        return $this->systemLogRepository->findPaginated($queryParams)
            ->customPaginate(function ($items) {
                return collect($items)->map->format(); // ใช้ format จาก Model
            }, $paginationParams);
    }

    public function saveAction(Request $request, string $status = 'access', string $errorReason = null)
    {
        $data = [
            'user_id' => Auth::id(),
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
        $route = $request->path();
        if (str_starts_with($route, 'api/')) {
            $route = substr($route, 4);
        }

        $method = $request->method();

        $map = [
            'login' => [
                'POST' => 'Login',
            ],
            'logout' => [
                'POST' => 'Logout',
            ],
            'register' => [
                'POST' => 'Register',
            ],
            'admin/course' => [
                'POST' => function ($route) {
                    return strpos($route, '{id}') !== false ? 'Edit Course' : 'Add Course';
                },
                'DELETE' => 'Delete Course',
            ],
            'admin/quiz' => [
                'POST' => function ($route) {
                    return strpos($route, '{id}') !== false ? 'Edit Quiz' : 'Add Quiz';
                },
                'DELETE' => 'Delete Quiz',
            ],
            'admin/survey' => [
                'POST' => function ($route) {
                    return strpos($route, '{id}') !== false ? 'Edit Survey' : 'Add Survey';
                },
                'DELETE' => function ($route) {
                    return str_contains($route, 'del-question-survey')
                        ? 'Delete Survey Question'
                        : 'Delete Survey';
                },
            ],
            'admin/user-activities' => [
                'GET' => 'View User Activities',
            ],
            'admin/system-logs' => [
                'GET' => 'View System Logs',
            ],
        ];

        foreach ($map as $pattern => $methods) {
            if (fnmatch($pattern, $route) && isset($methods[$method])) {
                $action = $methods[$method];

                if (is_callable($action)) {
                    return $action($route);
                }

                return $action;
            }
        }

        return 'Unknown Action';
    }
}
