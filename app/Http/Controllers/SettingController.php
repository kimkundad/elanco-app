<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\AnimalType;
use App\Models\quiz;
use App\Models\Survey;
use App\Models\Role;
use App\Models\course;
use App\Models\User;
use App\Models\CourseAction;
use Illuminate\Support\Carbon;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getCountry(){

        $countries = Country::all();

        $response = [
            'countries' => $countries
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }

    public function overView(Request $request)
    {
        try {


            // รับพารามิเตอร์ start_date และ end_date
            $startDate = $request->input('start_date', today()->subDays(7)->toDateString()); // ค่าเริ่มต้น: 7 วันที่ผ่านมา
            $endDate = $request->input('end_date', today()->toDateString()); // ค่าเริ่มต้น: วันนี้

            // ตรวจสอบรูปแบบวันที่ (optional)
            if (!strtotime($startDate) || !strtotime($endDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Please use YYYY-MM-DD.',
                ], 400);
            }

            // คำนวณจำนวน Active Users ในช่วง Date Range
            $activeUsersByDate = collect(Carbon::parse($startDate)->daysUntil($endDate))->map(function ($date) {
                return [
                    'date' => $date->toDateString(),
                    'count' => User::whereDate('last_active_at', $date->toDateString())->count(),
                ];
            });


            // Active Users Today
            $activeUsersToday = User::whereDate('last_active_at', today())->count();

            // Active Users Yesterday
            // จำนวนผู้ใช้งานวันนี้
            $activeUsersToday = User::whereDate('last_active_at', today())->count();

            // เปรียบเทียบกับเมื่อวาน
            $activeUsersYesterday = User::whereDate('last_active_at', today()->subDay())->count();
            $percentageChange = $activeUsersYesterday > 0
                ? round((($activeUsersToday - $activeUsersYesterday) / $activeUsersYesterday) * 100, 2)
                : ($activeUsersToday > 0 ? 100 : 0);

            // ดึงข้อมูลสำหรับกราฟ (7 วันที่ผ่านมา)
            $activeUsersLast7Days = collect(range(0, 6))->map(function ($day) {
                return [
                    'date' => now()->subDays($day)->toDateString(),
                    'count' => User::whereDate('last_active_at', now()->subDays($day)->toDateString())->count(),
                ];
            })->reverse(); // เรียงลำดับจากวันเก่ามากไปวันล่าสุด

            // Total Registrations, All Courses, Learning, Complete
            $totalRegistrations = CourseAction::count();
            $totalCourses = Course::where('status', 1)->count();
            $totalLearning = CourseAction::where('isFinishCourse', 0)->count(); // กำลังเรียน
            $totalComplete = CourseAction::where('isFinishCourse', 1)->count(); // เรียนจบ

            // Most Popular Categories
            $popularCategories = MainCategory::with(['courses' => function ($query) {
                $query->withCount('courseActions'); // นับการลงทะเบียนในแต่ละคอร์ส
            }])
            ->get()
            ->map(function ($category) use ($totalRegistrations) {
                // รวมจำนวนการลงทะเบียนในคอร์สทั้งหมดที่เกี่ยวข้องกับ MainCategory
                $categoryRegistrations = $category->courses->sum('course_actions_count');

                return [
                    'name' => $category->name,
                    'percentage' => $totalRegistrations > 0
                        ? round(($categoryRegistrations / $totalRegistrations) * 100, 2) // คำนวณเปอร์เซ็นต์
                        : 0,
                ];
            });

            // Most Popular Courses
            $popularCourses = course::withCount('courseActions')
                ->orderBy('course_actions_count', 'desc')
                ->take(10)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->course_title,
                        'image' => $course->course_img,
                    ];
                });

            // Response Data
            $data = [
                'active_users_today' => $activeUsersToday,
                'percentage_change' => $percentageChange,
                'data_percentage_change' => 'data_percentage_change เปรียบเทียบเปอร์เซ็นต์ระหว่างวันนี้และเมื่อวาน ของยอดคนเข้าใช้งาน',
                'active_users_by_date' => $activeUsersByDate,
                'stats' => [
                    'total_registrations' => $totalRegistrations,
                    'all_courses' => $totalCourses,
                    'learning' => $totalLearning,
                    'complete' => $totalComplete,
                ],
                'most_popular_categories' => $popularCategories,
                'most_popular_courses' => $popularCourses,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve overview data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }





    public function getRole(){

        $Role = Role::all();

        $response = [
            'Role' => $Role
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }



    public function getMainCategory(){

        $mainCategories = MainCategory::all();

        $response = [
            'mainCategories' => $mainCategories
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }

    public function getSubCategory(){

        $subCategories = SubCategory::all();

        $response = [
            'subCategories' => $subCategories
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }


    public function getAnimalType(){

        $animalTypes = AnimalType::all();

        $response = [
            'animalTypes' => $animalTypes
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }


    public function getQuiz(){

        $quiz = Quiz::all();

        $response = [
            'quiz' => $quiz
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }

    public function getSurvey(){

        $survey = Survey::all();

        $response = [
            'survey' => $survey
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }


    public function getItemForCourse(){

        $countries = Country::all();
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $animalTypes = AnimalType::all();
        $quiz = Quiz::all();
        $survey = Survey::all();

        $response = [
            'countries' => $countries,
            'mainCategories' => $mainCategories,
            'subCategories' => $subCategories,
            'animalTypes' => $animalTypes,
            'quiz' => $quiz,
            'survey' => $survey,
        ];

        // ส่งออกข้อมูลในรูปแบบ JSON
        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);

    }

    public function upPicUrl(Request $request)
    {
        try {
            // ตรวจสอบว่า `course_img` มีอยู่และเป็นไฟล์ภาพ
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8048', // รองรับเฉพาะไฟล์ภาพ
            ]);

            // อัปโหลดภาพและรับ URL
            $filename = $this->uploadImage($request->file('image'), 'elanco/editor');

            // ตรวจสอบว่าอัปโหลดสำเร็จหรือไม่
            if ($filename) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully.',
                    'url' => $filename,
                ], 200);
            }

            // กรณีไม่มีไฟล์ที่อัปโหลด
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image.',
            ], 400);

        } catch (\Exception $e) {
            // กรณีเกิดข้อผิดพลาด
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during image upload.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function uploadImage($image, $path)
    {
        try {
            if ($image) {
                // ใช้ Intervention Image เพื่อปรับขนาดภาพ
                $img = Image::make($image->getRealPath());
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->stream();

                // สร้างชื่อไฟล์ใหม่
                $filename = time() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());

                // อัปโหลดไปยัง DigitalOcean Spaces
                Storage::disk('do_spaces')->put(
                    "$path/$filename",
                    $img->__toString(),
                    'public'
                );

                // คืน URL เต็ม
                return "https://kimspace2.sgp1.cdn.digitaloceanspaces.com/$path/$filename";
            }
            return null;

        } catch (\Exception $e) {
            // กรณีเกิดข้อผิดพลาด
            return null;
        }
    }


    public function index()
    {
        //
        return view('admin.setting.index');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
