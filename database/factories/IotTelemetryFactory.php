<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\IotDevice;
use App\Models\IotTelemetry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<IotTelemetry> */
class IotTelemetryFactory extends Factory
{
    protected $model = IotTelemetry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'iot_device_id' => IotDevice::factory(),
            'latitude' => -7.21450000,
            'longitude' => 110.72340000,
            'temp_air' => 29.4,
            'hum_air' => 72.5,
            'temp_soil' => 27.2,
            'hum_soil_percent' => 56.8,
            'raw_soil' => 1840,
            'lux_light' => 12500.0,
        ];
    }
}
