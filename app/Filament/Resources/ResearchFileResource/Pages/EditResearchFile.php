<?php

declare(strict_types=1);

namespace App\Filament\Resources\ResearchFileResource\Pages;

use App\Filament\Resources\ResearchFileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditResearchFile extends EditRecord
{
    protected static string $resource = ResearchFileResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['file_path'] ?? null) !== $this->record->file_path) {
            $data['file_size_kb'] = (int) ceil(Storage::disk('local')->size((string) $data['file_path']) / 1024);
        }

return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
