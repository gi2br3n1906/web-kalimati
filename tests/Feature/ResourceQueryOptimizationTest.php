<?php

declare(strict_types=1);

use App\Filament\Resources\FarmGridResource;
use App\Filament\Resources\LandRecommendationResource;
use App\Filament\Resources\LocationPointResource;
use App\Filament\Resources\SensorLogResource;
use App\Filament\Resources\UmkmBusinessResource;
use App\Filament\Resources\UmkmLedgerResource;

it('eager loads relationships rendered by relation-backed resource tables', function (): void {
    expect(SensorLogResource::getEloquentQuery()->getEagerLoads())->toHaveKey('device')
        ->and(LandRecommendationResource::getEloquentQuery()->getEagerLoads())->toHaveKey('device')
        ->and(LocationPointResource::getEloquentQuery()->getEagerLoads())->toHaveKey('landGrid')
        ->and(FarmGridResource::getEloquentQuery()->getEagerLoads())->toHaveKey('iotDevices')
        ->and(UmkmBusinessResource::getEloquentQuery()->getEagerLoads())->toHaveKey('owner')
        ->and(UmkmLedgerResource::getEloquentQuery()->getEagerLoads())->toHaveKey('business');
});
