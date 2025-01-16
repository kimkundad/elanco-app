<?php

namespace App\Http\Middleware;

use Closure;

class UserRoleMiddleware
{
    public function handle($request, Closure $next, $roles)
    {


        if ($request->user() == null) {
            return response()->json([
                'message' => 'Unauthorized: Please log in first.'
            ], 401);
        }

        // แปลง roles เป็น array และตรวจสอบบทบาท
        $rolesArray = explode("|", $roles);

        if (!$request->user()->hasAnyRole($rolesArray)) {
            return response()->json([
                'message' => 'Unauthorized: You do not have the required permissions.',
                'required_roles' => $rolesArray,
            ], 401);
        }

        return $next($request);
    }
}
