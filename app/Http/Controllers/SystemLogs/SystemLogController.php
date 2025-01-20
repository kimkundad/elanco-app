<?php

namespace App\Http\Controllers\SystemLogs;

use App\Http\Controllers\Controller;
use App\Http\Services\SystemLogs\SystemLogService;
use Exception;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    private SystemLogService $systemLogService;

    public function __construct(SystemLogService $systemLogService)
    {
        $this->systemLogService = $systemLogService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $response = $this->systemLogService->findAll();
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
