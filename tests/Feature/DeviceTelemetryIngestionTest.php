<?php

declare(strict_types=1);

use App\Enums\CommodityType;
use App\Enums\LandGridStatus;
use App\Models\IotDevice;
use App\Models\LandGrid;
use Illuminate\Support\Facades\Http;

function deviceTelemetryPayload(): array
{
    return [
        'latitude' => -7.21450000,
        'longitude' => 110.72340000,
        'temp_air' => 30.2,
        'hum_air' => 71.4,
        'temp_soil' => 27.8,
        'hum_soil_percent' => 54.6,
        'raw_soil' => 1850,
        'lux_light' => 12750.5,
    ];
}

it('stores telemetry updates the device and creates ai reasoning synchronously', function (): void {
    config()->set('services.llm.provider', 'gemini');
    config()->set('services.gemini.api_key', 'test-key');
    config()->set('services.gemini.model', 'gemini-2.5-flash');
    config()->set('services.gemini.models_url', 'https://generativelanguage.googleapis.com/v1beta/models');
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [[
                'content' => ['parts' => [['text' => json_encode([
                    'condition_status' => 'optimal',
                    'headline' => 'Kondisi lahan stabil',
                    'action_recommendation' => 'Pertahankan pemantauan dan penyiraman saat ini.',
                ], JSON_THROW_ON_ERROR)]]],
            ]],
        ]),
    ]);
    $nearestGrid = LandGrid::factory()->create([
        'grid_code' => 'KAL-DAMPIT-A01',
        'commodity_type' => CommodityType::JAGUNG,
        'status' => LandGridStatus::ACTIVE,
        'latitude' => -7.21440000,
        'longitude' => 110.72330000,
    ]);
    LandGrid::factory()->create([
        'latitude' => -7.30000000,
        'longitude' => 110.80000000,
    ]);
    $device = IotDevice::factory()->create([
        'api_token' => 'valid-device-token',
        'latitude' => -7.30000000,
        'longitude' => 110.80000000,
    ]);

    $this->withHeader('X-Device-Token', 'valid-device-token')
        ->postJson('/api/v1/telemetry', deviceTelemetryPayload())
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.device_code', $device->device_code)
        ->assertJsonPath('data.recommendation_id', 1);

    $this->assertDatabaseHas('iot_telemetries', [
        'iot_device_id' => $device->id,
        'latitude' => -7.2145,
        'longitude' => 110.7234,
        'raw_soil' => 1850,
    ]);
    $this->assertDatabaseHas('ai_recommendations', [
        'iot_device_id' => $device->id,
        'condition_status' => 'optimal',
        'action_title' => 'Kondisi lahan stabil',
    ]);
    $this->assertDatabaseMissing('iot_devices', ['api_token' => 'valid-device-token']);
    $device->refresh();
    expect($device->latitude)->toBe(-7.2145)
        ->and($device->longitude)->toBe(110.7234)
        ->and($device->last_active_at)->not->toBeNull()
        ->and($device->land_grid_id)->toBe($nearestGrid->id);
    Http::assertSentCount(1);
});

it('rejects device telemetry with an invalid or inactive token', function (): void {
    IotDevice::factory()->create(['api_token' => 'inactive-token', 'is_active' => false]);

    $this->withHeader('X-Device-Token', 'invalid-token')
        ->postJson('/api/v1/telemetry', deviceTelemetryPayload())
        ->assertUnauthorized();

    $this->withHeader('X-Device-Token', 'inactive-token')
        ->postJson('/api/v1/telemetry', deviceTelemetryPayload())
        ->assertUnauthorized();

    $this->assertDatabaseCount('iot_telemetries', 0);
});

it('validates device telemetry metric ranges', function (): void {
    $device = IotDevice::factory()->create(['api_token' => 'valid-device-token']);

    $this->withHeader('X-Device-Token', 'valid-device-token')
        ->postJson('/api/v1/telemetry', [...deviceTelemetryPayload(), 'hum_air' => 101])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('hum_air');
});

it('creates a caution fallback synchronously when the ai provider fails', function (): void {
    config()->set('services.llm.provider', 'gemini');
    config()->set('services.gemini.api_key', 'test-key');
    config()->set('services.gemini.model', 'gemini-2.5-flash');
    config()->set('services.gemini.models_url', 'https://generativelanguage.googleapis.com/v1beta/models');
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([], 503),
    ]);
    $device = IotDevice::factory()->create(['api_token' => 'valid-device-token']);

    $this->withHeader('X-Device-Token', 'valid-device-token')
        ->postJson('/api/v1/telemetry', deviceTelemetryPayload())
        ->assertOk()
        ->assertJsonPath('data.recommendation_id', 1);

    $this->assertDatabaseHas('ai_recommendations', [
        'iot_device_id' => $device->id,
        'condition_status' => 'caution',
        'action_title' => 'Periksa kondisi lahan secara manual',
    ]);
});
