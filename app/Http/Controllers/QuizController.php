<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\quiz;

use App\Models\answer;
use App\Models\question;
use App\Models\QuizAttempt;
use App\Models\course;
use App\Models\CourseAction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $search = $request->input('search');

        try {

            $objs = Quiz::with([
                'countries' => function ($query) {
                    $query->select('countries.id', 'countries.name', 'countries.flag', 'countries.img'); // เลือกเฉพาะฟิลด์ที่ต้องการ
                }
            ])
            ->withCount([
                'courses as courses_link', // นับจำนวนคอร์สที่ใช้ Quiz
                'quizAttempts as Submitted' => function ($query) {
                    $query->select(DB::raw('count(distinct user_id)')); // นับจำนวนผู้ทำ (Distinct user_id)
                }
            ])
            ->when($search, function ($query, $search) {
                    $query->where('quiz_id', 'LIKE', "%$search%") // ค้นหารหัส Quiz
                  ->orWhere('questions_title', 'LIKE', "%$search%") // ค้นหาชื่อหัวข้อคำถาม
                  ->orWhere('expire_date', 'LIKE', "%$search%"); // ค้นหาวันหมดอายุ
            })
            ->paginate(10); // จำนวนรายการต่อหน้า

      //  dd($objs);

        return response()->json([
            'success' => true,
            'message' => 'Quiz retrieved successfully',
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        return view('admin.quiz.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            'expire_date' => 'nullable|date',
            'questions_title' => 'required|string|max:255',
            'pass_percentage' => 'required|integer|min:0|max:100',
            'certificate' => 'required|boolean',
            'point_cpd' => 'nullable|integer',
            'code_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Save data to database

        try {

            $totalQuizzes = Quiz::count();
            $nextQuizNumber = $totalQuizzes + 1;

           $objs = new quiz();
           $objs->quiz_id = 'Q' . str_pad($nextQuizNumber, 3, '0', STR_PAD_LEFT);
           $objs->expire_date = $request->expire_date;
           $objs->questions_title = $request->questions_title;
           $objs->pass_percentage = $request->pass_percentage;
           $objs->certificate = $request->certificate === 'Yes' ? true : false;
           $objs->point_cpd = $request->point_cpd;
           $objs->code_number = $request->code_number;
           $objs->created_by = Auth::id(); // เพิ่ม ID ของผู้สร้าง
           $objs->save();

           return response()->json([
            'status' => true,
            'message' => 'Quiz created successfully.',
            'data' => $objs
        ], 201);


    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['database' => 'Failed to save course data: ' . $e->getMessage()]);
    }

    }

    /**
     * Display the specified resource.
     */

    public function questionID(string $id){

        try {
            // ดึงข้อมูล Question พร้อมคำตอบ (Answers)
            $question = Question::with('answers:id,questions_id,answers,answers_status') // เลือกฟิลด์ที่ต้องการจาก answers
                ->findOrFail($id); // ถ้าไม่เจอ Question จะส่ง 404

            // ส่งข้อมูลกลับในรูปแบบ JSON
            return response()->json([
                'success' => true,
                'message' => 'Question details retrieved successfully.',
                'data' => $question,
            ], 200);
        } catch (\Exception $e) {
            // จัดการข้อผิดพลาดทั่วไป
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve question details.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function show(Request $request, string $id)
    {
        try {
            $startDate = $request->input('start_date'); // วันที่เริ่มต้น
            $endDate = $request->input('end_date');    // วันที่สิ้นสุด

            // ดึงข้อมูล Quiz พร้อม Question, Answer, และ Courses ที่เกี่ยวข้อง
            $quiz = Quiz::with([
                'questions.answers' => function ($query) {
                    $query->select('id', 'questions_id', 'answers', 'answers_status'); // เลือกเฉพาะฟิลด์ที่ต้องการ
                },
                'courses.animalTypes', // ดึง animalTypes ผ่าน courses
                'creator' // ดึง creator
            ])->findOrFail($id); // ถ้าไม่เจอ Quiz จะส่ง 404

            // คำนวณจำนวนคำถามทั้งหมด
            $totalQuestions = $quiz->questions->count();

            // คำนวณจำนวนคนที่ทำ Quiz ทั้งหมด
            $userMakeQuizCount = $quiz->courses->flatMap(function ($course) {
                return $course->courseActions()->where('isFinishVideo', 1)->pluck('user_id');
            })->unique()->count();

            // คำนวณจำนวนคนที่ผ่าน Quiz
            $passedCount = $quiz->courses->flatMap(function ($course) {
                return $course->courseActions()->where('isFinishQuiz', 1)->pluck('user_id');
            })->unique()->count();

            // คำนวณเปอร์เซ็นต์คนที่ผ่าน
            $passedPercentage = $userMakeQuizCount > 0 ? round(($passedCount / $userMakeQuizCount) * 100) : 0;

            // คำนวณจำนวนคนที่ได้รับ Certificate
            $certificateReceivedCount = $quiz->courses->flatMap(function ($course) {
                return $course->courseActions()->where('isFinishCourse', 1)->pluck('user_id');
            })->unique()->count();

            // ข้อมูลสำหรับกราฟแท่ง: จำนวนคนทำ Quiz ในแต่ละเดือน (6 เดือนล่าสุดหรือในช่วง Date Range)
            $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->subMonths(6)->startOfMonth();
            $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfMonth();

            $quizActivityQuery = CourseAction::whereIn('course_id', $quiz->courses->pluck('id'))
                ->where('isFinishQuiz', 1) // เฉพาะคนที่ทำ Quiz เสร็จ
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month');

            // เพิ่ม Date Range ในการกรองข้อมูล
            if ($startDate && $endDate) {
                $quizActivityQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $quizActivity = $quizActivityQuery->get()
                ->mapWithKeys(function ($row) {
                    return [date('M', mktime(0, 0, 0, $row->month, 1)) => $row->count];
                });


                // ดึง Courses ที่เกี่ยวข้องกับ Quiz นี้
                // ดึง Courses ที่เกี่ยวข้องกับ Quiz นี้
$courses = $quiz->courses->map(function ($course) {
    // ดึงจำนวนผู้ลงทะเบียนทั้งหมด
    $totalEnrolled = $course->courseActions()->count();

    // ดึงจำนวนคนที่เรียนจบ (isFinishCourse = 1)
    $completedCount = $course->courseActions()->where('isFinishCourse', 1)->count();

    // คำนวณเปอร์เซ็นต์การเรียนจบ
    $completedPercentage = $totalEnrolled > 0 ? round(($completedCount / $totalEnrolled) * 100) : 0;

    // ดึงคะแนน Rating
    $rating = number_format($course->ratting, 1);

    // ดึงวันที่ล่าสุดที่มีคนเรียนจบ
    $lastCompletionDate = $course->courseActions()
        ->where('isFinishCourse', 1)
        ->latest('updated_at')
        ->value('updated_at');

    return [
        'course_id' => $course->course_id,
        'course_title' => $course->course_title,
        'course_description' => $course->course_description,
        'rating' => $rating, // คะแนน Rating
        'total_enrolled' => $totalEnrolled, // จำนวนผู้ลงทะเบียนทั้งหมด
        'completed_percentage' => $completedPercentage, // % ของคนที่เรียนจบ
        'last_completion_date' => $lastCompletionDate ? $lastCompletionDate->format('d M Y') : null, // วันที่ล่าสุดที่มีคนเรียนจบ
        'countries' => $course->countries->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
            ];
        }),
        'mainCategories' => $course->mainCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }),
    ];
});


            // ส่งข้อมูลกลับในรูปแบบ JSON
            return response()->json([
                'success' => true,
                'message' => 'Quiz details retrieved successfully.',
                'data' => [
                    'quiz' => $quiz,
                    'animal_types' => $quiz->courses->flatMap(function ($course) {
                        return $course->animalTypes;
                    })->unique('id')->values(), // รวม animalTypes ที่เกี่ยวข้องและลบซ้ำ
                    'total_questions' => $totalQuestions,
                    'user_make_quiz_count' => $userMakeQuizCount,
                    'passed_count' => $passedCount,
                    'passed_percentage' => $passedPercentage,
                    'certificate_received_count' => $certificateReceivedCount,
                    'quiz_activity_chart' => $quizActivity, // ข้อมูลสำหรับกราฟแท่ง
                    'course_link' => $courses,
                ],
            ], 200);
        } catch (\Exception $e) {
            // จัดการข้อผิดพลาด
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve quiz details.',
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
        $quiz = question::with('answers')->where('quiz_id', $id)->get();
       // dd($quiz);
       $data['quiz'] = $quiz;
        $objs = quiz::find($id);
        $data['url'] = url('admin/quiz/'.$id);
        $data['method'] = "put";
        $data['objs'] = $objs;
        return view('admin.quiz.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */


     public function questionUpdate(Request $request, string $id)
    {


        $request->validate([
            'detail' => 'required|string',
            'answers' => 'nullable|array',
            'answers.*.answers' => 'required|string',
            'answers.*.answers_status' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            // อัปเดตคำถาม
            $question = Question::findOrFail($id);
            $question->detail = $request->input('detail');
            $question->save();

            // ลบคำตอบเดิมทั้งหมด
            Answer::where('questions_id', $id)->delete();

            // เพิ่มคำตอบใหม่
            if ($request->has('answers')) {
                foreach ($request->input('answers') as $answerData) {
                    $newAnswer = new Answer();
                    $newAnswer->questions_id = $id;
                    $newAnswer->answers = $answerData['answers'];
                    $newAnswer->answers_status = $answerData['answers_status'] ?? 0;
                    $newAnswer->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question and Answers updated successfully!',
                'data' => $question->load('answers'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Question and Answers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        //
     //   dd($request->all());

        $validator = Validator::make($request->all(), [
            'expire_date' => 'nullable|date',
            'questions_title' => 'required|string|max:255',
            'pass_percentage' => 'required|integer|min:0|max:100',
            'certificate' => 'required|boolean',
            'point_cpd' => 'nullable|integer',
            'code_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Save data to database

        try {

       //     dd($request->all());

           $objs = quiz::find($id);
           $objs->expire_date = $request->expire_date;
           $objs->questions_title = $request->questions_title;
           $objs->pass_percentage = $request->pass_percentage;
           $objs->certificate = $request->certificate == '1' ? true : false;
           $objs->point_cpd = $request->point_cpd;
           $objs->code_number = $request->code_number;
           $objs->save();


           return response()->json([
            'status' => true,
            'message' => 'Quiz Update successfully.',
            'data' => $objs
        ], 201);


        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['database' => 'Failed to save course data: ' . $e->getMessage()]);
        }
    }


    public function PostQuestion(Request $request, string $id)
{
    // Validate Request Input
    $validated = $request->validate([
        'detail' => 'required|string|max:255', // ตรวจสอบว่า detail ต้องมีค่าและเป็น string
        'answer' => 'required|array|min:1',    // ต้องมีคำตอบอย่างน้อย 1 รายการ
        'answer.*' => 'nullable|string|max:255', // แต่ละคำตอบต้องเป็น string
        'answers_status' => 'nullable|array',  // ตรวจสอบสถานะ checkbox (สามารถว่างได้)
        'answers_status.*' => 'nullable|boolean', // สถานะแต่ละคำตอบต้องเป็น boolean (0 หรือ 1)
    ]);

    // เริ่ม Transaction
    DB::beginTransaction();

    try {
        // บันทึกคำถามใหม่
        $question = new Question();
        $question->quiz_id = $id;
        $question->detail = $validated['detail'];
        $question->save();

        // บันทึกคำตอบหากมีการส่งมา
        if (isset($validated['answer'])) {
            foreach ($validated['answer'] as $index => $answerText) {
                if (!is_null($answerText)) {
                    $answer = new Answer();
                    $answer->questions_id = $question->id;
                    $answer->answers = $answerText;
                    $answer->answers_status = isset($validated['answers_status'][$index]) ? $validated['answers_status'][$index] : 0;
                    $answer->save();
                }
            }
        }

        // ยืนยันการบันทึกข้อมูลทั้งหมด
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Question and Answers added successfully!',
        ], 201);

    } catch (\Exception $e) {
        // หากเกิดข้อผิดพลาด ย้อนกลับการเปลี่ยนแปลงทั้งหมด
        DB::rollback();

        return response()->json([
            'success' => false,
            'message' => 'Failed to add Question and Answers.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // เริ่มต้น Transaction
            DB::beginTransaction();

            // ตรวจสอบว่า Quiz ที่จะลบมีอยู่หรือไม่
            $quiz = Quiz::find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz not found.',
                ], 404);
            }

            // ลบ Quiz และข้อมูลที่สัมพันธ์กัน เช่น Quiz Attempts
            QuizAttempt::where('quiz_id', $id)->delete(); // ลบ Quiz Attempts ที่เกี่ยวข้อง
            $quiz->delete(); // ลบ Quiz

            // ยืนยัน Transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            // ยกเลิก Transaction หากเกิดข้อผิดพลาด
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quiz.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function questionDelete($id)
    {
        DB::beginTransaction();

        try {
            // ค้นหา Question
            $question = question::findOrFail($id);

            // ลบคำตอบ (Answers) ที่เกี่ยวข้องกับ Question นี้
            answer::where('questions_id', $id)->delete();

            // ลบ Question
            $question->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete question.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
