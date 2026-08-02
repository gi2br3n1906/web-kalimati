<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('land_grid_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sensor_log_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ai_model_used', 100);
            $table->text('soil_condition_summary');
            $table->text('fertilizer_dosage');
            $table->text('lime_treatment');
            $table->longText('action_plan');
            $table->boolean('is_applied')->default(false);
            $table->timestamps();

            $table->index(['land_grid_id', 'is_applied']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_recommendations');
    }
};
