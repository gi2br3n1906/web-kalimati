<?php

declare(strict_types=1);

namespace App\Filament\Resources\GisPointOfInterestResource\Pages;

use App\Filament\Resources\GisPointOfInterestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGisPointsOfInterest extends ListRecords
{
    protected static string $resource = GisPointOfInterestResource::class;

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
