<?php

namespace App\Http\Middleware;

use App\Http\Services\SystemLog\SystemLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SystemLoggingMiddleware
{
    private SystemLogService $systemLogService;

    public function __construct(SystemLogService $systemLogService)
    {
        $this->systemLogService = $systemLogService;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->status() === 200) {
            $this->systemLogService->saveAction($request, 'success');
        } else {
            $this->systemLogService->saveAction($request, 'error', $response->getContent());
        }

        return $response;
    }
}
