<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CommodityType;
use App\Enums\LandGridStatus;
use App\Models\LandGrid;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandGrid>
 */
class LandGridFactory extends Factory
{
    public function definition(): array
    {
        return [
            'grid_code' => sprintf('KAL-%s-%s', fake()->unique()->bothify('???'), fake()->numerify('A##')),
            'dusun_name' => fake()->randomElement(['Dampit', 'Brojo', 'Kedungrandu']),
            'commodity_type' => fake()->randomElement(CommodityType::cases()),
            'latitude' => fake()->randomFloat(8, -7.23000000, -7.20000000),
            'longitude' => fake()->randomFloat(8, 110.80000000, 110.84000000),
            'geojson_polygon' => null,
            'owner_name' => fake()->name(),
            'status' => LandGridStatus::ACTIVE,
        ];
    }
}
