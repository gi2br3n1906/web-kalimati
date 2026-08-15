<?php

declare(strict_types=1);

use App\Enums\RoleType;
use App\Models\AiRecommendation;
use App\Models\IotDevice;
use App\Models\IotTelemetry;
use App\Models\LandGrid;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;

function agricultureUser(RoleType $role): User
{
    $user = User::factory()->withRole($role)->create();
    $user->assignRole(Role::findByName($role->value, 'web'));

    return $user;
}

it('allows kelompok tani to access smart agriculture filament resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(agricultureUser(RoleType::KELOMPOK_TANI))
        ->get('/admin/farm-grids')
        ->assertOk();

    $this->get('/admin/land-grids/create')->assertOk();
    $grid = LandGrid::factory()->create(['grid_code' => 'KAL-DAMPIT-REAL']);
    $device = IotDevice::factory()->create([
        'land_grid_id' => $grid->id,
        'name' => 'ESP32 Sawah Dampit',
        'device_code' => 'IOT-REAL-001',
        'crop_type' => 'Jagung',
        'latitude' => -7.21450000,
        'longitude' => 110.72340000,
        'last_active_at' => now(),
    ]);
    $telemetry = IotTelemetry::factory()->for($device, 'device')->create([
        'temp_air' => 31.2,
        'hum_soil_percent' => 48.6,
        'lux_light' => 14500,
    ]);
    AiRecommendation::factory()->for($device, 'device')->for($telemetry, 'telemetry')->create([
        'action_title' => 'Kelembapan tanah perlu perhatian',
        'recommendation_text' => 'Lakukan penyiraman bertahap.',
    ]);

    $this->get('/admin/iot-location-points')
        ->assertOk()
        ->assertSee('ESP32 Sawah Dampit')
        ->assertSee('IOT-REAL-001');
    $this->get('/admin/sensor-logs')
        ->assertOk()
        ->assertSee('ESP32 Sawah Dampit')
        ->assertSee('31.2');
    $this->get('/admin/land-recommendations')
        ->assertOk()
        ->assertSee('Kelembapan tanah perlu perhatian')
        ->assertSee('Lakukan penyiraman bertahap.');
    $this->get('/admin/farm-grids')
        ->assertOk()
        ->assertSee('KAL-DAMPIT-REAL')
        ->assertSee('ESP32 Sawah Dampit')
        ->assertSee('Jagung');
    $this->get('/admin/iot-devices')->assertOk();
    $this->get('/admin/iot-devices/create')->assertOk();
    $this->get("/admin/iot-devices/{$device->getKey()}/edit")
        ->assertOk()
        ->assertSee('Histori Telemetri')
        ->assertSee('Riwayat Rekomendasi AI');
});

it('allows super admin to access and see the iot device resource', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(agricultureUser(RoleType::SUPER_ADMIN))
        ->get('/admin/iot-devices')
        ->assertOk()
        ->assertSee('Perangkat IoT');

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Perangkat IoT');
});

it('denies warga access to smart agriculture filament resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(agricultureUser(RoleType::WARGA))
        ->get('/admin/farm-grids')
        ->assertForbidden();

    $this->get('/admin/iot-location-points')->assertForbidden();
    $this->get('/admin/sensor-logs')->assertForbidden();
    $this->get('/admin/land-recommendations')->assertForbidden();
    $this->get('/admin/iot-devices')->assertForbidden();
});
