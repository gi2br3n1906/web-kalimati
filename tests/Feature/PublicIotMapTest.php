<?php

declare(strict_types=1);

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotDevice;
use App\Models\IotTelemetry;

it('exposes every coordinated telemetry history as an independent GIS measurement point', function (): void {
    config()->set('app.timezone', 'Asia/Jakarta');

    $device = IotDevice::factory()->create([
        'name' => 'Sensor Jagung Dampit',
        'device_code' => 'IOT-DAMPIT-01',
    ]);
    $olderTelemetry = IotTelemetry::factory()->for($device, 'device')->create([
        'latitude' => -7.21450000,
        'longitude' => 110.72340000,
        'created_at' => '2026-08-15 06:00:00',
    ]);
    $newerTelemetry = IotTelemetry::factory()->for($device, 'device')->create([
        'latitude' => -7.21550000,
        'longitude' => 110.72440000,
        'temp_air' => 31.4,
        'created_at' => '2026-08-15 14:30:00',
    ]);
    AiRecommendation::factory()->for($device, 'device')->for($newerTelemetry, 'telemetry')->create([
        'condition_status' => AiConditionStatus::CRITICAL,
        'action_title' => 'Segera lakukan penyiraman',
        'recommendation_text' => 'Kelembapan tanah terlalu rendah.',
    ]);
    IotTelemetry::factory()->for($device, 'device')->create([
        'latitude' => null,
        'longitude' => null,
    ]);

    $this->getJson('/api/v1/gis/telemetries')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newerTelemetry->getKey())
        ->assertJsonPath('data.0.latitude', -7.2155)
        ->assertJsonPath('data.0.longitude', 110.7244)
        ->assertJsonPath('data.0.measured_at', '15 Agu 2026, 14:30 WIB')
        ->assertJsonPath('data.0.sensor_data.temp_air', 31.4)
        ->assertJsonPath('data.0.condition_status', 'warning')
        ->assertJsonPath('data.0.recommendation.action_title', 'Segera lakukan penyiraman')
        ->assertJsonPath('data.0.recommendation.recommendation_text', 'Kelembapan tanah terlalu rendah.')
        ->assertJsonPath('data.0.device.name', 'Sensor Jagung Dampit')
        ->assertJsonPath('data.1.id', $olderTelemetry->getKey())
        ->assertJsonMissingPath('data.0.device.api_token');
});

it('renders iot map configuration on GIS and agriculture pages', function (): void {
    $this->get('/peta')
        ->assertOk()
        ->assertSee('telemetries')
        ->assertSee('data-gis-map', false);

    $this->get('/pertanian')
        ->assertOk()
        ->assertSee('telemetries')
        ->assertSee('data-iot-map', false);
});
