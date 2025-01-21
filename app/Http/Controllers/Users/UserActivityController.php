<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Services\Users\UserActivityService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    private UserActivityService $userActivityService;

    public function __construct(UserActivityService $userActivityService)
    {
        $this->userActivityService = $userActivityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $response = $this->userActivityService->findAll($request->query());
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ], 500);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $systemLog = $this->userActivityService->findById($id);
            return response()->json([
                'status' => ['status' => 'success', 'message' => null],
                'data' => $systemLog
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => 'User activity not found.'],
                'data' => null
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ], 500);
        }
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
