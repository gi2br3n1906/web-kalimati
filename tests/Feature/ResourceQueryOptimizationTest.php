<?php

declare(strict_types=1);

use App\Filament\Resources\LandRecommendationResource;
use App\Filament\Resources\SensorLogResource;
use App\Filament\Resources\UmkmBusinessResource;
use App\Filament\Resources\UmkmLedgerResource;

it('eager loads relationships rendered by relation-backed resource tables', function (): void {
    expect(SensorLogResource::getEloquentQuery()->getEagerLoads())->toHaveKey('landGrid')
        ->and(LandRecommendationResource::getEloquentQuery()->getEagerLoads())->toHaveKey('landGrid')
        ->and(UmkmBusinessResource::getEloquentQuery()->getEagerLoads())->toHaveKey('owner')
        ->and(UmkmLedgerResource::getEloquentQuery()->getEagerLoads())->toHaveKey('business');
});
