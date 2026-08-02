<?php

declare(strict_types=1);

use App\Enums\RoleType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function phaseTwoUser(RoleType $role): User
{
    $user = User::factory()->withRole($role)->create();
    $user->assignRole(Role::findByName($role->value, 'web'));

    return $user;
}

/**
 * @return Collection<int, string>
 */
function phaseTwoPermissions(): Collection
{
    return Permission::query()
        ->where(static function (Builder $query): void {
            $query
                ->where('name', 'like', '%_news::article')
                ->orWhere('name', 'like', '%_gis::point::of::interest');
        })
        ->pluck('name');
}

it('allows super admin to access phase two filament resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(phaseTwoUser(RoleType::SUPER_ADMIN))
        ->get('/admin/news-articles')
        ->assertOk();

    $this->get('/admin/news-articles/create')
        ->assertOk();

    $this->get('/admin/gis-point-of-interests')
        ->assertOk();

    $this->get('/admin/gis-point-of-interests/create')
        ->assertOk();
});

it('allows admin desa to access phase two filament resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $adminDesa = phaseTwoUser(RoleType::ADMIN_DESA);

    expect($adminDesa->hasAllPermissions(phaseTwoPermissions()))->toBeTrue();

    $this->actingAs($adminDesa)
        ->get('/admin/news-articles')
        ->assertOk();

    $this->get('/admin/news-articles/create')
        ->assertOk();

    $this->get('/admin/gis-point-of-interests')
        ->assertOk();

    $this->get('/admin/gis-point-of-interests/create')
        ->assertOk();
});

it('denies kelompok tani access to phase two filament resources', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(phaseTwoUser(RoleType::KELOMPOK_TANI))
        ->get('/admin/news-articles')
        ->assertForbidden();

    $this->get('/admin/gis-point-of-interests')
        ->assertForbidden();
});
