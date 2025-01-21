<?php

namespace App\Http\Services\Settings;

use App\Http\Repositories\Settings\PageBannerRepository;
use App\Http\Utils\ArrayKeyConverter;
use App\Providers\Image\ImageUploadService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PageBannerService
{
    private PageBannerRepository $pageBannerRepository;

    public function __construct(PageBannerRepository $pageBannerRepository)
    {
        $this->pageBannerRepository = $pageBannerRepository;
    }

    public function findAll(array $queryParams)
    {
        $queryParams = ArrayKeyConverter::convertToSnakeCase($queryParams);

        return $this->pageBannerRepository
            ->findAll($queryParams)
            ->map->formatIncludingCreatedUserAndUpdatedUserAndCountry();
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

            $maxOrder = $this->pageBannerRepository->findMaxOrder();
            $data['order'] = $maxOrder ? $maxOrder + 1 : 1;

            $banner = $this->pageBannerRepository->save($data);

            $uploadedImages = $this->uploadImages($request, $banner->id);
            $data = array_merge($data, $uploadedImages);

            $this->pageBannerRepository->update($banner->id, $data);

            DB::commit();

            return [
                'status' => ['status' => 'success', 'message' => 'Page banner created successfully.'],
                'data' => $banner
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create page banner: ' . $e->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $this->validateRequest($request);

        DB::beginTransaction();
        try {
            $data = ArrayKeyConverter::convertToSnakeCase($request->all());
            $data['updated_by'] = Auth::id();

            $banner = $this->pageBannerRepository->findById($id);
            if (!$banner) {
                throw new Exception('Page banner not found.');
            }

            $user = $request->user();
            $data['country_id'] = $user->countryDetails->id;

            $currentOrder = $banner->order;
            $newOrder = $data['order'] ?? $currentOrder;

            $newOrder = max(1, $newOrder);

            $maxOrder = $this->pageBannerRepository->findMaxOrder();
            if ($newOrder > $maxOrder) {
                $newOrder = $maxOrder;
            }

            if ($newOrder !== $currentOrder) {
                if ($newOrder > $currentOrder) {
                    $this->pageBannerRepository->shiftOrderRange($currentOrder + 1, $newOrder, -1);
                } elseif ($newOrder < $currentOrder) {
                    $this->pageBannerRepository->shiftOrderRange($newOrder, $currentOrder - 1, 1);
                }
            }

            $oldImages = array_filter([
                'desktop_image' => $banner->desktop_image,
                'mobile_image' => $banner->mobile_image,
            ]);

            $uploadedImages = $this->uploadImages($request, $id);
            $data = array_merge($data, $uploadedImages);

            $data['order'] = $newOrder;
            $updatedBanner = $this->pageBannerRepository->update($id, $data);

            $filesToRemove = array_intersect_key($oldImages, $uploadedImages);
            if (!empty($filesToRemove)) {
                ImageUploadService::removeFiles(array_values($filesToRemove));
            }

            DB::commit();

            return [
                'status' => ['status' => 'success', 'message' => 'Page banner updated successfully.'],
                'data' => $updatedBanner
            ];
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($filesToRemove ?? [] as $filePath) {
                if (!Storage::disk('do_spaces')->exists($filePath)) {
                    $content = file_get_contents($filePath);
                    ImageUploadService::restoreImage($filePath, $content);
                }
            }

            throw new Exception('Failed to update page banner: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $banner = $this->pageBannerRepository->findById($id);

            if (!$banner) {
                throw new Exception('Page banner not found.');
            }

            $filesToDelete = array_filter([
                $banner->desktop_image,
                $banner->mobile_image,
            ]);

            $this->pageBannerRepository->shiftOrderRange($banner->order + 1, null, -1);
            $this->pageBannerRepository->delete($id);

            $deletedFiles = [];
            if (!empty($filesToDelete)) {
                $deletedFiles = ImageUploadService::removeFiles($filesToDelete);
            }

            DB::commit();

            return [
                'status' => ['status' => 'success', 'message' => 'Page banner and associated files deleted successfully.'],
                'data' => null
            ];
        } catch (Exception $e) {
            DB::rollBack();

            if (!empty($deletedFiles)) {
                foreach ($deletedFiles as $filePath) {
                    $content = file_get_contents($filePath);
                    ImageUploadService::restoreImage($filePath, $content);
                }
            }

            throw new Exception('Failed to delete page banner: ' . $e->getMessage());
        }
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'desktopImage' => 'nullable|file|image',
            'mobileImage' => 'nullable|file|image',
            'status' => 'required|in:public,private',
            'order' => 'nullable|integer',
            'countryId' => 'nullable|exists:countries,id',
        ]);
    }

    private function uploadImages(Request $request, $id)
    {
        $basePath = 'elanco/page-banners/' . $id;
        $images = [
            'desktop_image' => $request->file('desktopImage'),
            'mobile_image' => $request->file('mobileImage'),
        ];

        return ImageUploadService::uploadImages($images, $basePath);
    }
}
