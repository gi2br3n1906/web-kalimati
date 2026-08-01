<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleType::cases() as $roleType) {
            Role::findOrCreate($roleType->value, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
