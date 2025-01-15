<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use Carbon\Carbon;

class SurvayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        //
        $objs = Survey::with([
            'courses.countries', // ดึงข้อมูล countries ผ่าน courses
        ])
        ->withCount([
            'courses as total_courses', // นับจำนวนคอร์สทั้งหมดที่ใช้ Survey นี้
            'responses as total_responses', // นับจำนวนผู้ที่ทำ Survey นี้
        ])
        ->paginate(4);

       // dd($objs);

        return view('admin.survey.index', ['objs' => $objs, 'search' => $search]);
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
        //

         //
         $request->validate([
            'survey_id' => 'required|string|unique:surveys,survey_id', // รหัส Survey ห้ามซ้ำ
            'expire_date' => 'nullable|date', // ตรวจสอบรูปแบบวันที่
            'survey_title' => 'required|string|max:255', // ชื่อ Survey ห้ามว่าง
            'survey_detail' => 'nullable|string', // รายละเอียด
        ]);

        // Save data to database

        try {

            $survey = new Survey();
            $survey->survey_id = $request->survey_id;
            $survey->expire_date = Carbon::createFromFormat('d-m-Y', $request->expire_date)->format('Y-m-d');
            $survey->survey_title = $request->survey_title;
            $survey->survey_detail = $request->survey_detail;

            // Save to the database
            $survey->save();

           return redirect(url('admin/survey'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');

    } catch (\Exception $e) {
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
        //
        $objs = Survey::find($id);
        $data['url'] = url('admin/survey/'.$id);
        $data['method'] = "put";
        $data['objs'] = $objs;
        return view('admin.survey.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'survey_id' => 'required',
            'expire_date' => 'nullable|date_format:d-m-Y', // Ensures correct date format
            'survey_title' => 'required|string|max:255',
            'survey_detail' => 'nullable|string',
        ]);

        try {
            // Find the survey by ID
            $survey = Survey::findOrFail($id);

            // Format the expire_date for saving in the database
            $formattedDate = $request->expire_date ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->expire_date)->toDateString() : null;

            // Update the survey record
            $survey->update([
                'survey_id' => $validatedData['survey_id'],
                'expire_date' => $formattedDate,
                'survey_title' => $validatedData['survey_title'],
                'survey_detail' => $validatedData['survey_detail'],
            ]);

            // Redirect with success message
            return redirect()->route('survey.index')->with('success', 'Survey updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return error if the survey is not found
            return redirect()->back()->withErrors(['error' => 'Survey not found.']);
        } catch (\Exception $e) {
            // Handle general errors
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
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
