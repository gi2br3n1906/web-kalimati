<?php

declare(strict_types=1);

namespace App\Filament\Resources\LandRecommendationResource\Pages;

use App\Filament\Resources\LandRecommendationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandRecommendation extends EditRecord
{
    protected static string $resource = LandRecommendationResource::class;

    /** @return array<Actions\Action> */
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
