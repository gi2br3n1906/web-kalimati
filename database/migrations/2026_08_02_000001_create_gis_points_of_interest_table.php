<?php

declare(strict_types=1);

use App\Enums\PoiCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gis_points_of_interest', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->enum('category', array_column(PoiCategory::cases(), 'value'));
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('description')->nullable();
            $table->string('icon_marker', 100)->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index(
                ['latitude', 'longitude'],
                'idx_gis_poi_coordinates',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gis_points_of_interest');
    }
};
