<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\Role;
use App\Models\CourseAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userType = $request->input('userType');

        try {

            $objs = User::with(['roles', 'countryDetails'])
                ->whereHas('roles', function ($query) {
                    $query->where('roles.id', 3); // เฉพาะ Role ID = 3
                })
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('email', 'LIKE', "%$search%");
                    });
                })
                ->when($userType, function ($query) use ($userType) {
                    $query->where('userType', $userType);
                })
                ->paginate(8);

            return response()->json([
                'success' => true,
                'message' => 'Surveys retrieved successfully.',
                'data' => [
                    'members' => $objs,
                    'search' => $search,
                    'userType' => $userType
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportMembers()
    {
        $fileName = 'members_' . now()->format('Y_m_d_H_i_s') . '.xlsx'; // ตั้งชื่อไฟล์
        return Excel::download(new MembersExport, $fileName);
    }

    public function getUserCourses($userId)
    {
        // ดึงข้อมูล CourseAction และความสัมพันธ์กับ Course
        $courses = CourseAction::where('user_id', $userId)
            ->with('course')
            ->get()
            ->map(function ($course) {
                // ถ้า isFinishCourse = 1 ให้ Pass Rate = 100%
                if ($course->isFinishCourse) {
                    $course->pass_rate = 100; // ตั้งค่าเป็น 100%
                } else {
                    // คำนวณ Pass Rate ปกติ
                    $totalItems = 2; // จำนวนกิจกรรมทั้งหมด (เปลี่ยนได้ตามจริง)
                    $completedItems = 0;

                    if ($course->isFinishVideo) $completedItems++;
                    if ($course->isFinishQuiz) $completedItems++;

                    $course->pass_rate = ($completedItems / $totalItems) * 100; // คำนวณเปอร์เซ็นต์
                }

                return $course;
            });

        $user = User::with(['countryDetails', 'mainCategories', 'subCategories', 'animalTypes'])
            ->findOrFail($userId);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'courses' => $courses
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // ค้นหา User ที่ต้องการแก้ไข
        $user = User::with(['roles', 'countryDetails'])->findOrFail($id);

        // ดึง Role และ Country ทั้งหมด
        $Role = Role::all();
        $countries = Country::all();

        // เตรียมข้อมูลสำหรับ View
        $data = [
            'course' => $user,
            'Role' => $Role,
            'countries' => $countries,
            'url' => url('admin/members/' . $id),
            'method' => 'put',
        ];

        // ส่งข้อมูลไปยัง View
        return view('admin.members.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */


     public function getMemberDetail($id)
    {
        try {
            // ดึงข้อมูลสมาชิกพร้อมความสัมพันธ์ที่เกี่ยวข้อง
            $user = User::with([
                'countryDetails', // ดึงข้อมูลประเทศ
                'mainCategories', // หมวดหมู่หลัก
                'subCategories',  // หมวดหมู่ย่อย
                'animalTypes',    // ประเภทสัตว์
            ])->findOrFail($id); // ถ้าไม่เจอ User จะ throw Exception

            // Response สำเร็จ
            return response()->json([
                'success' => true,
                'message' => 'Member details retrieved successfully.',
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            // Response เมื่อเกิดข้อผิดพลาด
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve member details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function update(Request $request, $id)
{
    // Validate ข้อมูลที่ส่งเข้ามา
    $validated = $request->validate([
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id, // ตรวจสอบอีเมลและต้องไม่ซ้ำกับ user อื่น
        'password' => 'nullable|confirmed|min:8',
        'country' => 'required|exists:countries,id', // ตรวจสอบว่า country มีอยู่ใน database
        'userType' => 'required|string|max:255',
        'avatar_img' => 'nullable|url', // ต้องเป็น URL
    ]);

    DB::beginTransaction();
    try {
        // ค้นหา User
        $user = User::findOrFail($id);

        // อัปเดตข้อมูลทั่วไป
        $user->firstName = $validated['firstName'];
        $user->lastName = $validated['lastName'];
        $user->email = $validated['email']; // อัปเดตอีเมล

        // อัปเดตรหัสผ่านถ้าส่งมา
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // อัปเดตข้อมูล country, userType และ avatar
        $user->country = $validated['country'];
        $user->userType = $validated['userType'];

        // กำหนดค่า avatar เป็น Full URL
        if (!empty($validated['avatar_img'])) {
            $user->avatar = $validated['avatar_img'];
        }

        // บันทึกข้อมูลลง Database
        $user->save();

        DB::commit();

        // Response สำเร็จ
        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully.',
            'data' => $user,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        // Response เมื่อเกิดข้อผิดพลาด
        return response()->json([
            'success' => false,
            'message' => 'Failed to update member.',
            'error' => $e->getMessage(),
        ], 500);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function softDelete($id)
    {
        DB::beginTransaction();

        try {
            // ค้นหา User
            $user = User::findOrFail($id);

            // เปลี่ยนอีเมลของผู้ใช้
            $timestamp = now()->timestamp;
            $newEmail = $user->email . "Deleted{$timestamp}";

            $user->update(['email' => $newEmail]);

            // ตั้งสถานะเป็นลบหรือ Inactive (ถ้าต้องการ)
            $user->update(['status' => 0]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member soft-deleted successfully.',
                'data' => [
                    'original_email' => $user->email,
                    'updated_email' => $newEmail,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to soft-delete member.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleUserStatus($id)
    {
        try {
            // Find the user
            $user = User::findOrFail($id);

            // Toggle the status (0 -> 1, 1 -> 0)
            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
                'data' => [
                    'user_id' => $user->id,
                    'status' => $user->status
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
