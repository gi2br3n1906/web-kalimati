<?php

declare(strict_types=1);

namespace App\Filament\Resources\ResearchFileResource\Pages;

use App\Filament\Resources\ResearchFileResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateResearchFile extends CreateRecord
{
    protected static string $resource = ResearchFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploader_id'] = auth()->id();
        $data['file_size_kb'] = (int) ceil(Storage::disk('local')->size((string) $data['file_path']) / 1024);

        return $data;
    }
}
