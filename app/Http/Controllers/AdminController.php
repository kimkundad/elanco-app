<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // ดึงข้อมูล Users ที่มี Roles และ Countries พร้อมกัน
            $users = User::with(['roles', 'countryDetails']) // เพิ่ม countryDetails เพื่อดึงข้อมูลประเทศ
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'superadmin']);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // เพิ่ม Path ให้ Pagination
            $users->withPath('');

            // Response ข้อมูลสำเร็จ
            return response()->json([
                'success' => true,
                'message' => 'Admin Users retrieved successfully.',
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            // Response เมื่อเกิดข้อผิดพลาด
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Admin Users.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $Role = Role::all();
        $data['Role'] = $Role;
        $countries = Country::all();
        $data['countries'] = $countries;
        $data['method'] = "post";
        $data['url'] = url('admin/adminUser');
        return view('admin.admin.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{


    $validator = Validator::make($request->all(), [
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:8',
        'country' => 'required|exists:countries,id',
        'role_id' => 'required|exists:roles,id', // ตรวจสอบว่า Role มีอยู่ใน database
        'avatar_img' => 'nullable|url',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        // สร้าง User ใหม่   $request->firstName
        $user = new User();
        $user->name = $request->firstName.' '.$request->lastName;
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->country = $request->country;
        $user->status = 1;
        $user->userType = 'admin'; // กำหนด userType เป็น admin

        // กำหนดค่า avatar เป็น Full URL
        if (!empty($request->avatar_img)) {
            $user->avatar = $request->avatar_img;
        }

        $user->save();

        // เพิ่ม Role ให้ User
        $user->roles()->attach($request->role_id);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Admin User created successfully.',
            'data' => $user->load('roles', 'countryDetails'), // ดึงข้อมูล Role กลับมาด้วย
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to create Admin User.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show($id)
        {
            try {
                // ดึงข้อมูลผู้ใช้ด้วย Roles และ Country
                $user = User::with(['roles', 'countryDetails']) // ดึงข้อมูล Roles และ Country
                    ->findOrFail($id);

                return response()->json([
                    'success' => true,
                    'message' => 'Admin User details retrieved successfully.',
                    'data' => $user,
                ], 200);
            } catch (\Exception $e) {
                // หากเกิดข้อผิดพลาด
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve Admin User details.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Validate ข้อมูล

    $validator = Validator::make($request->all(), [
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id, // อีเมลต้องไม่ซ้ำกับผู้ใช้อื่น
        'password' => 'nullable|confirmed|min:8',
        'country' => 'required|exists:countries,id',
        'role_id' => 'required|exists:roles,id', // ตรวจสอบว่า Role มีอยู่ใน database
        'avatar_img' => 'nullable|url',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        // ค้นหา User
        $user = User::findOrFail($id);

        // อัปเดตข้อมูลผู้ใช้
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->email = $request->email;

        // อัปเดตรหัสผ่าน (ถ้าส่งมา)
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->country = $request->country;

        // อัปเดต Avatar
        if (!empty($request->avatar_img)) {
            $user->avatar = $request->avatar_img;
        }

        $user->save();

        // อัปเดต Role
        $user->roles()->sync([$request->role_id]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Admin User updated successfully.',
            'data' => $user->load('roles', 'countryDetails'), // ดึงข้อมูล Role กลับมาด้วย
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to update Admin User.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // ค้นหา User
            $user = User::findOrFail($id);

            // ลบความสัมพันธ์กับ Role
            $user->roles()->detach();

            // ลบ User
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin User deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Admin User.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
