<?php

declare(strict_types=1);

use App\Jobs\ProcessTelemetryAiReasoning;
use App\Models\IotDevice;
use Illuminate\Support\Facades\Queue;

function deviceTelemetryPayload(): array
{
    return [
        'temp_air' => 30.2,
        'hum_air' => 71.4,
        'temp_soil' => 27.8,
        'hum_soil_percent' => 54.6,
        'raw_soil' => 1850,
        'lux_light' => 12750.5,
    ];
}

it('stores device telemetry and dispatches asynchronous ai reasoning', function (): void {
    Queue::fake();
    $device = IotDevice::factory()->create(['api_token' => 'valid-device-token']);

    $this->withHeader('X-Device-Token', 'valid-device-token')
        ->postJson('/api/v1/telemetry', deviceTelemetryPayload())
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.device_code', $device->device_code);

    $this->assertDatabaseHas('iot_telemetries', [
        'iot_device_id' => $device->id,
        'raw_soil' => 1850,
    ]);
    $this->assertDatabaseMissing('iot_devices', ['api_token' => 'valid-device-token']);
    expect($device->fresh()->last_active_at)->not->toBeNull();
    Queue::assertPushed(ProcessTelemetryAiReasoning::class, 1);
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
