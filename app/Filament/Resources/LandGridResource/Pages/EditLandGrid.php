<?php

declare(strict_types=1);

namespace App\Filament\Resources\LandGridResource\Pages;

use App\Filament\Resources\LandGridResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandGrid extends EditRecord
{
    protected static string $resource = LandGridResource::class;

    /** @return array<Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
