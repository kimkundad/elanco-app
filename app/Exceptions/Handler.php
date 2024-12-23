<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $exception)
{
    if ($request->expectsJson()) {
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);
        }

        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);
        }

        if ($exception instanceof JWTException) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        }
    }

    return parent::render($request, $exception);
}
}
