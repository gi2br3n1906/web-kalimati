<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UmkmCategory;
use App\Models\UmkmBusiness;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UmkmBusiness>
 */
class UmkmBusinessFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'business_name' => fake()->company(),
            'category' => fake()->randomElement(UmkmCategory::cases()),
            'description' => fake()->optional()->paragraph(),
            'phone_number' => fake()->numerify('08##########'),
            'logo_path' => null,
            'address' => fake()->address(),
        ];
    }
}
