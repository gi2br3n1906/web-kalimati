<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iot_devices', function (Blueprint $table): void {
            $table->id();
            $table->string('device_code', 100)->unique();
            $table->string('name');
            $table->string('api_token', 512)->unique();
            $table->char('api_token_hash', 64)->unique();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('coverage_radius_meters')->default(100);
            $table->string('crop_type', 100)->default('Jagung');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable()->index();
            $table->timestamps();

            $table->index(['is_active', 'latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_devices');
    }
};
