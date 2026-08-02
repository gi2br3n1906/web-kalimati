<?php

declare(strict_types=1);

use App\Enums\CommodityType;
use App\Enums\LandGridStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('land_grids', function (Blueprint $table): void {
            $table->id();
            $table->string('grid_code', 50)->unique();
            $table->string('dusun_name', 100);
            $table->enum('commodity_type', array_column(CommodityType::cases(), 'value'));
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->json('geojson_polygon')->nullable();
            $table->string('owner_name')->nullable();
            $table->enum('status', array_column(LandGridStatus::cases(), 'value'))->default(LandGridStatus::ACTIVE->value);
            $table->timestamps();

            $table->index(['dusun_name', 'commodity_type']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_grids');
    }
};
