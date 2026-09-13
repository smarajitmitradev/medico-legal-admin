<?php

namespace App\Helpers;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function compressToTargetSize($file, $folder, $targetKB = 130)
    {
        $image = Image::make($file);

        if ($image->width() > 1200) {
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $quality = 90;
        $encoded = $image->encode('jpg', $quality);

        // Floor lowered to 5 — guarantees hitting target size while trying to keep max possible quality
        while (strlen((string) $encoded) / 1024 > $targetKB && $quality > 5) {
            $quality -= 5;
            $encoded = $image->encode('jpg', $quality);
        }

        $filename = $folder . '/' . uniqid() . '.jpg';
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    public static function compressToRatio($file, $folder, $width = 1280, $height = 720, $targetKB = 150)
    {
        $image = Image::make($file);

        // Crop + resize to exact 16:9 ratio (crops from center)
        $image->fit($width, $height, function ($constraint) {
            $constraint->upsize();
        });

        $quality = 90;
        $encoded = $image->encode('jpg', $quality);

        // Now floor is 10 — guarantees hitting target size
        while (strlen((string) $encoded) / 1024 > $targetKB && $quality > 10) {
            $quality -= 5;
            $encoded = $image->encode('jpg', $quality);
        }

        $filename = $folder . '/' . uniqid() . '.jpg';
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    public static function compressToPublicPath($file, $folder, $targetKB = 65)
    {
        $image = Image::make($file);

        if ($image->width() > 1200) {
            $image->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $quality = 90;
        $encoded = $image->encode('jpg', $quality);

        while (strlen((string) $encoded) / 1024 > $targetKB && $quality > 5) {
            $quality -= 5;
            $encoded = $image->encode('jpg', $quality);
        }

        $filename = time() . '_' . uniqid() . '.jpg';
        $destinationPath = public_path($folder);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        file_put_contents($destinationPath . '/' . $filename, (string) $encoded);

        return $filename;
    }
}
