<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\AnimalType;
use App\Models\quiz;
use App\Models\Survey;
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
