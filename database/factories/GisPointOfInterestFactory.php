<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GisPointOfInterest>
 */
class GisPointOfInterestFactory extends Factory
{
    protected $model = GisPointOfInterest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(PoiCategory::cases());

        return [
            'name' => fake()->company(),
            'category' => $category,
            'latitude' => fake()->latitude(-7.23, -7.19),
            'longitude' => fake()->longitude(110.80, 110.85),
            'description' => fake()->optional()->sentence(),
            'icon_marker' => $category->defaultMarker(),
        ];
    }
}
