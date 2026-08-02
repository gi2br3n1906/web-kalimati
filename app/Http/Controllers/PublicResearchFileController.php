<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ResearchFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicResearchFileController extends Controller
{
    public function download(ResearchFile $researchFile): BinaryFileResponse
    {
        return $this->respond($researchFile, false);
    }

    public function preview(ResearchFile $researchFile): BinaryFileResponse
    {
        abort_unless($researchFile->isPdf, 404);

        return $this->respond($researchFile, true);
    }

    private function respond(ResearchFile $researchFile, bool $inline): BinaryFileResponse
    {
        abort_unless($researchFile->is_public, 404);
        abort_unless(Storage::disk('local')->exists($researchFile->file_path), 404);

        $path = Storage::disk('local')->path($researchFile->file_path);

        return $inline
            ? response()->file($path, ['Content-Type' => Storage::disk('local')->mimeType($researchFile->file_path) ?: 'application/octet-stream'])
            : response()->download($path, basename($researchFile->file_path));
    }
}
