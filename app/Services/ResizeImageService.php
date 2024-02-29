<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;

class ResizeImageService
{

    private const WIDTHS = [400, 800, 1200];

    public function resize(string $path): void
    {

        $fullPath = 'storage/' . $path;

        $fileName = File::name($fullPath);
        $fileDir = File::dirname($fullPath);
        $fileExtension = File::extension($fullPath);

        $manager = new ImageManager(
            Driver::class
        );

        $image = $manager->read('storage/' . $path);

        foreach (self::WIDTHS as $width) {

            if ($image->width() > $width) {
                $image->scale($width);
                if (!in_array($fileExtension, ['jpg', 'jpeg'])) {
                    $image->toJpeg();
                }
                $image->save($fileDir .  '/' . $fileName . '_w' . $width . '.jpg');

                $imageWebp = $image->toWebp();
                $imageWebp->save($fileDir .  '/' . $fileName . '_w' . $width . '.webp');
            }

        }

    }

    public function getResizeImages(?string $path = ''): array
    {
        $images = [];

        if  (empty($path)) {
            return $images;
        }
        $fullPath = 'storage/' . $path;

        $fileName = File::name($fullPath);
        $fileDir = File::dirname($fullPath);

        foreach (self::WIDTHS as $width) {
            foreach (['webp', 'jpg'] as $ext) {
                $imagePath = $fileDir .  '/' . $fileName . '_w' . $width . '.' .$ext;
                if (File::exists($imagePath)) {
                    $images[$ext][$width] = $imagePath;
                }
            }

        }

        return $images;

    }

    public function deleteResizeImages(?string $path = ''): bool
    {
        $images = $this->getResizeImages($path);
        if (empty($images)) {
            return false;
        }

        $files = [];

        foreach ($images as $list) {
            $files = array_merge($files, $list);
        }

        return File::delete($files);
    }
}
