<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE gis_points_of_interest '
                .'DROP CONSTRAINT IF EXISTS gis_points_of_interest_category_check',
            );
        }

        Schema::table('gis_points_of_interest', function (Blueprint $table): void {
            $table->string('category', 50)->change();
        });
    }

    public function down(): void
    {
        // The application enum remains the source of truth. Reverting to a database
        // ENUM could fail when rows use categories added after initial deployment.
    }
};
