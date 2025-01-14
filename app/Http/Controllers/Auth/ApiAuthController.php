<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Country;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\AnimalType;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\UserVerificationMail;
use Illuminate\Support\Str;

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

        if (!$user->email_verified_at) {
            return response()->json([
                'error' => 'Your email is not verified.',
                'message' => 'Please verify your email before logging in.',
            ], 403);
        }

        $user->load('countryDetails');

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'position' => $user->position,
                'vetId' => $user->vetId,
                'clinic' => $user->clinic,
                'userType' => $user->userType,
                'terms' => $user->terms,
                'flag' => $user->countryDetails ? $user->countryDetails->name : null, // ดึงชื่อประเทศ
            ],
            'verify' => 1,
            'refresh_token' => $this->createRefreshToken($request->email),
        ]);
    }

    public function resetPassword(Request $request)
    {
        try {
            // ดึงข้อมูลผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            // ตรวจสอบข้อมูลที่ส่งเข้ามา
            $validated = $request->validate([
                'current' => 'required|string',
                'password' => 'required|string|min:8|confirmed', // ต้องมี password_confirmation
            ]);

            // ตรวจสอบว่ารหัสผ่านเดิมตรงกันหรือไม่
            if (!Hash::check($validated['current'], $user->password)) {
                return response()->json([
                    'error' => 'Current password is incorrect',
                ], 400);
            }

            // อัปเดตรหัสผ่านใหม่
            $user->password = Hash::make($validated['password']);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userType' => 'required|string',
            'terms' => 'required|boolean',
            'prefix' => 'nullable|string',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'position' => 'nullable|string',
            'vetId' => 'nullable|string',
            'clinic' => 'nullable|string',
            'country' => 'required|string|exists:countries,flag', // ตรวจสอบ country
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // หาค่า country_id จาก `countries` table
        $country = Country::where('flag', $request->country)->first();

        // เริ่มต้น Transaction
        DB::beginTransaction();

        try {
            // สร้าง user
            $user = User::create([
                'name' => $request->firstName . ' ' . $request->lastName,
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'prefix' => $request->prefix,
                'userType' => $request->userType,
                'terms' => $request->terms,
                'position' => $request->position,
                'vetId' => $request->vetId,
                'clinic' => $request->clinic,
                'country' => $country->id,
                'email_verified_at' => null, // ยังไม่ได้ยืนยัน
            ]);

            $role = Role::find(3); // ค้นหา Role ที่ id = 3
            if ($role) {
                $user->roles()->attach($role); // เพิ่มความสัมพันธ์
            }

            $token = Str::random(64);

            DB::table('email_verifications')->insert([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $verificationUrl = route('verification.verify', ['token' => $token]);

            \Log::info('Generated Verification Link:', [
                'url' => $verificationUrl,
                'user_id' => $user->id,
            ]);

            // // สร้างลิงก์ยืนยัน
            // $verificationUrl = URL::temporarySignedRoute(
            //     'verification.verify', // Route ที่จะใช้ตรวจสอบ
            //     now()->addMinutes(60), // ลิงก์ใช้ได้ 60 นาที
            //     ['id' => $user->id] // พารามิเตอร์ที่ต้องการ
            // );

            // ส่งอีเมลยืนยัน
            Mail::to($user->email)->send(new UserVerificationMail($user, $verificationUrl));

            // ยืนยันการทำงาน
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            // ยกเลิกการทำงานในกรณีเกิดข้อผิดพลาด
            DB::rollback();

            // ลบข้อมูลผู้ใช้ที่สร้างไว้
            if (isset($user)) {
                $user->roles()->detach(); // ลบความสัมพันธ์ Role
                $user->delete(); // ลบข้อมูลผู้ใช้
            }

            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function users(Request $request)
    {


        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT

        // Validate ข้อมูล
        $validated = $request->validate([
            'userType' => 'nullable|string',
            'prefix' => 'nullable|string',
            'firstName' => 'nullable|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'position' => 'nullable|string',
            'vetId' => 'nullable|string',
            'clinic' => 'nullable|string',
            'avatar' => 'nullable|string',
            'category' => 'array',
            'subCaregory' => 'array',
            'petType' => 'array',
        ]);

        // ค้นหาผู้ใช้ที่ต้องการอัปเดต (สามารถเปลี่ยน logic ได้ เช่น ใช้ Auth::id())
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // อัปเดตข้อมูลทั่วไปของ User
        $user->update([
            'userType' => $validated['userType'],
            'prefix' => $validated['prefix'],
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
            'position' => $validated['position'],
            'vetId' => $validated['vetId'],
            'clinic' => $validated['clinic'],
            'avatar' => $validated['avatar'],
        ]);

        // อัปเดต Many-to-Many Relationships
        if (isset($validated['category'])) {
            $categoryIds = MainCategory::whereIn('name', $validated['category'])->pluck('id')->toArray();
            $user->mainCategories()->sync($categoryIds); // อัปเดตความสัมพันธ์ categories
        }

        if (isset($validated['subCaregory'])) {
            $subCategoryIds = SubCategory::whereIn('name', $validated['subCaregory'])->pluck('id')->toArray();
            $user->subCategories()->sync($subCategoryIds); // อัปเดตความสัมพันธ์ subCategories
        }

        if (isset($validated['petType'])) {
            $animalTypeIds = AnimalType::whereIn('name', $validated['petType'])->pluck('id')->toArray();
            $user->animalTypes()->sync($animalTypeIds); // อัปเดตความสัมพันธ์ animalTypes
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user->load('mainCategories', 'subCategories', 'animalTypes'), // โหลดความสัมพันธ์
        ]);

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


    private function uploadImage($image, $path)
    {
        if ($image) {
            $img = Image::make($image->getRealPath());
            $img->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->stream();

            $filename = time() . '_' . $image->getClientOriginalName();

            Storage::disk('do_spaces')->put(
                "$path/$filename",
                $img->__toString(),
                'public'
            );

            return "https://kimspace2.sgp1.cdn.digitaloceanspaces.com/$path/$filename";
        }

        return null;
    }


    private function deleteOldFile($fileUrl, $path)
    {
        $relativePath = str_replace('https://kimspace2.sgp1.cdn.digitaloceanspaces.com/', '', $fileUrl);
        Storage::disk('do_spaces')->delete($relativePath);
    }



    public function deleteUser(Request $request)
    {
        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // // ลบข้อมูลของผู้ใช้ (รวมถึง Pivot Relationships)
            // $user->roles()->detach(); // ลบความสัมพันธ์ roles
            // $user->mainCategories()->detach(); // ลบความสัมพันธ์ mainCategories
            // $user->subCategories()->detach(); // ลบความสัมพันธ์ subCategories
            // $user->animalTypes()->detach(); // ลบความสัมพันธ์ animalTypes

            // ลบข้อมูลผู้ใช้
            //$user->delete();

            $timestamp = now()->timestamp;
            $newEmail = $user->email . "Delete{$timestamp}";

            $user->update(['email' => $newEmail]);

            return response()->json([
                'success' => true,
                'message' => 'User email updated and relationships cleared successfully',
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function user(Request $request)
{
    try {
        // ตรวจสอบและดึงผู้ใช้จาก JWT
        $user = JWTAuth::parseToken()->authenticate();

        // โหลดข้อมูลความสัมพันธ์ที่จำเป็น
        $user->load(['mainCategories', 'subCategories', 'animalTypes', 'countryDetails']);

        // จัดรูปแบบข้อมูลสำหรับ Response
        // จัดรูปแบบข้อมูลสำหรับ Response
        return response()->json([
            'userType' => $user->userType,
            'prefix' => $user->prefix,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'position' => $user->position,
            'vetId' => $user->vetId,
            'clinic' => $user->clinic,
            'category' => $user->mainCategories->pluck('name'), // ดึงเฉพาะชื่อจาก mainCategories
            'subCaregory' => $user->subCategories->pluck('name'), // ดึงเฉพาะชื่อจาก subCategories
            'petType' => $user->animalTypes->pluck('name'), // ดึงเฉพาะชื่อจาก animalTypes
            'avatar' => $user->avatar,
            'country' => $user->countryDetails ? $user->countryDetails->name : null, // ดึงชื่อประเทศ ถ้ามี
            'flag' => $user->countryDetails ? $user->countryDetails->flag : null,
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
