<?php

namespace App\Providers\Image;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageUploadServiceProvider
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('DO_SPACES_BASE_URL');
    }

    public function uploadImages(array $images, $uploadPath)
    {
        $uploadedPaths = [];

        foreach ($images as $key => $image) {
            if ($image) {
                $resizedImage = Image::make($image->getRealPath());
                $resizedImage->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $resizedImage->stream();

                $filename = time() . '_' . $image->getClientOriginalName();
                $filePath = "{$uploadPath}/{$filename}";

                Storage::disk('do_spaces')->put(
                    $filePath,
                    $resizedImage->__toString(),
                    'public'
                );

                $uploadedPaths[$key] = "{$this->baseUrl}/$filePath";
            }
        }

        return $uploadedPaths;
    }

    public function restoreImages(array $filePaths, array $contents)
    {
        foreach ($filePaths as $index => $filePath) {
            if (!Storage::disk('do_spaces')->exists($filePath)) {
                Storage::disk('do_spaces')->put($filePath, $contents[$index], 'public');
            }
        }
    }

    public function removeFiles(array $filePaths)
    {
        $deletedFiles = [];

        foreach ($filePaths as $filePath) {
            if (Storage::disk('do_spaces')->exists($filePath)) {
                if (Storage::disk('do_spaces')->delete($filePath)) {
                    $deletedFiles[] = $filePath;
                }
            }
        }

        return $deletedFiles;
    }
}
