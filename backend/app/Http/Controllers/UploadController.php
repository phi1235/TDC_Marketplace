<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'images' => ['required'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
        ]);

        $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];

        $results = [];
        $manager = new ImageManager(new Driver());

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $originalExtension = strtolower($file->getClientOriginalExtension());
            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeBaseName = preg_replace('/[^a-zA-Z0-9-_]/', '-', $baseName);
            $timestamp = now()->format('YmdHis');

            $dir = 'uploads/'.date('Y/m/d');

            // Read image
            $image = $manager->read($file->getPathname());

            // Cap max dimensions to save bandwidth (e.g., 1600px longest side)
            $imageMax = clone $image;
            $imageMax->scaleDown(1600);

            // Save optimized original format
            $optimizedFilename = $safeBaseName.'-'.$timestamp.'.'.$originalExtension;
            $optimizedPath = $dir.'/'.$optimizedFilename;

            $optQuality = in_array($originalExtension, ['jpg','jpeg']) ? 80 : 90; // png keeps higher quality
            $binaryOptimized = $imageMax->encodeByExtension($originalExtension, quality: $optQuality);
            Storage::disk('public')->put($optimizedPath, $binaryOptimized);

            // Save WebP variant
            $webpFilename = $safeBaseName.'-'.$timestamp.'.webp';
            $webpPath = $dir.'/'.$webpFilename;
            $binaryWebp = $imageMax->toWebp(quality: 80);
            Storage::disk('public')->put($webpPath, $binaryWebp);

            // Generate a medium thumbnail (e.g., 800px)
            $imageMedium = clone $image;
            $imageMedium->scaleDown(800);
            $mediumFilename = $safeBaseName.'-'.$timestamp.'-md.'.$originalExtension;
            $mediumPath = $dir.'/'.$mediumFilename;
            $binaryMedium = $imageMedium->encodeByExtension($originalExtension, quality: $optQuality);
            Storage::disk('public')->put($mediumPath, $binaryMedium);

            // Medium WebP
            $mediumWebpFilename = $safeBaseName.'-'.$timestamp.'-md.webp';
            $mediumWebpPath = $dir.'/'.$mediumWebpFilename;
            $binaryMediumWebp = $imageMedium->toWebp(quality: 80);
            Storage::disk('public')->put($mediumWebpPath, $binaryMediumWebp);

            // Small thumbnail (e.g., 400px)
            $imageSmall = clone $image;
            $imageSmall->scaleDown(400);
            $smallFilename = $safeBaseName.'-'.$timestamp+'-sm.'.$originalExtension;
            $smallPath = $dir.'/'.$smallFilename;
            $binarySmall = $imageSmall->encodeByExtension($originalExtension, quality: $optQuality);
            Storage::disk('public')->put($smallPath, $binarySmall);

            // Small WebP
            $smallWebpFilename = $safeBaseName.'-'.$timestamp+'-sm.webp';
            $smallWebpPath = $dir.'/'.$smallWebpFilename;
            $binarySmallWebp = $imageSmall->toWebp(quality: 80);
            Storage::disk('public')->put($smallWebpPath, $binarySmallWebp);

            $results[] = [
                'original' => Storage::disk('public')->url($optimizedPath),
                'webp' => Storage::disk('public')->url($webpPath),
                'medium' => Storage::disk('public')->url($mediumPath),
                'medium_webp' => Storage::disk('public')->url($mediumWebpPath),
                'small' => Storage::disk('public')->url($smallPath),
                'small_webp' => Storage::disk('public')->url($smallWebpPath),
            ];
        }

        return response()->json([
            'message' => 'Upload thành công',
            'files' => $results,
        ], 201);
    }
}


