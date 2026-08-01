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

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(self::ROLE_PERMISSIONS)
            ->map(static fn (string $permission): Permission => Permission::findOrCreate($permission, 'web'));

        Role::findOrCreate(RoleType::SUPER_ADMIN->value, 'web')
            ->givePermissionTo($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
