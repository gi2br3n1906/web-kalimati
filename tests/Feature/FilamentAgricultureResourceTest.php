<?php

declare(strict_types=1);

use App\Enums\RoleType;
use App\Models\IotDevice;
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
        ->get('/admin/land-grids')
        ->assertOk();

    $this->get('/admin/land-grids/create')->assertOk();
    $this->get('/admin/sensor-logs')->assertOk();
    $this->get('/admin/land-recommendations')->assertOk();
    $this->get('/admin/iot-devices')->assertOk();
    $this->get('/admin/iot-devices/create')->assertOk();
    $device = IotDevice::factory()->create();
    $this->get("/admin/iot-devices/{$device->getKey()}/edit")
        ->assertOk()
        ->assertSee('Histori Telemetri')
        ->assertSee('Riwayat Rekomendasi AI');
});

it('denies warga access to smart agriculture filament resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(agricultureUser(RoleType::WARGA))
        ->get('/admin/land-grids')
        ->assertForbidden();

    $this->get('/admin/sensor-logs')->assertForbidden();
    $this->get('/admin/land-recommendations')->assertForbidden();
    $this->get('/admin/iot-devices')->assertForbidden();
});
