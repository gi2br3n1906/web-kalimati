<?php

declare(strict_types=1);

namespace App\Filament\Resources\GisPointOfInterestResource\Pages;

use App\Filament\Resources\GisPointOfInterestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGisPointOfInterest extends CreateRecord
{
    protected static string $resource = GisPointOfInterestResource::class;
}
