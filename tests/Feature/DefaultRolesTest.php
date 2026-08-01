<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleType;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DefaultRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_the_phase_one_rbac_state_idempotently(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $expectedRoles = array_column(RoleType::cases(), 'value');
        $actualRoles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        sort($expectedRoles);

        $this->assertSame($expectedRoles, $actualRoles);

        $superAdmin = Role::findByName(RoleType::SUPER_ADMIN->value, 'web');
        $permissions = Permission::query()->orderBy('name')->pluck('name')->all();

        $this->assertCount(6, $permissions);
        $this->assertTrue($superAdmin->hasAllPermissions($permissions));
    }
}
