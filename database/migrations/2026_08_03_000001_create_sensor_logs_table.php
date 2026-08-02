<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('land_grid_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 100);
            $table->decimal('ph_level', 4, 2);
            $table->decimal('moisture_percentage', 5, 2);
            $table->decimal('temperature_celsius', 4, 2);
            $table->json('raw_payload');
            $table->timestamp('recorded_at')->index();
            $table->timestamps();

            $table->index(['land_grid_id', 'recorded_at']);
            $table->index(['device_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};
