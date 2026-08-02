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
                ],
            ],
        ]);
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
