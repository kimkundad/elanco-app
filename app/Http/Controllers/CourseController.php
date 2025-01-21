<?php

namespace App\Http\Controllers;

use App\Exports\CourseReviewExport;
use Illuminate\Http\Request;
use App\Models\course;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\AnimalType;
use App\Models\quiz;
use App\Models\itemDes;
use App\Models\Survey;
use App\Models\Speaker;
use App\Models\Referance;
use App\Models\CourseAction;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // dd($request->all());
            // Retrieve search query
            $search = $request->input('search');

            // Query courses with search filter
            $objs = course::with(['countries', 'mainCategories', 'subCategories', 'animalTypes', 'quiz'])
                ->withCount(['courseActions as enrolled_count' => function ($query) {
                    $query->where('isFinishCourse', true);
                }])
                ->when($search, function ($query, $search) {
                    $query->where('course_title', 'like', '%' . $search . '%')
                        ->orWhere('course_preview', 'like', '%' . $search . '%');
                })
                ->paginate(10);

            // Return JSON response with course data
            return response()->json([
                'success' => true,
                'message' => 'Courses retrieved successfully',
                'data' => $objs,
            ], 200);
        } catch (\Exception $e) {
            // จัดการข้อผิดพลาดทั่วไป
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            $course = course::with(['quiz', 'countries', 'mainCategories', 'referances', 'Speaker'])
                ->findOrFail($id);

            // ดึงจำนวนผู้ที่ลงทะเบียนทั้งหมด
            $totalEnrolled = CourseAction::where('course_id', $id)->count();

            // ดึงจำนวนผู้ที่สำเร็จคอร์ส (isFinishCourse = 1)
            $totalCompleted = CourseAction::where('course_id', $id)
                ->where('isFinishCourse', 1)
                ->count();

            // คิดเป็นเปอร์เซ็นต์การสำเร็จ
            $completionPercentage = $totalEnrolled > 0
                ? round(($totalCompleted / $totalEnrolled) * 100, 2)
                : 0;

            // ดึงข้อมูลสำหรับกราฟหรือรายงานเพิ่มเติม (เช่น สถิติการลงทะเบียนรายเดือน)
            $enrolledStats = [
                'oct' => 80,
                'mar' => 60,
                'aug' => 40,
            ];

            return response()->json([
                'success' => true,
                'course' => $course,
                'enrolledStats' => $enrolledStats,
                'totalEnrolled' => $totalEnrolled,
                'totalCompleted' => $totalCompleted,
                'completionPercentage' => $completionPercentage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found!',
            ], 404);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            // ค้นหาคอร์สจาก id
            $course = course::findOrFail($id);

            // อัปเดตสถานะ
            $course->status = $request->input('status') ? 1 : 0;
            $course->save();

            return response()->json([
                'success' => true,
                'status' => $course->status,
                'message' => 'Course status updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating course status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $quiz = Quiz::all();
        $survey = Survey::all();
        $countries = Country::all();
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $animalTypes = AnimalType::all();

        $data = [
            'quiz' => $quiz,
            'survey' => $survey,
            'countries' => $countries,
            'mainCategories' => $mainCategories,
            'subCategories' => $subCategories,
            'animalTypes' => $animalTypes,
        ];

        return view('admin2.course.create', $data);
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

    private function uploadFile($file, $path)
    {
        if ($file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            Storage::disk('do_spaces')->put(
                "$path/$filename",
                file_get_contents($file->getRealPath()),
                'public'
            );

            return "https://kimspace2.sgp1.cdn.digitaloceanspaces.com/$path/$filename";
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // $user = $request->user();
        // return response()->json([
        //     'user' => $user,
        //     'roles' => $user ? $user->roles->pluck('name') : null,
        // ], 200);

        // dd($request->all());
        // Validate Request Input
        $validator = Validator::make($request->all(), [
            'course_title' => 'required|string|max:255',
            'course_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'course_preview' => 'required|string',
            'status' => 'nullable|integer',
            'duration' => 'required|string',
            'url_video' => 'required|url',
            'id_quiz' => 'nullable|integer',
            'survey_id' => 'nullable|integer',
            'itemDes' => 'nullable|array',
            'itemDes.*' => 'nullable|string|max:255',
            'reference_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'file_product' => 'nullable|file|max:5048',
            'speaker_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'file_speaker' => 'nullable|file|max:5048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $totalCourses = course::count();
            $nextCourseNumber = $totalCourses + 1;

            // Save Course
            $filename = $this->uploadImage($request->file('course_img'), 'elanco/course');
            $course = new course();
            $course->course_title = $request->course_title;
            $course->course_img = $filename
                ? 'https://kimspace2.sgp1.cdn.digitaloceanspaces.com/elanco/course/' . $filename
                : null;
            $course->course_preview = $request->course_preview;
            $course->course_description = $request->course_description;
            $course->status = $request->status ?? 0;
            $course->duration = $request->duration;
            $course->url_video = $request->url_video;
            $course->id_quiz = $request->id_quiz;
            $course->survey_id = $request->survey_id;
            $course->created_by = Auth::id();
            $course->course_id = 'C' . str_pad($nextCourseNumber, 3, '0', STR_PAD_LEFT);
            $course->save();

            // Save ItemDes
            if ($request->has('itemDes')) {
                foreach ($request->itemDes as $choice) {
                    if (!is_null($choice)) {
                        $itemDes = new itemDes();
                        $itemDes->course_id = $course->id;
                        $itemDes->detail = $choice;
                        $itemDes->save();
                    }
                }
            }

            // Save Speaker
            if ($request->has('speaker_name')) {
                $speakerAvatar = $this->uploadImage($request->file('speaker_img'), 'elanco/speaker');
                $speakerFile = $this->uploadFile($request->file('file_speaker'), 'elanco/speaker');

                $speaker = new Speaker();
                $speaker->course_id = $course->id;
                $speaker->name = $request->speaker_name;
                $speaker->avatar = $speakerAvatar;
                $speaker->job_position = $request->speaker_job;
                $speaker->country = $request->speaker_country;
                $speaker->file = $speakerFile;
                $speaker->description = $request->speaker_background;
                $speaker->save();
            }

            // Save Reference
            if ($request->has('product_name')) {
                $referenceImg = $this->uploadImage($request->file('reference_img'), 'elanco/Referance');
                $referenceFile = $this->uploadFile($request->file('file_product'), 'elanco/Referance');

                $referance = new Referance();
                $referance->course_id = $course->id;
                $referance->title = $request->product_name;
                $referance->image = $referenceImg;
                $referance->file = $referenceFile;
                $referance->description = $request->reference_detail;
                $referance->save();
            }

            // Save Relations
            if ($request->has('countries')) {
                $course->countries()->attach($request->countries);
            }

            if ($request->has('main_categories')) {
                $course->mainCategories()->attach($request->main_categories);
            }

            if ($request->has('sub_categories')) {
                $course->subCategories()->attach($request->sub_categories);
            }

            if ($request->has('animal_types')) {
                $course->animalTypes()->attach($request->animal_types);
            }

            DB::commit();

            // Response
            return response()->json([
                'status' => true,
                'message' => 'Course created successfully.',
                'data' => $course
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            // Error Response
            return response()->json([
                'status' => false,
                'message' => 'Failed to create course.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function courseReview(Request $request, string $id)
    {
        try {
            $search = $request->input('search'); // คำค้นหา
            $ratingFilter = $request->input('rating'); // ค่ากรอง Rating (1 - 5)

            // ดึงข้อมูล Rating จาก courseActions
            $ratings = CourseAction::with(['user.countryDetails']) // ดึงข้อมูล User และประเทศ
            ->where('course_id', $id)
                ->when($search, function ($query, $search) {
                    // ค้นหาจากชื่อผู้ใช้ (firstName, lastName) หรืออีเมล
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('firstName', 'LIKE', "%$search%")
                            ->orWhere('lastName', 'LIKE', "%$search%")
                            ->orWhere('email', 'LIKE', "%$search%");
                    });
                })
                ->when($ratingFilter, function ($query, $ratingFilter) {
                    // กรอง Rating ตามค่าที่ส่งมา
                    $query->where('rating', $ratingFilter);
                })
                ->paginate(10); // แบ่งหน้า

            // คำนวณค่าเฉลี่ย Rating และจำนวน Review ทั้งหมด
            $averageRating = CourseAction::where('course_id', $id)->avg('rating');
            $totalReviews = CourseAction::where('course_id', $id)->count();

            // สร้าง Response
            $response = [
                'course_id' => $id,
                'average_rating' => round($averageRating, 1), // ค่าเฉลี่ย Rating
                'total_reviews' => $totalReviews, // จำนวนรีวิวทั้งหมด
                'ratings' => $ratings->map(function ($rating) {
                    return [
                        'name' => $rating->user ? $rating->user->firstName . ' ' . $rating->user->lastName : null,
                        'email' => $rating->user ? $rating->user->email : null,
                        'clinicName' => $rating->user ? $rating->user->clinic : null,
                        'rating' => $rating->rating,
                        'timestamp' => $rating->updated_at->format('d M Y | h:i A'),
                        'country' => $rating->user && $rating->user->countryDetails ? $rating->user->countryDetails->name : null,
                        'countryImg' => $rating->user && $rating->user->countryDetails ? $rating->user->countryDetails->img : null,
                        'userType' => $rating->user ? $rating->user->userType : null,
                    ];
                }),
                'pagination' => [
                    'current_page' => $ratings->currentPage(),
                    'total_pages' => $ratings->lastPage(),
                    'per_page' => $ratings->perPage(),
                    'total' => $ratings->total(),
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'Course reviews retrieved successfully.',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve course reviews.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export system logs to a CSV file.
     */
    public function exportCourseReview($id)
    {
        $fileName = 'course_reviews_' . $id . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new CourseReviewExport($id), $fileName);
    }

    public function show(Request $request, string $id)
    {
        try {

            $startDate = $request->input('start_date'); // วันที่เริ่มต้น
            $endDate = $request->input('end_date');    // วันที่สิ้นสุด

            // ดึงข้อมูลคอร์สและความสัมพันธ์ที่เกี่ยวข้อง
            $course = course::with([
                'countries',
                'mainCategories',
                'subCategories',
                'animalTypes',
                'itemDes',
                'referances',
                'Speaker',
                'Speaker.countryDetails',
                'creator'
            ])->findOrFail($id);

            // ข้อมูลเพิ่มเติมที่ต้องการคำนวณ
            $totalEnrolled = $course->courseActions()->count(); // จำนวนผู้ลงทะเบียนทั้งหมด
            $completedEnrolled = $course->courseActions()->where('isFinishCourse', 1)->count(); // จำนวนผู้เรียนจบ
            $rating = $course->courseActions()->avg('rating'); // คะแนนเฉลี่ย
            $ratingCount = $course->courseActions()->where('rating', '!=', 0)->count();
            $passedCount = $course->courseActions()->where('isFinishQuiz', 1)->count(); // จำนวนผู้ที่ผ่าน
            $userMakeQuizCount = $course->courseActions()->where('isFinishVideo', 1)->count(); // จำนวนผู้ทำข้อสอบ
            $surveySummit = $course->survey ? $course->survey->responses()->count() : 0; // จำนวนผู้ที่ตอบ Survey
            $course->ce_point = $course->quiz->point_cpd;
            // คำนวณเปอร์เซ็นต์
            $completePercentage = $totalEnrolled > 0 ? round(($completedEnrolled / $totalEnrolled) * 100) : 0;
            $passedPercentage = $totalEnrolled > 0 ? round(($passedCount / $userMakeQuizCount) * 100) : 0;
            $surveyPercentage = $totalEnrolled > 0 ? round(($surveySummit / $totalEnrolled) * 100) : 0;

            // กราฟแท่งแสดงจำนวนผู้ลงทะเบียนในแต่ละเดือน โดยมีการใช้ Date Range
            $enrollmentQuery = $course->courseActions()
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month');

            // เพิ่มการกรองตาม Date Range
            if ($startDate && $endDate) {
                $enrollmentQuery->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }

            $enrollmentReport = $enrollmentQuery->get()
                ->mapWithKeys(function ($row) {
                    return [date('M', mktime(0, 0, 0, $row->month, 1)) => $row->count];
                });

            // สร้าง Response Data
            $response = [
                'course' => $course,
                'stats' => [
                    'complete_percentage' => $completePercentage,
                    'completed' => $completedEnrolled,
                    'total_enrolled' => $totalEnrolled,
                    'rating' => round($rating, 1),
                    'rating_count' => $ratingCount,
                    'passed_percentage' => $passedPercentage,
                    'passed_count' => $passedCount,
                    'survey_summit' => $surveySummit,
                    'survey_percentage' => $surveyPercentage,
                    'userMakeQuizCount' => $userMakeQuizCount
                ],
                'enrollment_report' => $enrollmentReport,
                'created_by' => $course->creator ? [
                    'firstName' => $course->creator->firstName,
                    'lastName' => $course->creator->lastName,
                ] : null,
            ];

            // ส่งออกข้อมูลในรูปแบบ JSON
            return response()->json([
                'success' => true,
                'message' => 'Course data retrieved successfully.',
                'data' => $response
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // กรณีไม่พบคอร์ส
            return response()->json([
                'success' => false,
                'message' => 'Course not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // กรณีเกิดข้อผิดพลาดอื่น ๆ
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving course data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // ค้นหาคอร์สที่ต้องการแก้ไข
        $course = course::with(['countries', 'mainCategories', 'subCategories', 'animalTypes', 'itemDes', 'referances'])->findOrFail($id);

        // เตรียมข้อมูลที่จำเป็นสำหรับการแสดงผล
        $data['course'] = $course;
        $data['countries'] = Country::all(); // ดึงรายชื่อประเทศทั้งหมด
        $data['mainCategories'] = MainCategory::all(); // ดึงหมวดหมู่หลักทั้งหมด
        $data['subCategories'] = SubCategory::all(); // ดึงหมวดหมู่ย่อยทั้งหมด
        $data['animalTypes'] = AnimalType::all(); // ดึงประเภทสัตว์ทั้งหมด
        $data['quiz'] = Quiz::all();
        $data['survey'] = Survey::all();

        // URL และ Method สำหรับฟอร์มแก้ไข
        $data['url'] = url('admin/course/' . $id);
        $data['method'] = "put";

        // ส่งข้อมูลไปยัง View
        return view('admin.course.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        //dd($request->all());
        // Validation

        $validator = Validator::make($request->all(), [
            'course_title' => 'nullable|string|max:255',
            'course_preview' => 'nullable|string',
            'status' => 'nullable|integer',
            'duration' => 'nullable|string',
            'url_video' => 'nullable|url',
            'id_quiz' => 'nullable|integer',
            'survey_id' => 'nullable|integer',
            'itemDes' => 'nullable|array',
            'itemDes.*' => 'nullable|string|max:255',
            'reference_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'file_product' => 'nullable|file|max:5048',
            'speaker_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'file_speaker' => 'nullable|file|max:5048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $filename = null;
        //  dd($request->all());

        DB::beginTransaction();
        try {
            // ค้นหา course ที่ต้องการอัปเดต
            $course = course::findOrFail($id);
            //  dd($course);

            // ถ้ามีไฟล์รูป ให้ดำเนินการอัปโหลดใหม่
            if ($request->hasFile('course_img')) {
                $image = $request->file('course_img');
                $img = Image::make($image->getRealPath());
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->stream(); // Prepare image for upload

                // Generate a unique filename
                $filename = time() . '_' . $image->getClientOriginalName();

                // Upload the image to DigitalOcean Spaces
                Storage::disk('do_spaces')->put(
                    'elanco/course/' . $filename,
                    $img->__toString(),
                    'public'
                );

                // ลบรูปเดิม (ถ้ามี)
                if ($course->course_img) {
                    $oldImagePath = str_replace('https://kimspace2.sgp1.cdn.digitaloceanspaces.com/', '', $course->course_img);
                    Storage::disk('do_spaces')->delete($oldImagePath);
                }

                // อัปเดตรูปภาพใหม่
                $course->course_img = 'https://kimspace2.sgp1.cdn.digitaloceanspaces.com/elanco/course/' . $filename;
            }

            // อัปเดตข้อมูลทั่วไป
            $course->course_title = $request->course_title;
            $course->course_preview = $request->course_preview;
            $course->status = $request->status ?? 0;
            $course->duration = $request->duration;
            $course->url_video = $request->url_video;
            $course->id_quiz = $request->id_quiz;
            $course->survey_id = $request->survey_id;
            $course->course_description = $request->course_description;
            $course->save();

            $course->itemDes()->delete(); // ลบรายการเดิม
            if ($request->has('item_des')) {
                foreach ($request->item_des as $choice) {
                    if (!is_null($choice)) {
                        $itemDes = new ItemDes();
                        $itemDes->course_id = $course->id;
                        $itemDes->detail = $choice;
                        $itemDes->save();
                    }
                }
            }

            // **อัปเดต Speaker**
            if ($request->has('speaker_name')) {
                $speaker = $course->Speaker()->first() ?? new Speaker();

                // ตรวจสอบและอัปโหลดรูปภาพ (ถ้ามี)
                $speakerAvatar = $speaker->avatar; // ใช้ค่าปัจจุบันก่อน
                if ($request->hasFile('speaker_img')) {
                    $speakerAvatar = $this->uploadImage($request->file('speaker_img'), 'elanco/speaker');

                    // ลบรูปภาพเก่า (ถ้ามี)
                    if ($speaker->avatar) {
                        $this->deleteOldFile($speaker->avatar, 'elanco/speaker');
                    }
                }

                // ตรวจสอบและอัปโหลดไฟล์ (ถ้ามี)
                $speakerFile = $speaker->file; // ใช้ค่าปัจจุบันก่อน
                if ($request->hasFile('file_speaker')) {
                    $speakerFile = $this->uploadFile($request->file('file_speaker'), 'elanco/speaker');

                    // ลบไฟล์เก่า (ถ้ามี)
                    if ($speaker->file) {
                        $this->deleteOldFile($speaker->file, 'elanco/speaker');
                    }
                }

                $speaker->course_id = $course->id;
                $speaker->name = $request->speaker_name;
                $speaker->avatar = $speakerAvatar;
                $speaker->job_position = $request->speaker_job;
                $speaker->country = $request->speaker_country;
                $speaker->file = $speakerFile;
                $speaker->description = $request->speaker_background;
                $speaker->save();
            }

            // **อัปเดต Referance**
            if ($request->has('product_name')) {
                $referance = $course->referances()->first() ?? new Referance();

                // ตรวจสอบและอัปโหลดรูปภาพ (ถ้ามี)
                $referenceImg = $referance->image; // ใช้ค่าปัจจุบันก่อน
                if ($request->hasFile('reference_img')) {
                    $referenceImg = $this->uploadImage($request->file('reference_img'), 'elanco/Referance');

                    // ลบรูปภาพเก่า (ถ้ามี)
                    if ($referance->image) {
                        $this->deleteOldFile($referance->image, 'elanco/Referance');
                    }
                }

                // ตรวจสอบและอัปโหลดไฟล์ (ถ้ามี)
                $referenceFile = $referance->file; // ใช้ค่าปัจจุบันก่อน
                if ($request->hasFile('file_product')) {
                    $referenceFile = $this->uploadFile($request->file('file_product'), 'elanco/Referance');

                    // ลบไฟล์เก่า (ถ้ามี)
                    if ($referance->file) {
                        $this->deleteOldFile($referance->file, 'elanco/Referance');
                    }
                }

                // อัปเดตข้อมูล Referance
                $referance->course_id = $course->id;
                $referance->title = $request->product_name;
                $referance->image = $referenceImg;
                $referance->file = $referenceFile;
                $referance->description = $request->reference_detail;
                $referance->save();
            }

            // อัปเดตความสัมพันธ์ใน Pivot Tables
            $course->countries()->sync($request->countries ?? []);
            $course->mainCategories()->sync($request->main_categories ?? []);
            $course->subCategories()->sync($request->sub_categories ?? []);
            $course->animalTypes()->sync($request->animal_types ?? []);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Course Update successfully.',
                'data' => $course
            ], 201);


        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->withErrors(['database' => 'Failed to update course data: ' . $e->getMessage()]);
        }
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
        try {
            // ค้นหา Course จาก ID
            $course = course::findOrFail($id);

            // ลบไฟล์รูปภาพที่เกี่ยวข้อง (หากมี)
            if ($course->course_img) {
                $imagePath = str_replace('https://kimspace2.sgp1.cdn.digitaloceanspaces.com/', '', $course->course_img);
                Storage::disk('s3')->delete($imagePath);
            }

            // ลบความสัมพันธ์ในตาราง Pivot
            $course->countries()->detach();
            $course->mainCategories()->detach();
            $course->subCategories()->detach();
            $course->animalTypes()->detach();

            // ลบข้อมูลในตารางที่เกี่ยวข้อง (เช่น itemDes, Speaker, Referance)
            $course->itemDes()->delete();
            $course->Speaker()->delete();
            $course->referances()->delete();

            // ลบ Course
            $course->delete();

            return response()->json([
                'status' => true,
                'message' => 'Course deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete course.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function courseStatus($id)
    {
        try {
            // Find the course by its ID
            $course = course::findOrFail($id);

            // Toggle the status (if 1, set to 0; if 0, set to 1)
            $course->status = $course->status == 1 ? 0 : 1;

            // Save the updated status
            $course->save();

            return response()->json([
                'success' => true,
                'message' => 'Course status updated successfully.',
                'data' => [
                    'course_id' => $course->id,
                    'status' => $course->status // Return the new status
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update course status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
