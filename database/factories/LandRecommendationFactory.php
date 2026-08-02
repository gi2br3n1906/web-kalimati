<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LandGrid;
use App\Models\LandRecommendation;
use App\Models\SensorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandRecommendation>
 */
class LandRecommendationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'land_grid_id' => LandGrid::factory(),
            'sensor_log_id' => null,
            'ai_model_used' => 'RAG-v1',
            'soil_condition_summary' => fake()->sentence(12),
            'fertilizer_dosage' => fake()->sentence(10),
            'lime_treatment' => fake()->sentence(10),
            'action_plan' => fake()->paragraph(),
            'is_applied' => false,
        ];
    }

    public function forSensorLog(SensorLog $sensorLog): static
    {
        return $this->state(fn (array $attributes): array => [
            'land_grid_id' => $sensorLog->land_grid_id,
            'sensor_log_id' => $sensorLog->getKey(),
        ]);
    }
}
