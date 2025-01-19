<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Services\Users\UserActivityService;
use Exception;
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
    public function index()
    {
        try {
            $userActivities = $this->userActivityService->findAll();
            return response()->json([
                'status' => ['status' => 'success', 'message' => null],
                'data' => $userActivities
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
