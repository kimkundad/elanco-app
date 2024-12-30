<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\course;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\AnimalType;
use App\Models\quiz;
use App\Models\itemDes;

use App\Models\Speaker;
use App\Models\Referance;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $objs = course::with(['countries', 'mainCategories', 'subCategories', 'animalTypes'])->get();
        $data['objs'] = $objs;
      //  dd($objs);
        return view('admin.course.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $quiz = Quiz::all();
        $countries = Country::all();
        $mainCategories = MainCategory::all();
        $subCategories = SubCategory::all();
        $animalTypes = AnimalType::all();

        $data = [
            'quiz' => $quiz,
            'countries' => $countries,
            'mainCategories' => $mainCategories,
            'subCategories' => $subCategories,
            'animalTypes' => $animalTypes,
        ];

        return view('admin.course.create', $data);
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
        //
       // dd($request->all());

        $this->validate($request, [
            'course_title' => 'required|string|max:255',
            'course_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'course_preview' => 'nullable|string',
            'status' => 'nullable|integer',
            'duration' => 'nullable|string',
            'url_video' => 'nullable|url',
            'id_quiz' => 'required',
            'choice' => 'nullable|array', // Validate choice as an array
            'choice.*' => 'nullable|string|max:255', // Validate each choice
            'reference_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'file_product' => 'nullable|file|max:5048',
            'speaker_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
            'file_speaker' => 'nullable|file|max:5048',
        ]);

        $filename = null;

        DB::beginTransaction();

        try {

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
           $course->save();

           if ($request->has('choice')) {
            foreach ($request->choice as $choice) {
                if (!is_null($choice)) {
                    $itemDes = new itemDes();
                    $itemDes->course_id = $course->id;
                    $itemDes->detail = $choice;
                    $itemDes->save();
                }
            }
            }

            // **3. บันทึก Speaker**
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

        // **4. บันทึก Referance**
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

        return redirect(url('admin/course'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');

        } catch (\Exception $e) {

            DB::rollback();

            return redirect()->back()->withErrors(['database' => 'Failed to save course data: ' . $e->getMessage()]);
        }
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
        // ค้นหาคอร์สที่ต้องการแก้ไข
        $course = course::with(['countries', 'mainCategories', 'subCategories', 'animalTypes'])->findOrFail($id);

        // เตรียมข้อมูลที่จำเป็นสำหรับการแสดงผล
        $data['course'] = $course;
        $data['countries'] = Country::all(); // ดึงรายชื่อประเทศทั้งหมด
        $data['mainCategories'] = MainCategory::all(); // ดึงหมวดหมู่หลักทั้งหมด
        $data['subCategories'] = SubCategory::all(); // ดึงหมวดหมู่ย่อยทั้งหมด
        $data['animalTypes'] = AnimalType::all(); // ดึงประเภทสัตว์ทั้งหมด
        $data['quiz'] = Quiz::all();

        // URL และ Method สำหรับฟอร์มแก้ไข
        $data['url'] = url('admin/course/'.$id);
        $data['method'] = "put";

        // ส่งข้อมูลไปยัง View
        return view('admin.course.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    // Validation
    $this->validate($request, [
        'course_title' => 'required|string|max:255',
        'course_preview' => 'nullable|string',
        'status' => 'nullable|integer',
        'duration' => 'nullable|string',
        'url_video' => 'nullable|url',
        'id_quiz' => 'required',
        'course_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4048', // ไม่บังคับ
    ]);

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
        $course->save();

        // อัปเดตความสัมพันธ์ใน Pivot Tables
        $course->countries()->sync($request->countries ?? []);
        $course->mainCategories()->sync($request->main_categories ?? []);
        $course->subCategories()->sync($request->sub_categories ?? []);
        $course->animalTypes()->sync($request->animal_types ?? []);

        DB::commit();

        return redirect(url('admin/course/'.$id.'/edit'))->with('update_success', 'แก้ไขข้อมูลสำเร็จ');
    } catch (\Exception $e) {
        DB::rollback();

        return redirect()->back()->withErrors(['database' => 'Failed to update course data: ' . $e->getMessage()]);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
