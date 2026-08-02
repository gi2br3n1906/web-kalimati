<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class ShieldPermissionSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private const ROLE_PERMISSIONS = [
        'view_role',
        'view_any_role',
        'create_role',
        'update_role',
        'delete_role',
        'delete_any_role',
    ];

    /**
     * @var array<int, string>
     */
    private const PHASE_TWO_RESOURCE_PERMISSIONS = [
        'view_news::article',
        'view_any_news::article',
        'create_news::article',
        'update_news::article',
        'restore_news::article',
        'restore_any_news::article',
        'replicate_news::article',
        'reorder_news::article',
        'delete_news::article',
        'delete_any_news::article',
        'force_delete_news::article',
        'force_delete_any_news::article',
        'view_gis::point::of::interest',
        'view_any_gis::point::of::interest',
        'create_gis::point::of::interest',
        'update_gis::point::of::interest',
        'restore_gis::point::of::interest',
        'restore_any_gis::point::of::interest',
        'replicate_gis::point::of::interest',
        'reorder_gis::point::of::interest',
        'delete_gis::point::of::interest',
        'delete_any_gis::point::of::interest',
        'force_delete_gis::point::of::interest',
        'force_delete_any_gis::point::of::interest',
    ];

    /**
     * @var array<int, string>
     */
    private const PHASE_THREE_RESOURCE_PERMISSIONS = [
        'view_land::grid',
        'view_any_land::grid',
        'create_land::grid',
        'update_land::grid',
        'restore_land::grid',
        'restore_any_land::grid',
        'replicate_land::grid',
        'reorder_land::grid',
        'delete_land::grid',
        'delete_any_land::grid',
        'force_delete_land::grid',
        'force_delete_any_land::grid',
        'view_sensor::log',
        'view_any_sensor::log',
        'create_sensor::log',
        'update_sensor::log',
        'restore_sensor::log',
        'restore_any_sensor::log',
        'replicate_sensor::log',
        'reorder_sensor::log',
        'delete_sensor::log',
        'delete_any_sensor::log',
        'force_delete_sensor::log',
        'force_delete_any_sensor::log',
        'view_land::recommendation',
        'view_any_land::recommendation',
        'create_land::recommendation',
        'update_land::recommendation',
        'restore_land::recommendation',
        'restore_any_land::recommendation',
        'replicate_land::recommendation',
        'reorder_land::recommendation',
        'delete_land::recommendation',
        'delete_any_land::recommendation',
        'force_delete_land::recommendation',
        'force_delete_any_land::recommendation',
    ];

    /**
     * @var array<int, string>
     */
    private const PHASE_FOUR_RESOURCE_PERMISSIONS = [
        'view_umkm::business',
        'view_any_umkm::business',
        'create_umkm::business',
        'update_umkm::business',
        'restore_umkm::business',
        'restore_any_umkm::business',
        'replicate_umkm::business',
        'reorder_umkm::business',
        'delete_umkm::business',
        'delete_any_umkm::business',
        'force_delete_umkm::business',
        'force_delete_any_umkm::business',
        'view_umkm::ledger',
        'view_any_umkm::ledger',
        'create_umkm::ledger',
        'update_umkm::ledger',
        'restore_umkm::ledger',
        'restore_any_umkm::ledger',
        'replicate_umkm::ledger',
        'reorder_umkm::ledger',
        'delete_umkm::ledger',
        'delete_any_umkm::ledger',
        'force_delete_umkm::ledger',
        'force_delete_any_umkm::ledger',
    ];

    /**
     * @var array<int, string>
     */
    private const PHASE_FIVE_RESOURCE_PERMISSIONS = [
        'view_research::file',
        'view_any_research::file',
        'create_research::file',
        'update_research::file',
        'restore_research::file',
        'restore_any_research::file',
        'replicate_research::file',
        'reorder_research::file',
        'delete_research::file',
        'delete_any_research::file',
        'force_delete_research::file',
        'force_delete_any_research::file',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $rolePermissions = collect(self::ROLE_PERMISSIONS)
            ->map(static fn (string $permission): Permission => Permission::findOrCreate($permission, 'web'));

        $resourcePermissions = collect(self::PHASE_TWO_RESOURCE_PERMISSIONS)
            ->map(static fn (string $permission): Permission => Permission::findOrCreate($permission, 'web'));

        $agriculturePermissions = collect(self::PHASE_THREE_RESOURCE_PERMISSIONS)
            ->map(static fn (string $permission): Permission => Permission::findOrCreate($permission, 'web'));

        $umkmPermissions = collect(self::PHASE_FOUR_RESOURCE_PERMISSIONS)
            ->map(static fn (string $permission): Permission => Permission::findOrCreate($permission, 'web'));

        $researchPermissions = collect(self::PHASE_FIVE_RESOURCE_PERMISSIONS)
            ->map(static fn (string $permission): Permission => Permission::findOrCreate($permission, 'web'));

        Role::findOrCreate(RoleType::SUPER_ADMIN->value, 'web')
            ->givePermissionTo($rolePermissions->merge($resourcePermissions)->merge($agriculturePermissions)->merge($umkmPermissions)->merge($researchPermissions));

        Role::findOrCreate(RoleType::ADMIN_DESA->value, 'web')
            ->givePermissionTo($resourcePermissions->merge($researchPermissions));

        Role::findOrCreate(RoleType::KELOMPOK_TANI->value, 'web')
            ->givePermissionTo($agriculturePermissions);

        Role::findOrCreate(RoleType::UMKM->value, 'web')
            ->givePermissionTo($umkmPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
