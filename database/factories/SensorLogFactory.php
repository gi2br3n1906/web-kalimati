<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LandGrid;
use App\Models\SensorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SensorLog>
 */
class SensorLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'land_grid_id' => LandGrid::factory(),
            'device_id' => fake()->bothify('ESP32-SOIL-KAL-###'),
            'ph_level' => fake()->randomFloat(2, 4.50, 7.50),
            'moisture_percentage' => fake()->randomFloat(2, 30.00, 80.00),
            'temperature_celsius' => fake()->randomFloat(2, 20.00, 35.00),
            'raw_payload' => ['source' => 'factory'],
            'recorded_at' => fake()->dateTimeBetween('-7 days'),
        ];
    }
}
