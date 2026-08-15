<?php

declare(strict_types=1);

use App\Enums\PoiCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('gis_points_of_interest')
            ->where('category', 'fasilitas_umum')
            ->update([
                'category' => PoiCategory::FASILITAS_UMUM_PEMERINTAHAN->value,
                'icon_marker' => PoiCategory::FASILITAS_UMUM_PEMERINTAHAN->defaultMarker(),
            ]);

        DB::table('gis_points_of_interest')
            ->where('category', 'posyandu')
            ->update([
                'category' => PoiCategory::PENDIDIKAN_KESEHATAN->value,
                'icon_marker' => PoiCategory::PENDIDIKAN_KESEHATAN->defaultMarker(),
            ]);
    }

    public function down(): void
    {
        // Consolidated categories cannot be losslessly split into their legacy values.
    }
};
