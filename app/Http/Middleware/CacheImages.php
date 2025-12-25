<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheImages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if the request is for an image file
        if ($this->isImageRequest($request)) {
            // Set cache headers for images (1 year cache, public)
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
            $response->headers->set('Pragma', 'public');

            // Add ETag support if the response has content
            if ($response->getContent() && method_exists($response, 'setEtag')) {
                $etag = md5($response->getContent());
                $response->setEtag($etag);

                // Check if client has a matching ETag (304 Not Modified)
                if ($response->isNotModified($request)) {
                    return $response;
                }
            }
        }

        return $response;
    }

    /**
     * Check if the request is for an image file.
     */
    private function isImageRequest(Request $request): bool
    {
        $path = $request->path();
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];

        return in_array($extension, $imageExtensions) || str_starts_with($path, 'storage/');
    }
}
