<?php

namespace App\Http\Middleware;

use App\Http\Services\SystemLogs\SystemLogService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        try {
            $response = $next($request);

            if (!Auth::check()) {
                return $response;
            }

            if ($response->status() === 200) {
                $this->systemLogService->saveAction($request, 'success');
            } else {
                $this->systemLogService->saveAction($request, 'error', $response->getContent());
            }

            return $response;
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null,
            ], 500);
        }
    }
}
