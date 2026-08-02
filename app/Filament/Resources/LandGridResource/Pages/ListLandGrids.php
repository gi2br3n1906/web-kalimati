<?php

declare(strict_types=1);

namespace App\Filament\Resources\LandGridResource\Pages;

use App\Filament\Resources\LandGridResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandGrids extends ListRecords
{
    protected static string $resource = LandGridResource::class;

    /** @return array<Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
