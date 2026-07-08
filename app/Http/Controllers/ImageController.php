<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    /**
     * On-the-fly image resizing (Glide). Serves files from storage/app/public,
     * resized/converted per query params (e.g. ?w=800&fm=webp), and caches the
     * result so each size is only generated once.
     */
    public function show(Request $request, string $path)
    {
        $server = ServerFactory::create([
            'source' => storage_path('app/public'),
            'cache' => storage_path('app/glide-cache'),
        ]);

        // Whitelist the manipulation params we allow, and clamp dimensions, so the
        // endpoint can't be abused to generate unlimited arbitrary sizes (cheap DoS guard).
        $params = $request->only(['w', 'h', 'fit', 'fm', 'q']);

        foreach (['w', 'h'] as $dimension) {
            if (isset($params[$dimension])) {
                $params[$dimension] = (string) min((int) $params[$dimension], 2000);
            }
        }

        // Generate (or reuse the cached) manipulated image, then stream it back.
        // Glide 3.x has no Laravel response factory, so we stream the cache file ourselves.
        try {
            $cachedPath = $server->makeImage($path, $params);
        } catch (FileNotFoundException) {
            abort(404);
        }

        $cache = $server->getCache();

        return response()->stream(
            function () use ($cache, $cachedPath) {
                fpassthru($cache->readStream($cachedPath));
            },
            200,
            [
                'Content-Type' => $cache->mimeType($cachedPath),
                'Content-Length' => (string) $cache->fileSize($cachedPath),
                'Cache-Control' => 'public, max-age=31536000',
            ],
        );
    }
}
