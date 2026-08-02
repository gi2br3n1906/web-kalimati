<?php

declare(strict_types=1);

namespace App\Filament\Resources\LandRecommendationResource\Pages;

use App\Filament\Resources\LandRecommendationResource;
use Filament\Resources\Pages\ListRecords;

class ListLandRecommendations extends ListRecords
{
    protected static string $resource = LandRecommendationResource::class;
}
