<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\AnimalType;
use App\Models\quiz;
use App\Models\Survey;


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
