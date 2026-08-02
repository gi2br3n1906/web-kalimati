<?php

declare(strict_types=1);

use App\Enums\RoleType;
use App\Models\UmkmBusiness;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;

function umkmResourceUser(RoleType $role): User
{
    $user = User::factory()->withRole($role)->create();
    $user->assignRole(Role::findByName($role->value, 'web'));

    return $user;
}

it('allows the umkm owner role to access its business and ledger resources', function (): void {
    $this->seed(DatabaseSeeder::class);
    $user = umkmResourceUser(RoleType::UMKM);
    $ownedBusiness = UmkmBusiness::factory()->for($user, 'owner')->create();
    $otherBusiness = UmkmBusiness::factory()->create();

    $this->actingAs($user)
        ->get('/admin/umkm-businesses')
        ->assertOk()
        ->assertSee($ownedBusiness->business_name)
        ->assertDontSee($otherBusiness->business_name);

    $this->get('/admin/umkm-businesses/create')->assertOk();
    $this->get('/admin/umkm-ledgers')->assertOk();
});

it('denies kelompok tani access to umkm ledger resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(umkmResourceUser(RoleType::KELOMPOK_TANI))
        ->get('/admin/umkm-businesses')
        ->assertForbidden();

    $this->get('/admin/umkm-ledgers')->assertForbidden();
});
