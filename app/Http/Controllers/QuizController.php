<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\quiz;

use App\Models\answer;
use App\Models\question;


class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $objs = quiz::all();
        $data['objs'] = $objs;
        return view('admin.quiz.index', $data);
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
        $request->validate([
            'quiz_id' => 'required|string|unique:quizzes,quiz_id',
            'expire_date' => 'nullable|date',
            'questions_title' => 'required|string|max:255',
            'pass_percentage' => 'required|integer|min:0|max:100',
            'certificate' => 'required|boolean',
            'point_cpd' => 'nullable|integer',
        ]);

        // Save data to database

        try {

           $objs = new quiz();
           $objs->quiz_id = $request->quiz_id;
           $objs->expire_date = $request->expire_date;
           $objs->questions_title = $request->questions_title;
           $objs->pass_percentage = $request->pass_percentage;
           $objs->certificate = $request->certificate === 'Yes' ? true : false;
           $objs->point_cpd = $request->point_cpd;
           $objs->save();

           return redirect(url('admin/quiz'))->with('add_success','เพิ่ม เสร็จเรียบร้อยแล้ว');

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
    public function update(Request $request, string $id)
    {
        //

        $request->validate([
            'quiz_id' => 'required',
            'expire_date' => 'nullable|date',
            'questions_title' => 'required|string|max:255',
            'pass_percentage' => 'required|integer|min:0|max:100',
            'certificate' => 'required',
            'point_cpd' => 'nullable|integer',
        ]);

        // Save data to database

        try {

       //     dd($request->all());

           $objs = quiz::find($id);
           $objs->quiz_id = $request->quiz_id;
           $objs->expire_date = $request->expire_date;
           $objs->questions_title = $request->questions_title;
           $objs->pass_percentage = $request->pass_percentage;
           $objs->certificate = $request->certificate == '1' ? true : false;
           $objs->point_cpd = $request->point_cpd;
           $objs->save();

           return redirect(url('admin/quiz/'.$id.'/edit'))->with('edit_success','แก้ไข เสร็จเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['database' => 'Failed to save course data: ' . $e->getMessage()]);
        }
    }


    public function PostQuestion(Request $request, string $id){


        $request->validate([
            'detail' => 'required|string',
        ]);

      // model answer
      // model question;
        //    dd($request->all());
           $objs = new question();
           $objs->quiz_id = $id;
           $objs->detail = $request->detail;
           $objs->save();

           $question_id = $objs->id;

           // ตรวจสอบว่ามีคำตอบถูกส่งมาหรือไม่
           if ($request->has('answer')) {
            $answers = $request->answer; // คำตอบ
            $answers_status = $request->answers_status; // สถานะ checkbox

            foreach ($answers as $index => $answer_text) {

                if($answer_text !== null){

                    $answer = new Answer();
                    $answer->questions_id = $question_id;
                    $answer->answers = $answer_text;

                    // ตรวจสอบสถานะ checkbox โดยตรง
                    $answer->answers_status = isset($answers_status[$index]) ? $answers_status[$index] : 0;

                    $answer->save();

                }

            }
        }

            return redirect()->back()->with('success', 'Question and Answers added successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
