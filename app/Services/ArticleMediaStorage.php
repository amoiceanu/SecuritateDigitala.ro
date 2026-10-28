<?php

namespace App\Services;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ArticleMediaStorage
{
    public function store(UploadedFile $upload, array $metadata): MediaAsset
    {
        $sha256 = hash_file('sha256', $upload->getRealPath());
        $existing = MediaAsset::where('sha256', $sha256)->first();
        if ($existing) {
            return $existing;
        }

        $imageInfo = @getimagesize($upload->getRealPath());
        if (! $imageInfo) {
            throw ValidationException::withMessages(['image' => 'Fișierul încărcat nu este o imagine validă.']);
        }

        [$width, $height] = $imageInfo;
        if ($width * $height > 40_000_000) {
            throw ValidationException::withMessages(['image' => 'Imaginea depășește dimensiunea maximă de procesare.']);
        }

        $disk = Storage::disk('public');
        $variants = [];
        $filePath = null;
        $mimeType = $imageInfo['mime'] ?? $upload->getMimeType() ?? 'application/octet-stream';

        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $source = @imagecreatefromstring(file_get_contents($upload->getRealPath()));
            if ($source) {
                foreach ([480, 768, 1200, 1600, 1920] as $targetWidth) {
                    if ($targetWidth > $width) {
                        continue;
                    }

                    $targetHeight = (int) round($height * $targetWidth / $width);
                    $resized = imagecreatetruecolor($targetWidth, $targetHeight);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                    imagefill($resized, 0, 0, $transparent);
                    imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
                    ob_start();
                    $encoded = imagewebp($resized, null, 84);
                    $contents = ob_get_clean();
                    imagedestroy($resized);

                    if (! $encoded || $contents === false || $contents === '') {
                        continue;
                    }

                    $path = 'media/'.$sha256.'-'.$targetWidth.'.webp';
                    $disk->put($path, $contents);
                    $variants[(string) $targetWidth] = $path;
                    $filePath = $path;
                }

                imagedestroy($source);
                $mimeType = 'image/webp';
            }
        }

        if (! $filePath || $variants === []) {
            $extension = strtolower($upload->guessExtension() ?: 'jpg');
            $filePath = 'media/'.$sha256.'.'.$extension;
            $disk->putFileAs('media', $upload, basename($filePath));
            $variants[(string) $width] = $filePath;
        }

        return MediaAsset::create([
            ...$metadata,
            'file_path' => $filePath,
            'sha256' => $sha256,
            'variants' => $variants,
            'mime_type' => $mimeType,
            'width' => $width,
            'height' => $height,
        ]);
    }
}
