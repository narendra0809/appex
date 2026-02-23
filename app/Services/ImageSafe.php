<?php

namespace App\Services;

class ImageSafe
{
    public static function toJpeg(string $sourcePath): string
    {
        $info = getimagesize($sourcePath);

        if ($info === false) {
            throw new \Exception('Invalid image file');
        }

        switch ($info['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;

            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;

            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;

            default:
                throw new \Exception('Unsupported image mime: ' . $info['mime']);
        }

        // Force RGB truecolor
        $trueColor = imagecreatetruecolor(
            imagesx($image),
            imagesy($image)
        );

        imagecopy($trueColor, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

        $safePath = storage_path('app/word_logo_safe.jpg');

        imagejpeg($trueColor, $safePath, 90);

        imagedestroy($image);
        imagedestroy($trueColor);

        return $safePath;
    }
}
