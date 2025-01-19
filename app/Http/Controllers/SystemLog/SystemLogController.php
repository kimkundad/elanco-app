<?php

namespace App\Http\Controllers\SystemLog;

use App\Http\Controllers\Controller;
use App\Http\Services\SystemLog\SystemLogService;
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
        $systemLogs = $this->systemLogService->findAll();
        return response()->json(['error' => false, 'data' => $systemLogs]);
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
