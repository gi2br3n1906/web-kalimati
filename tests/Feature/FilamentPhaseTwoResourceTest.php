<?php

declare(strict_types=1);

use App\Enums\NewsCategory;
use App\Enums\RoleType;
use App\Filament\Resources\NewsArticleResource\Pages\CreateNewsArticle;
use App\Filament\Resources\NewsArticleResource\Pages\EditNewsArticle;
use App\Models\NewsArticle;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Support\Enums\MaxWidth;
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
        ->assertOk()
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN');

    $this->get('/admin/news-articles/create')
        ->assertOk();

    $this->get('/admin/gis-point-of-interests')
        ->assertOk()
        ->assertSee('Import KML Google Earth');

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

it('hides GIS write actions without the create point permission', function (): void {
    $this->seed(DatabaseSeeder::class);

    $adminRole = Role::findByName(RoleType::ADMIN_DESA->value, 'web');
    $adminRole->revokePermissionTo('create_gis::point::of::interest');

    $this->actingAs(phaseTwoUser(RoleType::ADMIN_DESA))
        ->get('/admin/gis-point-of-interests')
        ->assertOk()
        ->assertDontSee('Import KML Google Earth');
});

it('renders the news article editor with native filament controls and no custom alpine wrapper', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->actingAs(phaseTwoUser(RoleType::SUPER_ADMIN));

    $this->get('/admin/news-articles/create')
        ->assertOk()
        ->assertSee('Informasi &amp; Publikasi Artikel', false)
        ->assertSee('Isi Berita')
        ->assertSee('Judul Artikel')
        ->assertSee('Slug URL')
        ->assertSee('Foto Thumbnail')
        ->assertSee('Karang Taruna')
        ->assertSee('Pemerintah Desa')
        ->assertSee('news-content-editor', false)
        ->assertSee('/js/filament/support/support.js', false)
        ->assertSee('/js/filament/forms/components/rich-editor.js', false)
        ->assertSee('/js/filament/forms/components/select.js', false)
        ->assertSee('/js/filament/forms/components/file-upload.js', false)
        ->assertDontSee('news_draft_content', false)
        ->assertDontSee('Ditemukan draft isi berita', false);

    config()->set('trustedproxy.proxies', '*');

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.10'])
        ->withHeaders([
            'Host' => 'internal-web',
            'X-Forwarded-Host' => 'admin.kalimati.test',
            'X-Forwarded-Proto' => 'https',
        ])
        ->get('/admin/news-articles/create')
        ->assertOk()
        ->assertSee('https://admin.kalimati.test/js/filament/forms/components/rich-editor.js', false)
        ->assertDontSee('http://admin.kalimati.test/js/filament/forms/components/rich-editor.js', false);

    $createPage = Livewire::test(CreateNewsArticle::class);

    expect($createPage->instance()->getMaxContentWidth())->toBe(MaxWidth::Full);

    $createPage
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

    $editPage = Livewire::test(EditNewsArticle::class, ['record' => $article->getRouteKey()]);

    expect($editPage->instance()->getMaxContentWidth())->toBe(MaxWidth::Full);

    $editPage
        ->assertFormSet([
            'title' => 'Pelatihan Digital Karang Taruna',
            'category' => NewsCategory::KARANG_TARUNA->value,
            'content' => '<p>Materi pelatihan digital.</p>',
        ]);
});
