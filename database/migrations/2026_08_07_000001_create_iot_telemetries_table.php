<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iot_telemetries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('iot_device_id')->constrained()->cascadeOnDelete();
            $table->float('temp_air');
            $table->float('hum_air');
            $table->float('temp_soil');
            $table->float('hum_soil_percent');
            $table->integer('raw_soil');
            $table->float('lux_light');
            $table->timestamps();

            $table->index(['iot_device_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_telemetries');
    }
};
