<?php

declare(strict_types=1);

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotDevice;
use App\Models\IotTelemetry;

it('exposes active iot devices with telemetry radius and ai recommendation for GIS', function (): void {
    $device = IotDevice::factory()->create([
        'name' => 'Sensor Jagung Dampit',
        'coverage_radius_meters' => 175,
    ]);
    $telemetry = IotTelemetry::factory()->for($device, 'device')->create();
    AiRecommendation::factory()->for($device, 'device')->for($telemetry, 'telemetry')->create([
        'condition_status' => AiConditionStatus::CRITICAL,
    ]);
    IotDevice::factory()->create(['is_active' => false]);

    $this->getJson('/api/v1/gis/iot-devices')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Sensor Jagung Dampit')
        ->assertJsonPath('data.0.coverage_radius_meters', 175)
        ->assertJsonPath('data.0.recommendation.condition_status', 'critical')
        ->assertJsonMissingPath('data.0.api_token');
});

it('renders iot map configuration on GIS and agriculture pages', function (): void {
    $this->get('/peta')
        ->assertOk()
        ->assertSee('iot-devices')
        ->assertSee('data-gis-map', false);

    $this->get('/pertanian')
        ->assertOk()
        ->assertSee('iot-devices')
        ->assertSee('data-iot-map', false);
});
