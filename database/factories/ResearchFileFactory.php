<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ResearchCategory;
use App\Models\ResearchFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ResearchFile> */
class ResearchFileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uploader_id' => User::factory(), 'title' => fake()->sentence(5), 'kkn_cohort' => 'Tim II 2026',
            'category' => fake()->randomElement(ResearchCategory::cases()), 'author_names' => fake()->name().', '.fake()->name(),
            'file_path' => 'research-files/'.fake()->uuid().'.pdf', 'file_size_kb' => fake()->numberBetween(100, 20_480),
            'abstract' => fake()->paragraph(), 'is_public' => true,
        ];
    }
}
