<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function show(string $path): StreamedResponse|BinaryFileResponse
    {
        $path = ltrim($path, '/');

        abort_unless(Storage::disk('public')->exists($path), 404);

        $response = Storage::disk('public')->response($path);
        $response->headers->set('Cache-Control', 'public, max-age=604800, immutable');
        return $response;
    }
}
