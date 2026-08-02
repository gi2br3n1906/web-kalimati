<?php

declare(strict_types=1);

namespace App\Filament\Resources\LandGridResource\Pages;

use App\Filament\Resources\LandGridResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLandGrid extends CreateRecord
{
    protected static string $resource = LandGridResource::class;
}
