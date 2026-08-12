<?php

declare(strict_types=1);

namespace App\Filament\Resources\LocationPointResource\Pages;

use App\Filament\Resources\LocationPointResource;
use Filament\Resources\Pages\ListRecords;

class ListLocationPoints extends ListRecords
{
    protected static string $resource = LocationPointResource::class;
}
