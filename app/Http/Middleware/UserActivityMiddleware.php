<?php

namespace App\Http\Middleware;

use App\Http\Repositories\Courses\CourseRepository;
use App\Http\Services\Users\UserActivityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserActivityMiddleware
{
    private UserActivityService $userActivityService;
    private CourseRepository $courseRepository;

    public function __construct(UserActivityService $userActivityService, CourseRepository $courseRepository)
    {
        $this->userActivityService = $userActivityService;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $this->logUserActivity($request);

        return $response;
    }

    private function logUserActivity(Request $request)
    {
        $route = $request->path();
        $method = $request->method();
        $userId = Auth::id();
        $ipAddress = $request->ip();
        $device = $this->getDeviceType($request->header('User-Agent'));
        $browser = $this->getBrowserType($request->header('User-Agent'));
        $activity = null;
        $detail = null;

        if ($route === 'api/login' && $method === 'POST') {
            $activity = 'Log In';
            $detail = 'User successfully logged in.';
        } elseif (preg_match('/^courses\/(\d+)\/progress$/', $route, $matches) && $method === 'PUT') {
            $course = $this->courseRepository->findById($matches[1]);
            $activity = 'Course Enrollment';
            $detail = $course ? 'Start Learning - ' . $course->course_title : 'Start Learning - Unknown Course';
        } elseif (preg_match('/^courses\/(\d+)\/certificate$/', $route, $matches) && $method === 'GET') {
            $course = $this->courseRepository->findById($matches[1]);
            $activity = 'Course Completion';
            $detail = $course ? 'Earn Certificate - ' . $course->course_title : 'Earn Certificate - Unknown Course';
        }

        if ($activity) {
            $this->userActivityService->logActivity($userId, $activity, $detail, $ipAddress, $device, $browser);
        }
    }

    private function getDeviceType($userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh')) {
            return 'MacOS';
        } elseif (str_contains($userAgent, 'Android')) {
            return 'Android';
        } elseif (str_contains($userAgent, 'iPhone')) {
            return 'iPhone';
        } elseif (str_contains($userAgent, 'iPad')) {
            return 'iPad';
        }
        return 'Unknown Device';
    }

    private function getBrowserType($userAgent): string
    {
        if (str_contains($userAgent, 'Chrome')) {
            return 'Google Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Mozilla Firefox';
        } elseif (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Microsoft Edge';
        } elseif (str_contains($userAgent, 'Trident')) {
            return 'Internet Explorer';
        }
        return 'Unknown Browser';
    }
}
