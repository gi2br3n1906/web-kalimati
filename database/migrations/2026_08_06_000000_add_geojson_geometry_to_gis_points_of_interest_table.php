<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gis_points_of_interest', function (Blueprint $table): void {
            $table->json('geojson_geometry')->nullable()->after('icon_marker');
        });
    }

    public function down(): void
    {
        Schema::table('gis_points_of_interest', function (Blueprint $table): void {
            $table->dropColumn('geojson_geometry');
        });
    }
};
