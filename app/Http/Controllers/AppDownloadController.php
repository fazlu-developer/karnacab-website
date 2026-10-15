<?php

namespace App\Http\Controllers;

use App\Services\AppRelease;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppDownloadController extends Controller
{
    public function customer(): BinaryFileResponse
    {
        return $this->apk(AppRelease::customer(), 'Customer app APK is not published yet.');
    }

    public function driver(): BinaryFileResponse
    {
        return $this->apk(AppRelease::driver(), 'Driver app APK is not published yet.');
    }

    /**
     * @param  array{path: string, filename: string, bytes: int, version: string}|null  $file
     */
    private function apk(?array $file, string $missing): BinaryFileResponse
    {
        abort_unless($file && is_file($file['path']), 404, $missing);

        return response()->download($file['path'], $file['filename'], [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
