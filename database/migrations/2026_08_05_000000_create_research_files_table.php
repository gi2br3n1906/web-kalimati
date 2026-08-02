<?php

declare(strict_types=1);

use App\Enums\ResearchCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('uploader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('kkn_cohort', 50);
            $table->enum('category', array_column(ResearchCategory::cases(), 'value'));
            $table->text('author_names');
            $table->string('file_path');
            $table->unsignedInteger('file_size_kb');
            $table->text('abstract');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['category', 'kkn_cohort']);
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_files');
    }
};
