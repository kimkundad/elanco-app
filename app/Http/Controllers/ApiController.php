<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

use App\Models\course;
use App\Models\quiz;
use App\Models\QuizAttempt;
use App\Models\CourseAction;
use App\Models\Country;


class ApiController extends Controller
{
    //


    public function getCourseAction($id)
{
    $courseAction = CourseAction::where('course_id', $id)->first();

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
        'lastTimestamp' => (int) $courseAction->lastTimestamp, // แปลงเป็น int
        'isFinishVideo' => $courseAction->isFinishVideo == 1,
        'isFinishQuiz' => $courseAction->isFinishQuiz == 1,
        'isDownloadCertificate' => $courseAction->isDownloadCertificate == 1,
        'isReview' => $courseAction->isReview == 1,
    ]);
}


        public function getCertificate(Request $request, $id)
        {
            try {
                // ตรวจสอบและดึงผู้ใช้จาก JWT
                $user = JWTAuth::parseToken()->authenticate();

                // URL ของไฟล์ PDF
                $pdfUrl = 'https://kimspace2.sgp1.cdn.digitaloceanspaces.com/elanco/certificate_pdf.pdf';

                // ดาวน์โหลดไฟล์ PDF
                $pdfContent = file_get_contents($pdfUrl);

                if (!$pdfContent) {
                    return response()->json([
                        'error' => 'File not found',
                        'message' => 'Unable to download the certificate file.',
                    ], 404);
                }

                // แปลงไฟล์ PDF เป็น Base64
                $pdfBase64 = base64_encode($pdfContent);

                // อัปเดตสถานะ isDownloadCertificate เป็น true
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

                // ส่งข้อมูล Base64 กลับใน response
                return response()->json([
                    'certificate_base64' => $pdfBase64,
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
                // กรณีเกิดข้อผิดพลาดทั่วไป
                return response()->json([
                    'error' => 'Internal Server Error',
                    'message' => $e->getMessage(),
                ], 500);
            }
        }


    public function courses(Request $request)
    {

        try {
            // ตรวจสอบและดึงผู้ใช้จาก JWT
            $user = JWTAuth::parseToken()->authenticate();

            $userCountryId = $user->country;

            $courses = course::whereHas('countries', function ($query) use ($userCountryId) {
                $query->where('country_id', $userCountryId);
            })
            ->with([
                'countries' => function ($query) {
                    $query->select('countries.id', 'countries.name'); // เลือกเฉพาะฟิลด์ที่ต้องการ
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

                // แปลงข้อมูลในรูปแบบที่ต้องการ
                $course->thumbnail = $course->course_img;
                unset($course->course_img); // ลบฟิลด์ `course_img` ที่ไม่ต้องการ

                return [
                    'id' => $course->id,
                    'course_title' => $course->course_title,
                    'course_description' => $course->course_description,
                    'course_preview' => $course->course_preview,
                    'duration' => $course->duration,
                    'url_video' => $course->url_video,
                    'status' => $course->status,
                    'ratting' => number_format($course->ratting,1),
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
                    'course_title' => $course->course_title,
                    'course_description' => $course->course_description,
                    'course_preview' => $course->course_preview,
                    'duration' => $course->duration,
                    'url_video' => $course->url_video,
                    'status' => $course->status,
                    'ratting' => number_format($course->ratting,1),
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
            $courses = Course::whereHas('countries', function ($query) use ($userCountryId) {
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
                    'course_title' => $course->course_title,
                    'course_description' => $course->course_description,
                    'course_preview' => $course->course_preview,
                    'duration' => $course->duration,
                    'url_video' => $course->url_video,
                    'status' => $course->status,
                    'ratting' => number_format($course->ratting,1),
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



    public function exploreCourses(Request $request)
{
    try {
        // รับ country flag จาก request
        $countryFlag = $request->country;

        // ตรวจสอบว่า country flag มีอยู่ในฐานข้อมูลหรือไม่
        $country = Country::where('flag', $countryFlag)->first();
      //  dd($request->all());
        if (!$country) {
            return response()->json([
                'error' => 'Country not found',
                'message' => 'The specified country flag does not exist.',
            ], 404);
        }

        // ดึงข้อมูลคอร์สที่มี featured == 1 และเชื่อมโยงกับประเทศที่ระบุ
        $courses = Course::where('featured', 1)
            ->whereHas('countries', function ($query) use ($country) {
                $query->where('country_id', $country->id);
            })
            ->with([
                'countries',
                'mainCategories',
                'subCategories',
                'animalTypes',
                'itemDes',
                'Speaker',
                'referances'
            ])
            ->get();

        // จัดรูปแบบข้อมูลสำหรับการส่งกลับ
        $formattedCourses = $courses->map(function ($course) {
            return [
                'id' => $course->id,
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




public function courseDetail($id)
{
    try {
        // ตรวจสอบและดึงผู้ใช้จาก JWT
        $user = JWTAuth::parseToken()->authenticate();

        // ค้นหา Course โดย ID พร้อมโหลดความสัมพันธ์
        $course = Course::with([
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
        $relatedCourses = Course::whereHas('countries', function ($query) use ($course) {
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





    public function getCourseQuiz($id)
    {
        try {
            // ค้นหา course
            $course = Course::findOrFail($id);

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
        $course = Course::where('id_quiz', $quiz->id)->first();
        if (!$course) {
            return response()->json([
                'error' => 'Invalid Quiz',
                'message' => 'No course associated with this quiz.',
            ], 400);
        }

        $totalPoints = $quiz->point_cpd; // คะแนนเต็มจาก point_cpd
        $passPercentage = $quiz->pass_percentage; // เปอร์เซ็นต์ที่ต้องผ่าน
        $correctPoints = 0;

        // ตรวจสอบคำตอบที่ส่งมา
        foreach ($quiz->questions as $question) {
            $submittedAnswerId = collect($request->answers)
                ->firstWhere(fn($answerId) => $question->answers->pluck('id')->contains($answerId));

            $correctAnswer = $question->answers->firstWhere('answers_status', 1);

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
                'lastTimestamp' => (int) $courseAction->lastTimestamp,
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
                'lastTimestamp' => (int) $courseAction->lastTimestamp,
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





}
