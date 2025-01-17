<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Services\Settings\PageBannerService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PageBannerController extends Controller
{
    private PageBannerService $pageBannerService;

    public function __construct(PageBannerService $pageBannerService)
    {
        $this->pageBannerService = $pageBannerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $pageBanners = $this->pageBannerService->findAll($request->query());
            return response()->json([
                'status' => ['status' => 'success', 'message' => null],
                'data' => $pageBanners
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
            $response = $this->pageBannerService->create($request);
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
            $response = $this->pageBannerService->update($id, $request);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => 'Page banner not found.'],
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
            $response = $this->pageBannerService->delete($id);
            return response()->json($response);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => ['status' => 'error', 'message' => 'Page banner not found.'],
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
