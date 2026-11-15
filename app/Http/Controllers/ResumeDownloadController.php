<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeDownloadController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse|StreamedResponse
    {
        $resume = Resume::active();

        abort_unless($resume && filled($resume->file_path), 404, 'No active resume is available yet.');
        abort_unless(Storage::disk('local')->exists($resume->file_path), 404, 'The resume file could not be found.');

        $path = Storage::disk('local')->path($resume->file_path);
        $headers = [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($request->routeIs('resume.preview')) {
            return response()->file($path, $headers + [
                'Content-Disposition' => 'inline; filename="'.$resume->download_filename.'"',
            ]);
        }

        return response()->download($path, $resume->download_filename, $headers);
    }
}
