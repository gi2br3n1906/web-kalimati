<?php

declare(strict_types=1);

namespace App\Filament\Resources\ResearchFileResource\Pages;

use App\Filament\Resources\ResearchFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResearchFiles extends ListRecords
{
    protected static string $resource = ResearchFileResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
