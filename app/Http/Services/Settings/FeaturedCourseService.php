<?php

namespace App\Http\Services\Settings;

use App\Http\Repositories\Settings\FeaturedCourseRepository;
use App\Http\Utils\ArrayKeyConverter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeaturedCourseService
{
    private FeaturedCourseRepository $featuredCourseRepository;

    public function __construct(FeaturedCourseRepository $featuredCourseRepository)
    {
        $this->featuredCourseRepository = $featuredCourseRepository;
    }

    public function findAll()
    {
        return $this->featuredCourseRepository->findAll()->map->formatIncludingCourseAndCountry();
    }

    public function create(Request $request)
    {
        $this->validateRequest($request);

        DB::beginTransaction();
        try {
            $data = ArrayKeyConverter::convertToSnakeCase($request->all());
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $user = $request->user();
            $data['country_id'] = $user->countryDetails->id;

            $maxOrder = $this->featuredCourseRepository->findMaxOrder();
            $data['order'] = $maxOrder ? $maxOrder + 1 : 1;

            $featuredCourse = $this->featuredCourseRepository->save($data);

            DB::commit();

            return [
                'status' => ['status' => 'success', 'message' => 'Featured course created successfully.'],
                'data' => $featuredCourse
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create featured course: ' . $e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $this->validateRequest($request);

        DB::beginTransaction();
        try {
            $data = ArrayKeyConverter::convertToSnakeCase($request->all());
            $data['updated_by'] = Auth::id();

            $featuredCourse = $this->featuredCourseRepository->findById($id);
            if (!$featuredCourse) {
                throw new Exception('Featured course not found.');
            }

            $user = $request->user();
            $data['country_id'] = $user->countryDetails->id;

            $currentOrder = $featuredCourse->order;
            $newOrder = $data['order'] ?? $currentOrder;

            $newOrder = max(1, $newOrder);

            $maxOrder = $this->featuredCourseRepository->findMaxOrder();
            $newOrder = min($newOrder, $maxOrder + 1);

            if ($newOrder !== $currentOrder) {
                if ($newOrder > $currentOrder) {
                    $this->featuredCourseRepository->shiftOrderRange($currentOrder + 1, $newOrder, -1);
                } elseif ($newOrder < $currentOrder) {
                    $this->featuredCourseRepository->shiftOrderRange($newOrder, $currentOrder - 1, 1);
                }
            }

            $updatedFeaturedCourse = $this->featuredCourseRepository->update($id, $data);

            DB::commit();

            return [
                'status' => ['status' => 'success', 'message' => 'Featured course updated successfully.'],
                'data' => $updatedFeaturedCourse
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update featured course: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $featuredCourse = $this->featuredCourseRepository->findById($id);

            if (!$featuredCourse) {
                throw new Exception('Featured course not found.');
            }

            $this->featuredCourseRepository->delete($id);

            DB::commit();

            return [
                'status' => ['status' => 'success', 'message' => 'Featured course deleted successfully.'],
                'data' => null
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete featured course: ' . $e->getMessage());
        }
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'courseId' => 'required|exists:courses,id',
            'countryId' => 'nullable|exists:countries,id',
            'status' => 'required|in:public,private',
            'order' => 'nullable|integer',
        ]);
    }
}
