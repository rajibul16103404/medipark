<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait HasImageUrl
{
    /**
     * Convert a single image path to a full URL.
     */
    protected function getFullImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // If it's already a full URL (starts with http:// or https://), return it as is
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        // Cache the generated URL for 1 year (images don't change URL frequently)
        $cacheKey = 'image_url_'.md5($path);

        return Cache::remember($cacheKey, 31536000, function () use ($path) {
            // Normalize the path - ensure it starts with /
            $normalizedPath = str_starts_with($path, '/') ? $path : '/'.$path;

            // If path doesn't start with /storage, prepend it for storage files
            if (! str_starts_with($normalizedPath, '/storage')) {
                // Check if it's a storage path (no leading slash or relative)
                $normalizedPath = '/storage/'.ltrim($path, '/');
            }

            // Generate full URL using config('app.url') to ensure absolute URL
            $baseUrl = rtrim(config('app.url'), '/');

            return $baseUrl.$normalizedPath;
        });
    }

    /**
     * Convert multiple image paths to full URLs.
     *
     * @param  array<string|null>|null  $paths
     * @return array<string|null>|null
     */
    protected function getFullImageUrls(?array $paths): ?array
    {
        if (empty($paths)) {
            return null;
        }

        return array_map(function ($path) {
            return $this->getFullImageUrl($path);
        }, $paths);
    }
}
