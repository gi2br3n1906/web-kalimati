<?php

declare(strict_types=1);

use App\Enums\NewsCategory;
use App\Enums\RoleType;
use App\Filament\Resources\NewsArticleResource\Pages\CreateNewsArticle;
use App\Filament\Resources\NewsArticleResource\Pages\EditNewsArticle;
use App\Models\NewsArticle;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Livewire;
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

it('renders the news article editor with publication controls and local draft protection', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->actingAs(phaseTwoUser(RoleType::SUPER_ADMIN));

    $this->get('/admin/news-articles/create')
        ->assertOk()
        ->assertSee('Artikel Berita')
        ->assertSee('Publikasi')
        ->assertSee('Karang Taruna')
        ->assertSee('Pemerintah Desa')
        ->assertSee('min-height: 450px;', false)
        ->assertSee('filament.news-articles.create.content-draft', false)
        ->assertSee('setInterval', false)
        ->assertSee('5000', false);

    Livewire::test(CreateNewsArticle::class)
        ->fillForm([
            'title' => 'Pelatihan Digital Karang Taruna',
            'category' => NewsCategory::KARANG_TARUNA->value,
            'content' => '<p>Materi pelatihan digital.</p>',
        ])
        ->assertFormSet([
            'slug' => 'pelatihan-digital-karang-taruna',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $article = NewsArticle::query()
        ->where('slug', 'pelatihan-digital-karang-taruna')
        ->firstOrFail();

    expect($article->category)->toBe(NewsCategory::KARANG_TARUNA);

    Livewire::test(EditNewsArticle::class, ['record' => $article->getRouteKey()])
        ->assertFormSet([
            'title' => 'Pelatihan Digital Karang Taruna',
            'category' => NewsCategory::KARANG_TARUNA->value,
            'content' => '<p>Materi pelatihan digital.</p>',
        ]);
});
