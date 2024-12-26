<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // ดึงข้อมูลผู้ใช้และโหลด countryDetails
        $user = Auth::user();
        $user->load('countryDetails');

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'country' => $user->countryDetails ? $user->countryDetails->name : null, // ดึงชื่อประเทศ
            ],
            'verify' => 1,
            'refresh_token' => $this->createRefreshToken($request->email),
        ]);
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            $user->load('countryDetails');

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'country' => $user->countryDetails ? $user->countryDetails->name : null, // แสดงชื่อประเทศ
                ]
            ], 200);

        } catch (TokenExpiredException $e) {
            // กรณี Token หมดอายุ
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);

        } catch (TokenInvalidException $e) {
            // กรณี Token ไม่ถูกต้อง
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);

        } catch (JWTException $e) {
            // กรณีไม่มี Token ในคำขอ
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        }
    }


    // ฟังก์ชันสร้าง Refresh Token (บันทึกในฐานข้อมูลหรือที่อื่น)
    private function createRefreshToken($email)
    {
        $refreshToken = bin2hex(random_bytes(40)); // สร้าง Refresh Token แบบสุ่ม
        \DB::table('refresh_tokens')->updateOrInsert(
            ['email' => $email], // ตรวจสอบว่ามีอยู่หรือไม่
            ['refresh_token' => $refreshToken, 'created_at' => now()]
        );
        return $refreshToken;
    }

    // ใช้ Refresh Token เพื่อสร้าง Access Token ใหม่
    public function refreshToken(Request $request)
    {
        $refreshToken = $request->refresh_token;
        $tokenRecord = \DB::table('refresh_tokens')->where('refresh_token', $refreshToken)->first();

        if (!$tokenRecord) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        // ดึงข้อมูลผู้ใช้จาก Refresh Token
        $user = \App\Models\User::where('email', $tokenRecord->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // ออก Access Token ใหม่
        $newToken = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $newToken
        ]);
    }


}
