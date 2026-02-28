<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Serve files from the public storage disk when public/storage symlink is missing.
     * Path is passed as a URL-safe base64 string.
     */
    public function storageImage(Request $request, $encoded)
    {
        // Convert URL-safe base64 back to regular base64
        $b64 = strtr($encoded, '-_', '+/');
        $mod4 = strlen($b64) % 4;
        if ($mod4) {
            $b64 .= str_repeat('=', 4 - $mod4);
        }
        $path = base64_decode($b64);

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
