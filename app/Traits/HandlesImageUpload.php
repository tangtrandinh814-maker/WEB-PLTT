<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait HandlesImageUpload
{
    /**
     * Upload image and create thumbnail
     *
     * @param UploadedFile $file
     * @param string $path
     * @param int $thumbnailWidth
     * @return array ['image_path' => string, 'thumbnail_path' => string]
     */
    public function uploadImage(UploadedFile $file, string $path = 'articles', int $thumbnailWidth = 400): array
    {
        try {
            // Validate file
            if (!$this->validateImage($file)) {
                throw new \Exception('Invalid image file');
            }

            // Create filename with timestamp
            $filename = time() . '_' . $file->getClientOriginalName();
            $filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);

            // Store original image
            $imagePath = $file->storeAs($path, $filename, 'public');

            // Create thumbnail
            $image = Image::make(storage_path('app/public/' . $imagePath));
            $thumbnailPath = null;

            if ($image->width() > $thumbnailWidth) {
                $thumbnail = $image->resize($thumbnailWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME) . '_thumb.' . pathinfo($filename, PATHINFO_EXTENSION);
                $thumbnailPath = $path . '/' . $thumbnailFilename;

                $thumbnail->save(storage_path('app/public/' . $thumbnailPath));
            }

            return [
                'image_path' => $imagePath,
                'thumbnail_path' => $thumbnailPath ?? $imagePath,
            ];
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return [
                'image_path' => null,
                'thumbnail_path' => null,
            ];
        }
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @return bool
     */
    protected function validateImage(UploadedFile $file): bool
    {
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        return $file->getSize() <= $maxSize &&
            in_array($file->getMimeType(), $allowedMimes);
    }

    /**
     * Delete image files
     *
     * @param string|null $imagePath
     * @param string|null $thumbnailPath
     * @return void
     */
    public function deleteImages(?string $imagePath = null, ?string $thumbnailPath = null): void
    {
        try {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        } catch (\Exception $e) {
            \Log::error('Image delete error: ' . $e->getMessage());
        }
    }

    /**
     * Get image URL
     *
     * @param string|null $imagePath
     * @return string
     */
    public function getImageUrl(?string $imagePath): string
    {
        if (!$imagePath) {
            return asset('images/placeholder.png');
        }

        return Storage::disk('public')->url($imagePath);
    }
}
