<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Services\Settings\HomeBannerService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    private HomeBannerService $homeBannerService;

    public function __construct(HomeBannerService $homeBannerService)
    {
        $this->homeBannerService = $homeBannerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $homeBanners = $this->homeBannerService->findAll($request->query());
            return response()->json([
                'status' => ['status' => 'success', 'message' => null],
                'data' => $homeBanners
            ]);
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
        try {
            $response = $this->homeBannerService->create($request);
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
            $response = $this->homeBannerService->update($id, $request);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => 'Home banner not found.'],
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $response = $this->homeBannerService->delete($id);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => 'Home banner not found.'],
                'data' => null
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => $e->getMessage()],
                'data' => null
            ], 500);
        }
    }
}
