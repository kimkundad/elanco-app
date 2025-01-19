<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Services\Settings\FeaturedCourseService;
use Exception;
use Illuminate\Http\Request;

class FeaturedCourseController extends Controller
{
    private FeaturedCourseService $featuredCourseService;

    public function __construct(FeaturedCourseService $featuredCourseService)
    {
        $this->featuredCourseService = $featuredCourseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $featuredCourses = $this->featuredCourseService->findAll();
            return response()->json([
                'status' => ['status' => 'success', 'message' => null],
                'data' => $featuredCourses
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ]);
        }
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
        try {
            $response = $this->featuredCourseService->create($request);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ]);
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $response = $this->featuredCourseService->update($id, $request);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $response = $this->featuredCourseService->delete($id);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ]);
        }
    }
}
