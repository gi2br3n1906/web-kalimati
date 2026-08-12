<?php

declare(strict_types=1);

namespace App\Filament\Resources\FarmGridResource\Pages;

use App\Filament\Resources\FarmGridResource;
use Filament\Resources\Pages\ListRecords;

class ListFarmGrids extends ListRecords
{
    protected static string $resource = FarmGridResource::class;
}
