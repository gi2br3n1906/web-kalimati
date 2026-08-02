<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\GisDataController;
use App\Http\Controllers\Api\V1\IotTelemetryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/gis')
    ->name('v1.gis.')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('/points-of-interest', [GisDataController::class, 'pointsOfInterest'])
            ->name('points-of-interest');
    });

Route::prefix('v1/iot')
    ->name('v1.iot.')
    ->middleware('throttle:120,1')
    ->group(function (): void {
        Route::post('/telemetry', [IotTelemetryController::class, 'store'])
            ->name('telemetry.store');
    });
