<?php

declare(strict_types=1);

use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;

it('returns points of interest using the public gis response contract', function (): void {
    $point = GisPointOfInterest::factory()->create([
        'name' => 'Balai Desa Kalimati',
        'category' => PoiCategory::PEMERINTAHAN,
        'latitude' => -7.21450000,
        'longitude' => 110.82340000,
        'description' => 'Pusat pelayanan administrasi Desa Kalimati',
        'icon_marker' => 'building-government',
    ]);

    $this->getJson('/api/v1/gis/points-of-interest')
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'data' => [
                [
                    'id' => $point->id,
                    'name' => 'Balai Desa Kalimati',
                    'category' => 'pemerintahan',
                    'latitude' => -7.2145,
                    'longitude' => 110.8234,
                    'description' => 'Pusat pelayanan administrasi Desa Kalimati',
                    'icon_marker' => 'building-government',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [110.8234, -7.2145],
                    ],
                ],
            ],
        ]);
});

it('returns imported polygon geometry through the public gis endpoint', function (): void {
    GisPointOfInterest::factory()->create([
        'name' => 'Area Pertanian Dampit',
        'category' => PoiCategory::PERTANIAN_IOT,
        'latitude' => -7.212,
        'longitude' => 110.822,
        'geojson_geometry' => [
            'type' => 'Polygon',
            'coordinates' => [[
                [110.8200, -7.2100],
                [110.8240, -7.2100],
                [110.8240, -7.2140],
                [110.8200, -7.2140],
                [110.8200, -7.2100],
            ]],
        ],
    ]);

    $this->getJson('/api/v1/gis/points-of-interest?category=pertanian_iot')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Area Pertanian Dampit')
        ->assertJsonPath('data.0.geometry.type', 'Polygon')
        ->assertJsonCount(5, 'data.0.geometry.coordinates.0');
});

it('filters points of interest by category', function (): void {
    GisPointOfInterest::factory()->create([
        'name' => 'Balai Desa Kalimati',
        'category' => PoiCategory::PEMERINTAHAN,
    ]);
    GisPointOfInterest::factory()->create([
        'name' => 'SDN 02 Kalimati',
        'category' => PoiCategory::PENDIDIKAN,
    ]);

    $this->getJson('/api/v1/gis/points-of-interest?category=pendidikan')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'SDN 02 Kalimati')
        ->assertJsonPath('data.0.category', 'pendidikan');
});

it('rejects invalid point of interest categories', function (): void {
    $this->getJson('/api/v1/gis/points-of-interest?category=invalid')
        ->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 'VALIDATION_ERROR')
        ->assertJsonValidationErrors('category');
});
