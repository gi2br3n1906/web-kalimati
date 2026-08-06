<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotDevice;
use App\Models\IotTelemetry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AiRecommendation> */
class AiRecommendationFactory extends Factory
{
    protected $model = AiRecommendation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $device = IotDevice::factory();

        return [
            'iot_device_id' => $device,
            'iot_telemetry_id' => IotTelemetry::factory()->for($device, 'device'),
            'condition_status' => AiConditionStatus::OPTIMAL,
            'action_title' => 'Pertahankan kondisi lahan',
            'recommendation_text' => 'Kondisi sensor berada pada rentang optimal. Lanjutkan pemantauan berkala.',
        ];
    }
}
