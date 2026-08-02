<?php

declare(strict_types=1);

use App\Enums\NewsCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', array_column(NewsCategory::cases(), 'value'));
            $table->longText('content');
            $table->string('thumbnail_path')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(
                ['is_published', 'slug'],
                'idx_news_published_slug',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
