<?php

declare(strict_types=1);

use App\Enums\RoleType;
use App\Models\User;
use App\Support\ResearchFileUploadRules;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

function researchResourceUser(RoleType $role): User
{
    $user = User::factory()->withRole($role)->create();
    $user->assignRole(Role::findByName($role->value, 'web'));

    return $user;
}

it('accepts only approved research document MIME types and files up to 20 megabytes', function (): void {
    $validPdf = UploadedFile::fake()->create('monografi.pdf', 20, 'application/pdf');
    $invalidExecutable = UploadedFile::fake()->create('payload.exe', 20, 'application/x-msdownload');
    $oversizedPdf = UploadedFile::fake()->create('oversized.pdf', ResearchFileUploadRules::MAX_SIZE_KB + 1, 'application/pdf');

    expect(Validator::make(['file' => $validPdf], ['file' => ResearchFileUploadRules::rules()])->passes())->toBeTrue()
        ->and(Validator::make(['file' => $invalidExecutable], ['file' => ResearchFileUploadRules::rules()])->fails())->toBeTrue()
        ->and(Validator::make(['file' => $oversizedPdf], ['file' => ResearchFileUploadRules::rules()])->fails())->toBeTrue();
});

it('allows admin desa and denies umkm from the research backoffice', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->actingAs(researchResourceUser(RoleType::ADMIN_DESA))
        ->get('/admin/research-files')
        ->assertOk();

    $this->actingAs(researchResourceUser(RoleType::UMKM))
        ->get('/admin/research-files')
        ->assertForbidden();
});
