<?php

namespace App\Http\Controllers;

use App\Exports\SurveyParticipantsExport;
use App\Exports\SurveyQuestionsExport;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponseAnswer;
use App\Models\SurveyResponse;
use App\Models\CourseAction;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        try {
            $objs = Survey::with([
                'courses.countries' => function ($query) {
                    $query->select('countries.id', 'countries.name', 'countries.flag'); // ดึงเฉพาะ field ที่ต้องการ
                }
            ])
                ->withCount([
                    'courses as total_courses', // นับจำนวน Courses ที่เชื่อมโยงกับ Survey
                    'responses as total_responses', // นับจำนวน Responses ของ Survey
                ])
                ->when($search, function ($query, $search) {
                    $query->where('survey_title', 'LIKE', "%$search%") // ค้นหาชื่อ Survey
                    ->orWhere('survey_id', 'LIKE', "%$search%") // ค้นหารหัส Survey
                    ->orWhere('expire_date', 'LIKE', "%$search%"); // ค้นหาวันหมดอายุ
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10); // ใช้ pagination (10 รายการต่อหน้า)

            // Filter เฉพาะ countries ที่เกี่ยวข้อง
            $objs->transform(function ($survey) {
                $countries = $survey->courses->pluck('countries')->flatten()->unique('id'); // รวม countries จาก courses
                $survey->countries = $countries; // เพิ่ม attribute `countries` ให้ Survey
                unset($survey->courses); // ลบ courses ออกจาก response (ถ้าไม่ต้องการ)
                return $survey;
            });

            return response()->json([
                'success' => true,
                'message' => 'Surveys retrieved successfully.',
                'data' => $objs,
            ], 200);

        } catch (\Exception $e) {
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
        return view('admin.survey.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expire_date' => 'nullable|date',
            'survey_title' => 'required|string|max:255',
            'survey_detail' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $totalSurveys = Survey::count();
            $nextSurveyNumber = $totalSurveys + 1;
            // สร้าง Survey
            $survey = new Survey();
            $survey->survey_id = 'S' . str_pad($nextSurveyNumber, 3, '0', STR_PAD_LEFT);
            $survey->expire_date = Carbon::createFromFormat('d-m-Y', $request->expire_date)->format('Y-m-d');
            $survey->survey_title = $request->survey_title;
            $survey->survey_detail = $request->survey_detail;
            $survey->created_by = $request->user()->id; // เก็บ user_id ของผู้สร้าง
            $survey->save();

            return response()->json([
                'status' => true,
                'message' => 'Survey created successfully.',
                'data' => $survey
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create survey.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */

    private function calculateDuration($startDate, $completionDate)
    {
        $start = Carbon::parse($startDate);
        $completion = Carbon::parse($completionDate);

        $days = $start->diffInDays($completion);
        $hours = $start->diffInHours($completion) % 24;
        $minutes = $start->diffInMinutes($completion) % 60;

        return sprintf('%dD %dHrs %dMins', $days, $hours, $minutes);
    }

    public function getSurveyParticipants(Request $request, string $id)
    {
        try {
            // ตัวกรองข้อมูล
            $search = $request->input('search', ''); // ค้นหาชื่อผู้ใช้
            $courseId = $request->input('course_id'); // กรองตาม course_id

            // ดึง Survey ที่ระบุ ID
            $survey = Survey::findOrFail($id);

            // จำนวนผู้ตอบ Survey ทั้งหมด
            $summitReport = SurveyResponse::where('survey_id', $id)->count();
            $enrolled = CourseAction::count();

            // ดึงข้อมูลผู้ตอบ Survey พร้อมเงื่อนไขการกรอง
            $participantsQuery = SurveyResponse::with(['user.countryDetails', 'surveyResponseAnswers'])
                ->where('survey_id', $id)
                ->when($courseId, function ($query) use ($courseId) {
                    $query->whereHas('surveyResponseAnswers', function ($subQuery) use ($courseId) {
                        $subQuery->where('course_id', $courseId);
                    });
                })
                ->when($search, function ($query, $search) {
                    $query->whereHas('user', function ($subQuery) use ($search) {
                        $subQuery->where('firstName', 'LIKE', "%$search%")
                            ->orWhere('lastName', 'LIKE', "%$search%");
                    });
                });

            // ใช้ Pagination
            $paginatedParticipants = $participantsQuery->paginate(10);

            // จัดการข้อมูลผู้ตอบ
            $formattedParticipants = $paginatedParticipants->getCollection()->map(function ($participant) {
                $user = $participant->user;

                return [
                    'name' => $user ? "{$user->firstName} {$user->lastName}" : 'Unknown User', // ตรวจสอบว่า user มีค่าหรือไม่
                    'country' => $user->countryDetails->name ?? null,
                    'country_img' => $user->countryDetails->img ?? null,
                    'clinic' => $user->clinic ?? 'N/A',
                    'attempts' => 1, // สมมติว่าเป็น 1 ครั้ง
                    'start_date' => $participant->created_at->format('d M Y | h:i A'),
                    'completion_date' => $participant->updated_at->format('d M Y | h:i A'),
                    'duration' => $this->calculateDuration($participant->created_at, $participant->updated_at),
                ];
            });

            // เพิ่มข้อมูล Course Linked
            $courseLinked = $survey->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_id' => $course->course_id,
                    'course_title' => $course->course_title,
                    'course_img' => $course->course_img,
                    'description' => $course->course_description,
                    'rating' => number_format($course->ratting, 1),
                    'enrolled' => $course->Enrolled,
                    'summit_completed' => $course->SummitCompleted,
                    'mainCategories' => $course->mainCategories->pluck('name')->toArray(),
                    'country' => $course->countries->map(function ($country) {
                        return [
                            'name' => $country->name,
                            'img' => $country->img,
                        ];
                    })->toArray(),
                ];
            });

            // ส่งข้อมูลกลับ
            return response()->json([
                'success' => true,
                'message' => 'Survey participants retrieved successfully.',
                'data' => [
                    'total_summit' => $summitReport,
                    'total_enrolled' => $enrolled,
                    'participants' => [
                        'data' => $formattedParticipants,
                        'pagination' => [
                            'current_page' => $paginatedParticipants->currentPage(),
                            'last_page' => $paginatedParticipants->lastPage(),
                            'total' => $paginatedParticipants->total(),
                        ],
                    ],
                    'courseLinked' => $courseLinked,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving survey participants.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportSurveyParticipants($id)
    {
        $fileName = 'survey_participants_' . $id . '_' . now()->format('Y_m_d_H_i_s') . '.csv';
        return Excel::download(new SurveyParticipantsExport($id), $fileName);
    }

    public function getSurveyAnsList(Request $request, string $id)
    {
        try {
            $search = $request->input('search', ''); // ค้นหาคำถาม
            $courseId = $request->input('course_id'); // รับ course_id สำหรับกรองข้อมูล

            // ดึง Survey พร้อม Questions และ Answers
            $survey = Survey::with(['questions.answers'])->findOrFail($id);
            $enrolled = CourseAction::count();
            // จำนวนผู้ตอบ Survey ทั้งหมด
            $totalParticipants = SurveyResponse::where('survey_id', $id)
                ->when($courseId, function ($query) use ($courseId) {
                    $query->whereHas('surveyResponseAnswers', function ($subQuery) use ($courseId) {
                        $subQuery->where('course_id', $courseId);
                    });
                })
                ->distinct('user_id')
                ->count('user_id');

            // ดึง Questions พร้อมคำตอบและคำนวณเปอร์เซ็นต์การเลือก
            $questions = $survey->questions()
                ->when($search, function ($query, $search) {
                    $query->where('question_detail', 'LIKE', "%$search%");
                })
                ->with(['answers' => function ($query) use ($id, $courseId) {
                    $query->withCount(['surveyResponseAnswers as selected_count' => function ($subQuery) use ($id, $courseId) {
                        $subQuery->whereHas('surveyResponse', function ($responseQuery) use ($id, $courseId) {
                            $responseQuery->where('survey_id', $id);
                            if ($courseId) {
                                $responseQuery->where('course_id', $courseId);
                            }
                        });
                    }]);
                }])
                ->paginate(5); // Pagination

            // แปลงข้อมูลคำถามและคำตอบ
            $formattedQuestions = $questions->map(function ($question) use ($totalParticipants) {
                return [
                    'id' => $question->id,
                    'question' => $question->question_detail,
                    'choices' => $question->answers->map(function ($answer) use ($totalParticipants) {
                        $selectedCount = $answer->selected_count ?? 0;
                        $percentage = $totalParticipants > 0
                            ? round(($selectedCount / $totalParticipants) * 100, 2)
                            : 0;

                        return [
                            'id' => $answer->id,
                            'text' => $answer->answer_text,
                            'selected_count' => $selectedCount,
                            'percentage' => "{$percentage}%",
                        ];
                    }),
                ];
            });

            $courseLinked = $survey->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_id' => $course->course_id,
                    'course_title' => $course->course_title,
                    'course_img' => $course->course_img,
                    'description' => $course->course_description,
                    'rating' => number_format($course->ratting, 1),
                    'enrolled' => $course->Enrolled,
                    'summit_completed' => $course->SummitCompleted,
                    'mainCategories' => $course->mainCategories->map(function ($category) {
                        return $category->name; // ดึงชื่อหมวดหมู่หลัก
                    })->toArray(), // เปลี่ยนเป็น array
                    'country' => $course->countries->map(function ($country) {
                        return [
                            'name' => $country->name, // ชื่อประเทศ
                            'img' => $country->img,  // ลิงก์รูปภาพธงประเทศ
                        ];
                    })->toArray(),
                ];
            });

            // ส่งข้อมูลกลับ
            return response()->json([
                'success' => true,
                'message' => 'Survey questions retrieved successfully.',
                'data' => [
                    'total_participants' => $totalParticipants,
                    'total_enrolled' => $enrolled,
                    'questions' => $formattedQuestions,
                    'pagination' => [
                        'current_page' => $questions->currentPage(),
                        'last_page' => $questions->lastPage(),
                        'total' => $questions->total(),
                    ],
                    'courseLinked' => $courseLinked
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving survey questions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportSurveyQuestions($id)
    {
        $fileName = 'survey_questions_' . $id . '_' . now()->format('Y_m_d_H_i_s') . '.csv';
        return Excel::download(new SurveyQuestionsExport($id), $fileName);
    }

    public function show(Request $request, string $id)
    {
        try {
            // รับค่าช่วงวันที่จาก Query Parameters
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            // ดึง Survey พร้อมข้อมูลที่เกี่ยวข้อง
            $survey = Survey::with([
                'questions.answers', // ดึงคำถามและคำตอบ
                'creator',
                'courses' => function ($query) {
                    $query->with(['countries']) // ดึงข้อมูลประเทศของคอร์ส
                    ->withCount([
                        'courseActions as Enrolled', // จำนวนผู้ลงทะเบียนทั้งหมด
                        'courseActions as SummitCompleted' => function ($subQuery) {
                            $subQuery->where('isFinishCourse', 1); // จำนวนผู้ลงทะเบียนสำเร็จ
                        }
                    ]);
                }
            ])
                ->withCount('responses as total_responses') // นับจำนวนผู้ตอบ Survey
                ->findOrFail($id);

            // จำนวนผู้ตอบ Survey ทั้งหมด
            $summitReport = SurveyResponse::where('survey_id', $id)->count();

            // Course Linked
            $courseLinked = $survey->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_id' => $course->course_id,
                    'course_title' => $course->course_title,
                    'course_img' => $course->course_img,
                    'description' => $course->course_description,
                    'rating' => number_format($course->ratting, 1),
                    'enrolled' => $course->Enrolled,
                    'summit_completed' => $course->SummitCompleted,
                    'mainCategories' => $course->mainCategories->map(function ($category) {
                        return $category->name; // ดึงชื่อหมวดหมู่หลัก
                    })->toArray(), // เปลี่ยนเป็น array
                    'last_summit' => $course->updated_at ? $course->updated_at->format('d M Y') : null,
                    'country' => $course->countries->map(function ($country) {
                        return [
                            'name' => $country->name, // ชื่อประเทศ
                            'img' => $country->img,  // ลิงก์รูปภาพธงประเทศ
                        ];
                    })->toArray(),
                ];
            });

            // ดึงข้อมูลจำนวนผู้ตอบ Survey แยกตามเดือน (ตามช่วงวันที่)
            $surveyResponsesByMonth = SurveyResponse::select(
                DB::raw('MONTH(created_at) as month'), // เดือน
                DB::raw('YEAR(created_at) as year'), // ปี
                DB::raw('COUNT(id) as total_responses') // จำนวนผู้ตอบ Survey
            )
                ->where('survey_id', $id) // เฉพาะ Survey ID นี้
                ->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate); // เริ่มจาก start_date
                })
                ->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate); // ถึง end_date
                })
                ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)')) // จัดกลุ่มตามปีและเดือน
                ->orderBy(DB::raw('YEAR(created_at)'), 'DESC') // เรียงลำดับปี
                ->orderBy(DB::raw('MONTH(created_at)'), 'DESC') // เรียงลำดับเดือน
                ->get();

            // เตรียมข้อมูลกราฟแท่ง
            $barChartData = $surveyResponsesByMonth->map(function ($item) {
                return [
                    'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('F'), // ชื่อเดือน
                    'year' => $item->year, // ปี
                    'total_responses' => $item->total_responses, // จำนวนผู้ตอบ
                ];
            });

            // ส่งข้อมูลกลับ
            return response()->json([
                'success' => true,
                'message' => 'Survey details retrieved successfully.',
                'data' => [
                    'survey' => $survey,
                    'summit_report' => $summitReport, // จำนวนผู้ตอบ Survey ทั้งหมด
                    'course_link' => $courseLinked, // รายชื่อ Courses ที่เกี่ยวข้อง
                    'barChartData' => $barChartData, // ข้อมูลสำหรับกราฟแท่ง
                ],
            ], 200);

        } catch (\Exception $e) {
            // จัดการข้อผิดพลาด
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve survey details.',
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
        $objs = Survey::find($id);
        $data['url'] = url('admin/survey/' . $id);
        $data['method'] = "put";
        $data['objs'] = $objs;
        return view('admin.survey.edit', $data);
    }

    public function deleteAnswer(string $answerId)
    {
        DB::beginTransaction();

        try {
            // ค้นหา Answer ที่ต้องการลบ
            $answer = SurveyAnswer::findOrFail($answerId);

            // ตรวจสอบว่า Answer ถูกใช้ใน SurveyResponseAnswer หรือไม่
            $isUsed = SurveyResponseAnswer::where('survey_answer_id', $answer->id)->exists();

            if ($isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the answer because it is used in survey responses.',
                ], 400);
            }

            // ลบ Answer
            $answer->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Answer deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete answer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateSurveyQuestion(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'question_detail' => 'required|string|max:500', // รายละเอียดคำถาม
            'sort_order' => 'nullable|integer', // ลำดับการเรียง
            'answers' => 'nullable|array', // ตรวจสอบคำตอบที่ส่งมา
            'answers.*.id' => 'nullable|exists:survey_answers,id', // ID ของคำตอบ (ถ้ามี)
            'answers.*.answer_text' => 'required|string|max:255', // ข้อความคำตอบ
            'answers.*.sort_order' => 'nullable|integer', // ลำดับของคำตอบ
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // ค้นหา SurveyQuestion ตาม ID
            $surveyQuestion = SurveyQuestion::findOrFail($id);

            // อัปเดตคำถาม
            $surveyQuestion->question_detail = $request->input('question_detail');
            $surveyQuestion->sort_order = $request->input('sort_order', $surveyQuestion->sort_order);
            $surveyQuestion->save();

            // อัปเดตหรือเพิ่มคำตอบ
            if ($request->has('answers')) {
                foreach ($request->answers as $answerData) {
                    if (isset($answerData['id'])) {
                        // อัปเดตคำตอบเดิม
                        $answer = SurveyAnswer::findOrFail($answerData['id']);
                        $answer->answer_text = $answerData['answer_text'];
                        $answer->sort_order = $answerData['sort_order'] ?? $answer->sort_order;
                        $answer->save();
                    } else {
                        // เพิ่มคำตอบใหม่
                        $surveyQuestion->answers()->create([
                            'answer_text' => $answerData['answer_text'],
                            'sort_order' => $answerData['sort_order'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Survey question updated successfully.',
                'data' => $surveyQuestion->load('answers'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update survey question.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function SurveyQuestion(Request $request, string $id)
    {
        // Validate Input
        $request->validate([
            'questions' => 'required|array', // ต้องส่งคำถามมาเป็น array
            'questions.*.question_detail' => 'required|string', // คำถามแต่ละข้อห้ามว่าง
            'questions.*.sort_order' => 'nullable|integer', // อันดับคำถาม (optional)
            'questions.*.answers' => 'nullable|array', // คำตอบแต่ละคำถาม
            'questions.*.answers.*.answer_text' => 'required|string', // คำตอบห้ามว่าง
            'questions.*.answers.*.sort_order' => 'nullable|integer', // อันดับคำตอบ (optional)
        ]);

        DB::beginTransaction();

        try {
            $survey = Survey::findOrFail($id); // หา Survey

            foreach ($request->questions as $questionData) {
                // สร้างคำถาม
                $question = new SurveyQuestion();
                $question->survey_id = $survey->id;
                $question->question_detail = $questionData['question_detail'];
                $question->sort_order = $questionData['sort_order'] ?? 0;
                $question->save();

                // สร้างคำตอบ ถ้ามี
                if (!empty($questionData['answers'])) {
                    foreach ($questionData['answers'] as $answerData) {
                        $answer = new SurveyAnswer();
                        $answer->survey_question_id = $question->id;
                        $answer->answer_text = $answerData['answer_text'];
                        $answer->sort_order = $answerData['sort_order'] ?? 0;
                        $answer->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Survey questions and answers added successfully.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create survey questions and answers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'expire_date' => 'nullable|date', // ตรวจสอบรูปแบบวันที่
            'survey_title' => 'required|string|max:255', // ชื่อ Survey ห้ามว่าง
            'survey_detail' => 'nullable|string', // รายละเอียด
        ]);

        try {
            // หา Survey ที่ต้องการอัปเดต
            $survey = Survey::findOrFail($id);

            // อัปเดตข้อมูล
            $survey->expire_date = $request->expire_date
                ? Carbon::createFromFormat('d-m-Y', $request->expire_date)->format('Y-m-d')
                : $survey->expire_date;
            $survey->survey_title = $request->survey_title;
            $survey->survey_detail = $request->survey_detail;

            // บันทึกข้อมูลใหม่
            $survey->save();

            return response()->json([
                'success' => true,
                'message' => 'Survey updated successfully.',
                'data' => $survey
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update survey.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $survey = Survey::findOrFail($id); // หา Survey ถ้าไม่เจอจะ throw 404

            // ลบข้อมูลที่เกี่ยวข้อง
            $survey->questions()->each(function ($question) {
                // ลบคำตอบของคำถาม
                $question->answers()->delete();
            });

            // ลบคำถามทั้งหมด
            $survey->questions()->delete();

            // ลบ responses และ response answers
            $survey->responses()->each(function ($response) {
                $response->answers()->delete();
            });

            $survey->responses()->delete();

            // ลบ Survey
            $survey->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Survey and related data deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete survey.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyQuestion($id)
    {
        DB::beginTransaction();

        try {
            // ค้นหา Survey Question
            $question = SurveyQuestion::findOrFail($id);

            // ลบคำตอบ (SurveyAnswers) ที่เกี่ยวข้องกับคำถามนี้
            SurveyAnswer::where('survey_question_id', $id)->delete();

            // ลบความสัมพันธ์ที่เกี่ยวข้องกับ Survey Responses (SurveyResponseAnswers)
            SurveyResponseAnswer::where('survey_question_id', $id)->delete();

            // ลบคำถาม
            $question->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Survey Question deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete survey question.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
