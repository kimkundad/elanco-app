<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

use App\Models\course;
use App\Models\quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\CourseAction;
use App\Models\Country;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyResponseAnswer;
use App\Models\Settings\FeaturedCourse;
use App\Models\Settings\HomeBanner;
use App\Models\Settings\PageBanner;


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

// เพิ่มบรรทัดนี้สำหรับใช้ Str
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{


    //
    public function getCourseAction($id)
    {

        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

        $courseAction = CourseAction::where('course_id', $id)->where('user_id', $user->id)->first();

        if (!$courseAction) {
            // หากไม่พบข้อมูล ให้ส่งค่าเริ่มต้นกลับมา
            return response()->json([
                'isFinishCourse' => false,
                'lastTimestamp' => 0, // unit is sec
                'isFinishVideo' => false,
                'isFinishQuiz' => false,
                'isDownloadCertificate' => false,
                'isReview' => false,
            ]);
        }

        // ส่งข้อมูลกลับพร้อมแปลงค่าจาก 0/1 เป็น true/false
        return response()->json([
            'isFinishCourse' => $courseAction->isFinishCourse == 1,
            'lastTimestamp' => (int)$courseAction->lastTimestamp, // แปลงเป็น int
            'isFinishVideo' => $courseAction->isFinishVideo == 1,
            'isFinishQuiz' => $courseAction->isFinishQuiz == 1,
            'isDownloadCertificate' => $courseAction->isDownloadCertificate == 1,
            'isReview' => $courseAction->isReview == 1,
        ]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack(); // ย้อนกลับการเปลี่ยนแปลงหากเกิดข้อผิดพลาด
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCertificate(Request $request, $id)
    {
        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            $course = course::with('quiz')->findOrFail($id);

            // ดึงคะแนน (score) จาก QuizAttempt
            $quizAttempt = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $course->quiz->id ?? null) // ตรวจสอบว่ามี quiz_id ใน course หรือไม่
                ->first();

            $data = [
                'recipientName' => $request->name ?? "{$user->firstName} {$user->lastName}",
                'programTitle' => $course->course_title,
                'codeNumber' => $course->quiz->code_number ?? 'N/A', // ใช้ quiz_id แทน course_id
                'points' => $course->quiz->point_cpd ?? '0', // หากไม่มี CE Points ให้ใช้ 0
            ];

            // สร้าง PDF
            $pdf = Pdf::loadView('certificate-template', $data)
                ->setPaper('a4', 'landscape'); // ตั้งค่าเป็น A4 แนวนอน

            // สร้างชื่อไฟล์
            $fileName = "Certificate_{$user->firstName}_{$user->lastName}_{$course->course_title}.pdf";

            DB::beginTransaction();

            // อัปเดตสถานะ isDownloadCertificate และ isFinishCourse
            $courseAction = CourseAction::updateOrCreate(
                [
                    'course_id' => $id,
                    'user_id' => $user->id,
                ],
                [
                    'isDownloadCertificate' => true,
                    'isFinishCourse' => true,
                ]
            );

            // เพิ่ม point_cpd ให้กับ users.ce_credit
            if ($course->quiz && $course->quiz->point_cpd > 0) {
                $user->increment('ce_credit', $course->quiz->point_cpd);
            }

            DB::commit();

            // แปลง PDF เป็น Base64
            $pdfContent = $pdf->output(); // ดึงเนื้อหา PDF
            $pdfBase64 = base64_encode($pdfContent);

            // ส่งข้อมูล Base64 พร้อมชื่อไฟล์กลับใน response
            return response()->json([
                'certificate_base64' => $pdfBase64,
                'file_name' => $fileName, // เพิ่มชื่อไฟล์ใน response
                'message' => 'Certificate downloaded successfully.',
                'courseAction' => [
                    'course_id' => $courseAction->course_id,
                    'user_id' => $courseAction->user_id,
                    'isDownloadCertificate' => $courseAction->isDownloadCertificate,
                    'isFinishCourse' => $courseAction->isFinishCourse,
                ],
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack(); // ย้อนกลับการเปลี่ยนแปลงหากเกิดข้อผิดพลาด
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



public function courses(Request $request)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();
        $userCountryId = $user->country;

        // Get filter inputs
        $search = $request->input('search', '');
        $topic = $request->input('topic', 'all');
        $animalType = $request->input('animalType', 'all');
        $uploadDate = $request->input('uploadDate', 'desc');
        $ratingOrder = $request->input('rating', 'desc');
        $durationOrder = $request->input('duration', 'asc');

        // Step 1: Base query
        $coursesQuery = course::whereHas('countries', function ($query) use ($userCountryId) {
            $query->where('country_id', $userCountryId);
        })
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('course_title', 'LIKE', "%$search%")
                        ->orWhere('course_id', 'LIKE', "%$search%");
                });
            })
            ->when($topic !== 'all', function ($query) use ($topic) {
                $query->whereHas('mainCategories', function ($subQuery) use ($topic) {
                    $subQuery->where('name', 'LIKE', "%$topic%");
                });
            })
            ->when($animalType !== 'all', function ($query) use ($animalType) {
                $query->whereHas('animalTypes', function ($subQuery) use ($animalType) {
                    $subQuery->where('name', 'LIKE', "%$animalType%");
                });
            })
            ->with([
                'countries:id,name',
                'mainCategories:id,name',
                'subCategories:id,name',
                'animalTypes:id,name',
                'itemDes',
                'Speaker.countryDetails',
                'referances',
                'courseActions' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->select('course_id', 'isFinishCourse');
                },
            ])
            ->orderBy('updated_at', $uploadDate) // จัดลำดับตาม updated_at
            ->get(); // ดึงข้อมูลทั้งหมดออกมา

        // Step 2: Sorting within the collection
        $courses = $coursesQuery->sortByDesc('ratting') // กรองตาม ratting
            ->sortBy($durationOrder === 'asc' ? 'duration' : function ($course) {
                return -$course->duration; // จัดลำดับตาม duration
            })->values(); // รีเซ็ตดัชนีใหม่

        // Step 3: Map the results
        $courses = $courses->map(function ($course) {
            $isFinishCourse = $course->courseActions->first()
                ? $course->courseActions->first()->isFinishCourse == 1
                : false;

            $course->thumbnail = $course->course_img;
            unset($course->course_img);

            return [
                'id' => $course->id,
                'course_id' => $course->course_id,
                'course_title' => $course->course_title,
                'course_description' => $course->course_description,
                'course_preview' => $course->course_preview,
                'duration' => $course->duration,
                'url_video' => $course->url_video,
                'status' => $course->status,
                'ratting' => number_format($course->ratting, 1),
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
                'id_quiz' => $course->id_quiz,
                'thumbnail' => $course->thumbnail,
                'isFinishCourse' => $isFinishCourse,
                'countries' => $course->countries->map(function ($country) {
                    return ['name' => $country->name];
                }),
                'main_categories' => $course->mainCategories->map(function ($category) {
                    return ['name' => $category->name];
                }),
                'sub_categories' => $course->subCategories->map(function ($subcategory) {
                    return ['name' => $subcategory->name];
                }),
                'animal_types' => $course->animalTypes->map(function ($animal) {
                    return ['name' => $animal->name];
                }),
                'item_des' => $course->itemDes->map(function ($item) {
                    return ['detail' => $item->detail];
                }),
                'speakers' => $course->Speaker->map(function ($speaker) {
                    return [
                        'id' => $speaker->id,
                        'name' => $speaker->name,
                        'avatar' => $speaker->avatar,
                        'job_position' => $speaker->job_position,
                        'country' => $speaker->countryDetails ? $speaker->countryDetails->name : null,
                        'file' => $speaker->file,
                        'description' => $speaker->description,
                    ];
                }),
                'referances' => $course->referances->map(function ($referance) {
                    return [
                        'id' => $referance->id,
                        'title' => $referance->title,
                        'image' => $referance->image,
                        'file' => $referance->file,
                        'description' => $referance->description,
                    ];
                }),
            ];
        });

        return response()->json(['success' => true, 'courses' => $courses], 200);

    } catch (TokenExpiredException $e) {
        return response()->json(['error' => 'Token has expired', 'message' => 'Please refresh your token or login again.'], 401);
    } catch (TokenInvalidException $e) {
        return response()->json(['error' => 'Token is invalid', 'message' => 'The provided token is not valid.'], 401);
    } catch (JWTException $e) {
        return response()->json(['error' => 'Token not provided', 'message' => 'Authorization token is missing from your request.'], 400);
    }
}








    public function highlightCourses(Request $request)
    {
        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            $userCountryId = $user->country;

            // ดึง Courses ที่มี Highlight เท่านั้น
            $courses = course::whereHas('countries', function ($query) use ($userCountryId) {
                $query->where('country_id', $userCountryId);
            })
                ->where('highlight', 1) // เพิ่มเงื่อนไข highlight == 1
                ->with([
                    'countries' => function ($query) {
                        $query->select('countries.id', 'countries.name');
                    },
                    'mainCategories' => function ($query) {
                        $query->select('main_categories.id', 'main_categories.name');
                    },
                    'subCategories' => function ($query) {
                        $query->select('sub_categories.id', 'sub_categories.name');
                    },
                    'animalTypes' => function ($query) {
                        $query->select('animal_types.id', 'animal_types.name');
                    },
                    'itemDes',
                    'Speaker',
                    'referances',
                    // ดึงความสัมพันธ์กับ CourseAction
                    'courseActions' => function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->select('course_id', 'isFinishCourse'); // เลือกเฉพาะฟิลด์ที่ต้องการ
                    },
                ])
                ->get()
                ->map(function ($course) {

                    // ตรวจสอบว่า CourseAction มีข้อมูลหรือไม่
                    $isFinishCourse = $course->courseActions->first()
                        ? $course->courseActions->first()->isFinishCourse == 1
                        : false;

                    $course->thumbnail = $course->course_img;
                    unset($course->course_img);

                    return [
                        'id' => $course->id,
                        'course_id' => $course->course_id,
                        'course_title' => $course->course_title,
                        'course_description' => $course->course_description,
                        'course_preview' => $course->course_preview,
                        'duration' => $course->duration,
                        'url_video' => $course->url_video,
                        'status' => $course->status,
                        'ratting' => number_format($course->ratting, 1),
                        'created_at' => $course->created_at,
                        'updated_at' => $course->updated_at,
                        'id_quiz' => $course->id_quiz,
                        'thumbnail' => $course->thumbnail,
                        'isFinishCourse' => $isFinishCourse, // เพิ่มสถานะ isFinishCourse
                        'countries' => $course->countries->map(function ($country) {
                            return ['name' => $country->name];
                        }),
                        'main_categories' => $course->mainCategories->map(function ($category) {
                            return ['name' => $category->name];
                        }),
                        'sub_categories' => $course->subCategories->map(function ($subcategory) {
                            return ['name' => $subcategory->name];
                        }),
                        'animal_types' => $course->animalTypes->map(function ($animal) {
                            return ['name' => $animal->name];
                        }),
                        'item_des' => $course->itemDes->map(function ($item) {
                            return [
                                'detail' => $item->detail,
                            ];
                        }),
                        'speakers' => $course->Speaker->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'avatar' => $item->avatar,
                                'job_position' => $item->job_position,
                                'country' => $item->countryDetails ? $item->countryDetails->name : null, // ใช้ countryDetails
                                'file' => $item->file,
                                'description' => $item->description,
                            ];
                        }),
                        'referances' => $course->referances->map(function ($referance) {
                            return [
                                'id' => $referance->id,
                                'title' => $referance->title,
                                'image' => $referance->image,
                                'file' => $referance->file,
                                'description' => $referance->description,
                            ];
                        }),
                    ];
                });

            return response()->json(['courses' => $courses], 200);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        }
    }

    public function newCourses(Request $request)
    {
        try {
            // ตรวจสอบและดึงข้อมูลผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            // ดึง country ของผู้ใช้
            $userCountryId = $user->country;

            // ดึง courses ที่เกี่ยวข้องกับ country ของผู้ใช้
            $courses = course::whereHas('countries', function ($query) use ($userCountryId) {
                $query->where('country_id', $userCountryId);
            })
                ->orderBy('created_at', 'desc') // เรียงลำดับจากใหม่ล่าสุด
                ->take(12) // จำกัด 12 รายการ
                ->with([
                    'countries',
                    'mainCategories',
                    'subCategories',
                    'animalTypes',
                    'itemDes',
                    'Speaker',
                    'referances',
                    'courseActions' => function ($query) use ($user) {
                        $query->where('user_id', $user->id); // ดึงเฉพาะข้อมูลของผู้ใช้งานนี้
                    }
                ])
                ->get();

            // จัดการข้อมูลก่อนส่งกลับ
            $formattedCourses = $courses->map(function ($course) {

                $isFinishCourse = $course->courseActions->first()
                    ? $course->courseActions->first()->isFinishCourse == 1
                    : false;

                return [
                    'id' => $course->id,
                    'course_id' => $course->course_id,
                    'course_title' => $course->course_title,
                    'course_description' => $course->course_description,
                    'course_preview' => $course->course_preview,
                    'duration' => $course->duration,
                    'url_video' => $course->url_video,
                    'status' => $course->status,
                    'ratting' => number_format($course->ratting, 1),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                    'id_quiz' => $course->id_quiz,
                    'thumbnail' => $course->course_img,
                    'isFinishCourse' => $isFinishCourse, // เพิ่มสถานะการเรียน
                    'countries' => $course->countries->map(function ($country) {
                        return ['name' => $country->name];
                    }),
                    'main_categories' => $course->mainCategories->map(function ($category) {
                        return ['name' => $category->name];
                    }),
                    'sub_categories' => $course->subCategories->map(function ($subcategory) {
                        return ['name' => $subcategory->name];
                    }),
                    'animal_types' => $course->animalTypes->map(function ($animal) {
                        return ['name' => $animal->name];
                    }),
                    'item_des' => $course->itemDes->map(function ($item) {
                        return [
                            'detail' => $item->detail,
                        ];
                    }),
                    'speakers' => $course->Speaker->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'avatar' => $item->avatar,
                            'job_position' => $item->job_position,
                            'country' => $item->countryDetails ? $item->countryDetails->name : null, // ใช้ countryDetails
                            'file' => $item->file,
                            'description' => $item->description,
                        ];
                    }),
                    'referances' => $course->referances->map(function ($referance) {
                        return [
                            'id' => $referance->id,
                            'title' => $referance->title,
                            'image' => $referance->image,
                            'file' => $referance->file,
                            'description' => $referance->description,
                        ];
                    }),
                ];
            });

            // ส่งผลลัพธ์กลับ
            return response()->json([
                'courses' => $formattedCourses,
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        }
    }

    // public function exploreCourses(Request $request)
    // {
    //     try {
    //         // รับ country flag จาก request
    //         $countryFlag = $request->country;

    //         // ตรวจสอบว่า country flag มีอยู่ในฐานข้อมูลหรือไม่
    //         $country = Country::where('flag', $countryFlag)->first();
    //         //  dd($request->all());
    //         if (!$country) {
    //             return response()->json([
    //                 'error' => 'Country not found',
    //                 'message' => 'The specified country flag does not exist.',
    //             ], 404);
    //         }

    //         // ดึงข้อมูลคอร์สที่มี featured == 1 และเชื่อมโยงกับประเทศที่ระบุ
    //         $courses = course::where('featured', 1)
    //             ->whereHas('countries', function ($query) use ($country) {
    //                 $query->where('country_id', $country->id);
    //             })
    //             ->with([
    //                 'countries',
    //                 'mainCategories',
    //                 'subCategories',
    //                 'animalTypes',
    //                 'itemDes',
    //                 'Speaker',
    //                 'referances'
    //             ])
    //             ->get();

    //         // จัดรูปแบบข้อมูลสำหรับการส่งกลับ
    //         $formattedCourses = $courses->map(function ($course) {
    //             return [
    //                 'id' => $course->id,
    //                 'course_id' => $course->course_id,
    //                 'course_title' => $course->course_title,
    //                 'course_description' => $course->course_description,
    //                 'course_preview' => $course->course_preview,
    //                 'duration' => $course->duration,
    //                 'url_video' => $course->url_video,
    //                 'status' => $course->status,
    //                 'ratting' => number_format($course->ratting, 1),
    //                 'created_at' => $course->created_at,
    //                 'updated_at' => $course->updated_at,
    //                 'id_quiz' => $course->id_quiz,
    //                 'thumbnail' => $course->course_img,
    //                 'countries' => $course->countries->map(function ($country) {
    //                     return ['name' => $country->name];
    //                 }),
    //                 'main_categories' => $course->mainCategories->map(function ($category) {
    //                     return ['name' => $category->name];
    //                 }),
    //                 'sub_categories' => $course->subCategories->map(function ($subcategory) {
    //                     return ['name' => $subcategory->name];
    //                 }),
    //                 'animal_types' => $course->animalTypes->map(function ($animal) {
    //                     return ['name' => $animal->name];
    //                 }),
    //                 'item_des' => $course->itemDes->map(function ($item) {
    //                     return [
    //                         'detail' => $item->detail,
    //                     ];
    //                 }),
    //                 'speakers' => $course->Speaker->map(function ($item) {
    //                     return [
    //                         'id' => $item->id,
    //                         'name' => $item->name,
    //                         'avatar' => $item->avatar,
    //                         'job_position' => $item->job_position,
    //                         'country' => $item->countryDetails ? $item->countryDetails->name : null, // ใช้ countryDetails
    //                         'file' => $item->file,
    //                         'description' => $item->description,
    //                     ];
    //                 }),
    //                 'referances' => $course->referances->map(function ($referance) {
    //                     return [
    //                         'id' => $referance->id,
    //                         'title' => $referance->title,
    //                         'image' => $referance->image,
    //                         'file' => $referance->file,
    //                         'description' => $referance->description,
    //                     ];
    //                 }),
    //             ];
    //         });

    //         // ส่งข้อมูล
    //         return response()->json([
    //             'courses' => $formattedCourses,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // จัดการข้อผิดพลาดทั่วไป
    //         return response()->json([
    //             'error' => 'Internal Server Error',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function exploreCourses(Request $request)
    {
        try {
            // รับ country flag จาก request
            $countryFlag = $request->country;

            // ตรวจสอบว่า country flag มีอยู่ในฐานข้อมูลหรือไม่
            $country = Country::where('flag', $countryFlag)->first();
            if (!$country) {
                return response()->json([
                    'error' => 'Country not found',
                    'message' => 'The specified country flag does not exist.',
                ], 404);
            }

          //  dd($country->id);

            // ดึงข้อมูลจาก FeaturedCourse ที่เป็น public และเรียงตาม order
            $featuredCourses = FeaturedCourse::where('status', 'public')
                ->where('country_id', $country->id)
                ->orderBy('order', 'asc')
                ->with(['course' => function ($query) {
                    $query->with([
                        'countries',
                        'mainCategories',
                        'subCategories',
                        'animalTypes',
                        'itemDes',
                        'Speaker',
                        'referances',
                    ]);
                }])
                ->get();

            // จัดรูปแบบข้อมูล
            $formattedCourses = $featuredCourses->map(function ($featuredCourse) {
                $course = $featuredCourse->course; // ดึงข้อมูล course ที่เกี่ยวข้อง
                if (!$course) {
                    return null; // ถ้าไม่มี course ให้ข้าม
                }
                return [
                    'id' => $course->id,
                    'course_id' => $course->course_id,
                    'course_title' => $course->course_title,
                    'course_description' => $course->course_description,
                    'course_preview' => $course->course_preview,
                    'duration' => $course->duration,
                    'url_video' => $course->url_video,
                    'status' => $course->status,
                    'ratting' => number_format($course->ratting, 1),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                    'id_quiz' => $course->id_quiz,
                    'thumbnail' => $course->course_img,
                    'countries' => $course->countries->map(function ($country) {
                        return ['name' => $country->name];
                    }),
                    'main_categories' => $course->mainCategories->map(function ($category) {
                        return ['name' => $category->name];
                    }),
                    'sub_categories' => $course->subCategories->map(function ($subcategory) {
                        return ['name' => $subcategory->name];
                    }),
                    'animal_types' => $course->animalTypes->map(function ($animal) {
                        return ['name' => $animal->name];
                    }),
                    'item_des' => $course->itemDes->map(function ($item) {
                        return [
                            'detail' => $item->detail,
                        ];
                    }),
                    'speakers' => $course->Speaker->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'avatar' => $item->avatar,
                            'job_position' => $item->job_position,
                            'country' => $item->countryDetails ? $item->countryDetails->name : null,
                            'file' => $item->file,
                            'description' => $item->description,
                        ];
                    }),
                    'referances' => $course->referances->map(function ($referance) {
                        return [
                            'id' => $referance->id,
                            'title' => $referance->title,
                            'image' => $referance->image,
                            'file' => $referance->file,
                            'description' => $referance->description,
                        ];
                    }),
                ];
            })->filter(); // กรองค่า null ออก

            // ส่งข้อมูล
            return response()->json([
                'courses' => $formattedCourses,
            ], 200);
        } catch (\Exception $e) {
            // จัดการข้อผิดพลาดทั่วไป
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function mainPageBanner(Request $request)
    {
        try {
            // รับ country flag จาก request
            $countryFlag = $request->input('country');

            // ตรวจสอบว่า country flag มีอยู่ในฐานข้อมูลหรือไม่
            $country = Country::where('flag', $countryFlag)->first();
            if (!$country) {
                return response()->json([
                    'error' => 'Country not found',
                    'message' => 'The specified country flag does not exist.',
                ], 404);
            }

            // ดึงข้อมูลจาก HomeBanner ที่เป็น public และเชื่อมโยงกับประเทศที่ระบุ
            $homeBanners = PageBanner::where('status', 'public')
                ->where('country_id', $country->id)
                ->orderBy('order', 'asc') // เรียงลำดับตาม order
                ->get();

            // จัดรูปแบบข้อมูลสำหรับการส่งกลับ
            $formattedBanners = $homeBanners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'link' => $banner->link, // รวม link
                    'description' => $banner->description, // รวม description
                    'desktop_image' => $banner->desktop_image,
                    'mobile_image' => $banner->mobile_image,
                    'country_id' => $banner->country_id,
                    'created_at' => $banner->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $banner->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            // ส่งข้อมูล
            return response()->json([
                'success' => true,
                'message' => 'Home banners retrieved successfully.',
                'data' => $formattedBanners,
            ], 200);
        } catch (\Exception $e) {
            // จัดการข้อผิดพลาดทั่วไป
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function homePageBanner(Request $request)
    {
        try {
            $countryFlag = $request->input('country');

            $country = Country::where('flag', $countryFlag)->first();
            if (!$country) {
                return response()->json([
                    'error' => 'Country not found',
                    'message' => 'The specified country flag does not exist.',
                ], 404);
            }

            $homeBanners = HomeBanner::where('status', 'public')
                ->where('country_id', $country->id)
                ->orderBy('order', 'asc')
                ->get();

            $formattedBanners = $homeBanners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'desktop_image' => $banner->desktop_image,
                    'mobile_image' => $banner->mobile_image,
                    'link' => $banner->link, // รวม link
                    'description' => $banner->description, // รวม description
                    'country_id' => $banner->country_id,
                    'created_at' => $banner->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $banner->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Home banners retrieved successfully.',
                'data' => $formattedBanners,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function courseDetail($id)
    {
        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            // ค้นหา Course โดย ID พร้อมโหลดความสัมพันธ์
            $course = course::with([
                'countries',
                'mainCategories',
                'subCategories',
                'animalTypes',
                'itemDes',
                'Speaker.countryDetails', // เพิ่มการโหลดข้อมูล Country ของ Speaker
                'referances'
            ])->findOrFail($id);

            // ตรวจสอบว่าผู้ใช้เคยเรียนคอร์สนี้หรือไม่
            $isFinishCourse = CourseAction::where('course_id', $id)
                    ->where('user_id', $user->id)
                    ->value('isFinishCourse') == 1;

            // ดึง Related Courses
            $relatedCourses = course::whereHas('countries', function ($query) use ($course) {
                $query->whereIn('countries.id', $course->countries->pluck('id'));
            })
                ->whereHas('subCategories', function ($query) use ($course) {
                    $query->whereIn('sub_categories.id', $course->subCategories->pluck('id'));
                })
                ->whereHas('mainCategories', function ($query) use ($course) {
                    $query->whereIn('main_categories.id', $course->mainCategories->pluck('id'));
                })
                ->where('id', '!=', $id) // ไม่รวมคอร์สปัจจุบัน
                ->take(5) // จำกัดจำนวนคอร์ส
                ->get();

            // จัดรูปแบบข้อมูลสำหรับ Related Courses
            $formattedRelatedCourses = $relatedCourses->map(function ($relatedCourse) {
                return [
                    'id' => $relatedCourse->id,
                    'course_title' => $relatedCourse->course_title,
                    'thumbnail' => $relatedCourse->course_img,
                    'duration' => $relatedCourse->duration,
                    'ratting' => number_format($relatedCourse->ratting, 1),
                    'main_categories' => $relatedCourse->mainCategories->map(fn($mainCategory) => ['name' => $mainCategory->name]),
                    'sub_categories' => $relatedCourse->subCategories->map(fn($subCategory) => ['name' => $subCategory->name]),
                    'countries' => $relatedCourse->countries->map(fn($country) => ['name' => $country->name]),
                    'course_description' => $relatedCourse->course_description,
                ];
            });

            // จัดรูปแบบข้อมูลสำหรับการส่งกลับ
            $formattedCourse = [
                'id' => $course->id,
                'course_id' => $course->course_id,
                'course_title' => $course->course_title,
                'course_description' => $course->course_description,
                'course_preview' => $course->course_preview,
                'duration' => $course->duration,
                'url_video' => $course->url_video,
                'status' => $course->status,
                'ratting' => number_format($course->ratting, 1),
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
                'thumbnail' => $course->course_img,
                'isFinishCourse' => $isFinishCourse,
                'countries' => $course->countries->map(fn($country) => ['name' => $country->name]),
                'main_categories' => $course->mainCategories->map(fn($mainCategory) => ['name' => $mainCategory->name]),
                'sub_categories' => $course->subCategories->map(fn($subCategory) => ['name' => $subCategory->name]),
                'animal_types' => $course->animalTypes->map(fn($animalType) => ['name' => $animalType->name]),
                'item_des' => $course->itemDes->map(fn($item) => [
                    'detail' => $item->detail,
                ]), // จัดรูปแบบ item_des
                'speakers' => $course->Speaker->map(fn($speaker) => [
                    'id' => $speaker->id,
                    'name' => $speaker->name,
                    'avatar' => $speaker->avatar,
                    'job_position' => $speaker->job_position,
                    'country' => $speaker->countryDetails ? $speaker->countryDetails->name : null, // ใช้ countryDetails
                    'file' => $speaker->file,
                    'description' => $speaker->description,
                ]), // จัดรูปแบบ speakers
                'referances' => $course->referances->map(fn($referance) => [
                    'id' => $referance->id,
                    'title' => $referance->title,
                    'image' => $referance->image,
                    'file' => $referance->file,
                    'description' => $referance->description,
                ]), // จัดรูปแบบ referances
                'related_courses' => $formattedRelatedCourses, // เพิ่ม Related Courses
            ];

            // ส่งข้อมูล
            return response()->json($formattedCourse, 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        }
    }

    public function getSurveyByCourse($id)
    {
        try {
            // ค้นหา Course และดึง survey_id
            $course = Course::findOrFail($id);

            if (!$course->survey_id) {
                return response()->json([
                    'error' => 'No Survey Found',
                    'message' => 'This course does not have an associated survey.',
                ], 404);
            }

            // ค้นหา Survey ที่เชื่อมโยงกับ survey_id
            $survey = Survey::with(['questions.answers'])->findOrFail($course->survey_id);

            // จัดรูปแบบข้อมูล Survey
            $formattedSurvey = [
                'id' => $survey->id,
                'survey_id' => $survey->survey_id,
                'title' => $survey->survey_title,
                'detail' => $survey->survey_detail,
                'expire_date' => $survey->expire_date,
                'questions' => $survey->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'detail' => $question->question_detail,
                        'answers' => $question->answers->map(function ($answer) {
                            return [
                                'id' => $answer->id,
                                'text' => $answer->answer_text,
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json([
                'survey' => $formattedSurvey,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'Course or Survey not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCourseQuiz($id)
    {
        try {
            // ค้นหา course
            $course = course::findOrFail($id);

            // ดึง quiz_id จาก course
            $quizId = $course->id_quiz;

            // ดึง quiz พร้อมคำถามและคำตอบ
            $quiz = quiz::with(['questions.answers'])->findOrFail($quizId);

            // จัดรูปแบบข้อมูล
            $formattedQuiz = [
                'id' => $quiz->id,
                'quiz_id' => $quiz->quiz_id,
                'expire_date' => $quiz->expire_date,
                'questions_title' => $quiz->questions_title,
                'pass_percentage' => $quiz->pass_percentage,
                'certificate' => $quiz->certificate,
                'point_cpd' => $quiz->point_cpd,
                'questions' => $quiz->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'detail' => $question->detail,
                        'answers' => $question->answers->map(function ($answer) {
                            return [
                                'id' => $answer->id,
                                'text' => $answer->answers,
                                'correct_ans' => $answer->answers_status
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json([
                'quiz' => $formattedQuiz,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'Course or Quiz not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function submitQuiz(Request $request, $id)
    {
        $validatedData = $request->validate([
            'answers' => 'required|array', // รับ array ของคำตอบ
            'answers.*' => 'integer|exists:answers,id', // แต่ละคำตอบต้องมี id ที่อยู่ในตาราง answers
        ]);

    try {
        // ตรวจสอบผู้ใช้
        $user = JWTAuth::parseToken()->authenticate();

        // ดึงข้อมูล Quiz พร้อมคำถามและคำตอบ
        $quiz = Quiz::with('questions.answers')->findOrFail($id);

        if ($quiz->questions->isEmpty()) {
            return response()->json([
                'error' => 'Invalid Quiz',
                'message' => 'The quiz has no questions.',
            ], 400);
        }

        // ค้นหา Course ที่สัมพันธ์กับ Quiz
        $course = course::where('id_quiz', $quiz->id)->first();
        if (!$course) {
            return response()->json([
                'error' => 'Invalid Quiz',
                'message' => 'No course associated with this quiz.',
            ], 400);
        }

        $totalPoints = count($quiz->questions);  // คะแนนเต็มจาก point_cpd
        $passPercentage = $quiz->pass_percentage; // เปอร์เซ็นต์ที่ต้องผ่าน
        $correctPoints = 0;

        // ลบคำตอบเก่าของ User ใน Quiz นี้ (หากมี)
        DB::table('quiz_user_answers')->where('user_id', $user->id)->where('quiz_id', $quiz->id)->delete();

        // ตรวจสอบคำตอบที่ส่งมาและบันทึกใน `quiz_user_answers`
        foreach ($quiz->questions as $question) {
            $submittedAnswerId = collect($request->answers)
                ->firstWhere(fn($answerId) => $question->answers->pluck('id')->contains($answerId));

            $correctAnswer = $question->answers->firstWhere('answers_status', 1);


            // บันทึกคำตอบของ User
            DB::table('quiz_user_answers')->insert([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'course_id' => $request->course_id,
                'question_id' => $question->id,
                'answer_id' => $submittedAnswerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ตรวจสอบว่าคำตอบถูกต้องหรือไม่
            if ($correctAnswer && $submittedAnswerId == $correctAnswer->id) {
                $correctPoints++;
            }
        }

        // คำนวณเปอร์เซ็นต์ที่ได้
        $scorePercentage = ($correctPoints / $totalPoints) * 100;

        // ตรวจสอบผ่านหรือไม่
        $isPass = $scorePercentage >= $passPercentage;

        // ตรวจสอบและจัดการ QuizAttempt
        $attempt = QuizAttempt::updateOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
            ],
            [
                'score' => $correctPoints,
                'total_questions' => $totalPoints,
            ]
        );

        // อัปเดตหรือสร้าง course_action
        $courseAction = CourseAction::updateOrCreate(
            [
                'course_id' => $course->id, // ใช้ course_id จาก Course
                'user_id' => $user->id,
            ],
            [
                'isFinishQuiz' => $isPass, // อัปเดตสถานะ isFinishQuiz
            ]
        );

        // ส่งข้อมูลกลับ
        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $correctPoints,
            'total_points' => $totalPoints,
            'isPass' => $isPass,
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'error' => 'Quiz not found',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function submitSurvey(Request $request, $id)
    {
        $validatedData = $request->validate([
            'answers' => 'required|array', // รับ array ของคำตอบ
            'answers.*.question_id' => 'required|integer|exists:survey_questions,id', // ตรวจสอบว่า question_id มีอยู่ในฐานข้อมูล
            'answers.*.answer_id' => 'nullable|integer|exists:survey_answers,id', // answer_id (ถ้ามี) ต้องมีอยู่ในฐานข้อมูล
            'answers.*.custom_answer' => 'nullable|string', // custom_answer (ถ้ามี)
            'answers.*.course_id' => 'nullable',
        ]);

        try {
            // ตรวจสอบผู้ใช้
            $user = JWTAuth::parseToken()->authenticate();

            // ค้นหา Survey
            $survey = Survey::findOrFail($id);

            // ตรวจสอบว่า Survey หมดอายุหรือไม่
            if ($survey->expire_date && now()->greaterThan($survey->expire_date)) {
                return response()->json([
                    'error' => 'Survey Expired',
                    'message' => 'This survey has expired.',
                ], 400);
            }

            // สร้าง SurveyResponse สำหรับผู้ใช้
            $surveyResponse = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => $user->id,
            ]);

            // บันทึกคำตอบของผู้ใช้

            foreach ($validatedData['answers'] as $answerData) {

                SurveyResponseAnswer::create([
                    'survey_response_id' => $surveyResponse->id,
                    'course_id' => $answerData['course_id'],
                    'survey_question_id' => $answerData['question_id'],
                    'survey_answer_id' => $answerData['answer_id'] ?? null, // ถ้าเป็น null แปลว่าผู้ใช้กรอก custom_answer
                    'custom_answer' => $answerData['custom_answer'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Survey submitted successfully',
                'survey_id' => $survey->id,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Survey not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function PostReview(Request $request, $id)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validatedData = $request->validate([
            'rating' => 'required|integer|min:1|max:5', // rating ต้องเป็นตัวเลขระหว่าง 1-5
        ]);

        try {
            // ตรวจสอบผู้ใช้
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'User not authenticated',
                ], 401);
            }

            // ตรวจสอบว่ามี CourseAction อยู่หรือไม่
            $courseAction = CourseAction::where('course_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$courseAction) {
                // หากไม่มี CourseAction ให้สร้างใหม่
                $courseAction = CourseAction::create([
                    'course_id' => $id,
                    'user_id' => $user->id,
                    'isFinishCourse' => false,
                    'lastTimestamp' => 0,
                    'isFinishVideo' => false,
                    'isFinishQuiz' => false,
                    'isDownloadCertificate' => false,
                    'isReview' => true,
                    'rating' => $validatedData['rating'], // ตั้งค่า rating
                ]);
            } else {
                // หากมี CourseAction อยู่แล้ว ให้ทำการอัปเดต
                $courseAction->update([
                    'isReview' => true,
                    'rating' => $validatedData['rating'], // อัปเดตค่า rating
                ]);
            }

            // ส่งข้อมูลกลับ
            return response()->json([
                'message' => 'Review submitted successfully.',
                'courseAction' => [
                    'course_id' => $courseAction->course_id,
                    'user_id' => $courseAction->user_id,
                    'lastTimestamp' => (int)$courseAction->lastTimestamp,
                    'isFinishVideo' => $courseAction->isFinishVideo == 1,
                    'isFinishQuiz' => $courseAction->isFinishQuiz == 1,
                    'isDownloadCertificate' => $courseAction->isDownloadCertificate == 1,
                    'isReview' => $courseAction->isReview == 1,
                    'isFinishCourse' => $courseAction->isFinishCourse == 1,
                    'rating' => $courseAction->rating,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function upProgress(Request $request, $id)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validatedData = $request->validate([
            'timestamp' => 'required|integer|min:0',
            'isFinishVideo' => 'required|boolean',
        ]);

        try {
            // ตรวจสอบผู้ใช้
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'User not authenticated',
                ], 401);
            }

            // ถ้า isFinishVideo เป็น true ให้ตั้งค่า timestamp เป็น 0
            if ($validatedData['isFinishVideo']) {
                $validatedData['timestamp'] = 0;
            }

            // ใช้ updateOrCreate เพื่อตรวจสอบและอัปเดต/สร้างข้อมูล
            $courseAction = CourseAction::updateOrCreate(
                [
                    'course_id' => $id,
                    'user_id' => $user->id, // อ้างอิงผู้ใช้
                ],
                [
                    'lastTimestamp' => $validatedData['timestamp'],
                    'isFinishVideo' => $validatedData['isFinishVideo'],
                ]
            );

            return response()->json([
                'message' => 'Progress updated successfully.',
                'courseAction' => [
                    'lastTimestamp' => (int)$courseAction->lastTimestamp,
                    'isFinishVideo' => $courseAction->isFinishVideo == 1,
                    'isFinishQuiz' => $courseAction->isFinishQuiz == 1,
                    'isDownloadCertificate' => $courseAction->isDownloadCertificate == 1,
                    'isReview' => $courseAction->isReview == 1,
                    'isFinishCourse' => $courseAction->isFinishCourse == 1,
                    'rating' => $courseAction->rating,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function courseMe(Request $request)
    {
        try {
            // ดึงผู้ใช้ที่เข้าสู่ระบบ
            $user = JWTAuth::parseToken()->authenticate();

            // ดึงข้อมูลคอร์สที่ผู้ใช้งานอยู่พร้อมสถานะและ quiz
            $courses = CourseAction::with(['course.quiz']) // โหลดข้อมูล Quiz ด้วย
            ->where('user_id', $user->id) // เฉพาะคอร์สของผู้ใช้ปัจจุบัน
            ->get();

            // แปลงข้อมูลให้อยู่ในรูปแบบ Response
            $response = $courses->map(function ($courseAction) {
                return [
                    'course_id' => $courseAction->course_id,
                    'course_title' => $courseAction->course->course_title,
                    'course_preview' => $courseAction->course->course_preview,
                    'course_img' => $courseAction->course->course_img,
                    'expire_date' => optional($courseAction->course->quiz)->expire_date, // ดึง expire_date จาก Quiz
                    'isFinishCourse' => $courseAction->isFinishCourse,
                    'isFinishVideo' => $courseAction->isFinishVideo,
                    'isFinishQuiz' => $courseAction->isFinishQuiz,
                    'isDownloadCertificate' => $courseAction->isDownloadCertificate,
                    'isReview' => $courseAction->isReview,
                    'rating' => $courseAction->rating,
                    'lastTimestamp' => $courseAction->lastTimestamp,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $response,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
                'message' => 'Please refresh your token or login again.',
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
                'message' => 'The provided token is not valid.',
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token not provided',
                'message' => 'Authorization token is missing from your request.',
            ], 400);
        }
    }

    public function verifyEmail(Request $request)
{
    try {
        $verificationToken = $request->query('token');

        $record = DB::table('email_verifications')->where('token', $verificationToken)->first();
        if (!$record) {
            //return response()->json(['message' => 'Invalid verification link.'], 403);
            return redirect("https://elanco-fe.vercel.app/expire");
        }

        if (now()->greaterThan($record->expires_at)) {
            return redirect("https://elanco-fe.vercel.app/expire");
        }

        $user = User::find($record->user_id);
        if (!$user) {
            return redirect("https://elanco-fe.vercel.app/expire");
        }


        if ($user->email_verified_at) {
            $accessToken = JWTAuth::fromUser($user);
            $refreshToken = $this->generateRefreshToken($user);

            DB::table('email_verifications')->where('token', $verificationToken)->delete();

            return redirect("https://elanco-fe.vercel.app/login?accToken={$accessToken}&refreshToken={$refreshToken}");
        }

        $user->update(['email_verified_at' => now()]);

        $accessToken = JWTAuth::fromUser($user);
        $refreshToken = $this->generateRefreshToken($user);

        DB::table('email_verifications')->where('token', $verificationToken)->delete();

        return redirect("https://elanco-fe.vercel.app/login?accToken={$accessToken}&refreshToken={$refreshToken}");
    } catch (\Exception $e) {
        \Log::error('Error in verifyEmail: ' . $e->getMessage());
        return redirect("https://elanco-fe.vercel.app/expire");
       // return response()->json(['message' => 'An unexpected error occurred.'], 500);
    }
}


    /**
     * Generate a refresh token for the user.
     */
    protected function generateRefreshToken(User $user)
    {
        // ตัวอย่าง: ใช้ UUID เพื่อสร้าง refresh token
        $refreshToken = Str::uuid();

        // เก็บ refresh token ในฐานข้อมูลหรือ cache (ขึ้นกับการจัดการของคุณ)
        $user->update(['refresh_token' => $refreshToken]);

        return $refreshToken;
    }
}
