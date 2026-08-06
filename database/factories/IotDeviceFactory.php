<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\IotDevice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<IotDevice> */
class IotDeviceFactory extends Factory
{
    protected $model = IotDevice::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'device_code' => 'IOT-KAL-'.fake()->unique()->numerify('###'),
            'name' => fake()->words(3, true),
            'api_token' => Str::random(64),
            'latitude' => fake()->latitude(-7.35, -7.20),
            'longitude' => fake()->longitude(110.60, 110.80),
            'coverage_radius_meters' => 100,
            'crop_type' => 'Jagung',
            'is_active' => true,
            'last_active_at' => null,
        ];
    }
}
