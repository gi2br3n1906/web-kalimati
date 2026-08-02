<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NewsCategory;
use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsArticle>
 */
class NewsArticleFactory extends Factory
{
    protected $model = NewsArticle::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'title' => fake()->unique()->sentence(6),
            'slug' => null,
            'category' => fake()->randomElement(NewsCategory::cases()),
            'content' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'thumbnail_path' => null,
            'is_published' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);
    }
}
