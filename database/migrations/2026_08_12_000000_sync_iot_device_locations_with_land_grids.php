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
        Schema::table('iot_devices', function (Blueprint $table): void {
            $table->foreignId('land_grid_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        Schema::table('iot_telemetries', function (Blueprint $table): void {
            $table->decimal('latitude', 10, 8)->nullable()->after('iot_device_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->index(['latitude', 'longitude']);
        });

        $this->associateExistingDevices();
    }

    public function down(): void
    {
        Schema::table('iot_telemetries', function (Blueprint $table): void {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('iot_devices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('land_grid_id');
        });
    }

    private function associateExistingDevices(): void
    {
        $grids = DB::table('land_grids')
            ->where('status', 'active')
            ->get(['id', 'latitude', 'longitude']);

        if ($grids->isEmpty()) {
            return;
        }

        DB::table('iot_devices')
            ->where('is_active', true)
            ->orderBy('id')
            ->each(function (object $device) use ($grids): void {
                $nearestGrid = $grids->sortBy(function (object $grid) use ($device): float {
                    $latitudeDelta = (float) $grid->latitude - (float) $device->latitude;
                    $longitudeDelta = (float) $grid->longitude - (float) $device->longitude;

                    return ($latitudeDelta ** 2) + ($longitudeDelta ** 2);
                })->first();

                DB::table('iot_devices')
                    ->where('id', $device->id)
                    ->update(['land_grid_id' => $nearestGrid->id]);
            });
    }
};
