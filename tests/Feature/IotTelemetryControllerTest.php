<?php

declare(strict_types=1);

use App\Models\LandGrid;
use App\Models\SensorLog;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    config()->set('services.iot.webhook_secret', 'phase-three-test-token');
});

it('stores a valid telemetry payload from an authenticated iot device', function (): void {
    $grid = LandGrid::factory()->create(['grid_code' => 'KAL-DAMPIT-A12']);
    $recordedAt = Carbon::parse('2026-08-01T10:30:00Z');

    $this->withHeader('X-IoT-Device-Token', 'phase-three-test-token')
        ->postJson('/api/v1/iot/telemetry', [
            'device_id' => 'ESP32-SOIL-KAL-001',
            'grid_code' => $grid->grid_code,
            'ph_level' => 5.85,
            'moisture_percentage' => 42.50,
            'temperature_celsius' => 28.40,
            'recorded_at' => $recordedAt->toISOString(),
        ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Telemetry log successfully stored.')
        ->assertJsonPath('data.land_grid_id', $grid->id);

    $this->assertDatabaseHas('sensor_logs', [
        'land_grid_id' => $grid->id,
        'device_id' => 'ESP32-SOIL-KAL-001',
        'ph_level' => '5.85',
        'moisture_percentage' => '42.50',
        'temperature_celsius' => '28.40',
    ]);

    expect(SensorLog::query()->sole()->raw_payload)->toMatchArray([
        'grid_code' => $grid->grid_code,
        'device_id' => 'ESP32-SOIL-KAL-001',
    ]);
});

it('rejects iot telemetry with an invalid device token', function (): void {
    $grid = LandGrid::factory()->create();

    $this->withHeader('X-IoT-Device-Token', 'invalid-token')
        ->postJson('/api/v1/iot/telemetry', [
            'device_id' => 'ESP32-SOIL-KAL-001',
            'grid_code' => $grid->grid_code,
            'ph_level' => 5.85,
            'moisture_percentage' => 42.50,
            'temperature_celsius' => 28.40,
            'recorded_at' => now()->toISOString(),
        ])
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Unauthorized.');
});

it('rejects out of bounds iot telemetry metrics', function (): void {
    $grid = LandGrid::factory()->create();

    $this->withHeader('X-IoT-Device-Token', 'phase-three-test-token')
        ->postJson('/api/v1/iot/telemetry', [
            'device_id' => 'ESP32-SOIL-KAL-001',
            'grid_code' => $grid->grid_code,
            'ph_level' => 14.01,
            'moisture_percentage' => 100.01,
            'temperature_celsius' => 28.40,
            'recorded_at' => now()->toISOString(),
        ])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation failed.')
        ->assertJsonValidationErrors(['ph_level', 'moisture_percentage']);
});
