<?php

declare(strict_types=1);

use App\Actions\Gis\ImportKmlLocationsAction;
use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;

it('bulk imports KML placemarks using the selected default category', function (): void {
    $filePath = tempnam(sys_get_temp_dir(), 'kalimati-kml-');

    expect($filePath)->not->toBeFalse();
    file_put_contents($filePath, sampleGisKml());

    try {
        $count = app(ImportKmlLocationsAction::class)->execute(
            filePath: $filePath,
            category: PoiCategory::PERTANIAN_IOT,
            sourceName: 'google-earth.kml',
        );
    } finally {
        @unlink($filePath);
    }

    expect($count)->toBe(2)
        ->and(GisPointOfInterest::query()->count())->toBe(2);

    $point = GisPointOfInterest::query()->where('name', 'Balai Desa Kalimati')->firstOrFail();
    $polygon = GisPointOfInterest::query()->where('name', 'Area Pertanian Dampit')->firstOrFail();

    expect($point->category)->toBe(PoiCategory::PERTANIAN_IOT)
        ->and($point->icon_marker)->toBe('agriculture-iot')
        ->and($point->mapGeometry()['type'])->toBe('Point')
        ->and($polygon->mapGeometry()['type'])->toBe('Polygon')
        ->and($polygon->geometryTypeLabel())->toBe('Area / Polygon');
});
