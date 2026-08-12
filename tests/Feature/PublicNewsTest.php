<?php

declare(strict_types=1);

use App\Enums\NewsCategory;
use App\Livewire\Public\NewsIndex;
use App\Models\NewsArticle;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

it('renders and filters the public village news catalog', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->get('/berita')
        ->assertOk()
        ->assertSee('Kabar & Publikasi Desa', escape: false)
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertSee('Karang Taruna Gelar Kerja Bakti Lingkungan')
        ->assertSee('Pemdes Perbarui Layanan Informasi Publik')
        ->assertSee('Panel Admin');

    Livewire::test(NewsIndex::class)
        ->set('category', NewsCategory::KKN->value)
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertDontSee('Karang Taruna Gelar Kerja Bakti Lingkungan')
        ->set('search', 'literasi')
        ->assertSee('Pendampingan Literasi Digital bagi Warga');
});

it('renders a public news detail and rejects an unknown slug', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->get('/berita/pemetaan-potensi-desa-bersama-tim-kkn')
        ->assertOk()
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertSee('Tim KKN Desa Kalimati')
        ->assertSee('Artikel Terkait');

    $this->get('/berita/slug-tidak-tersedia')->assertNotFound();
});

it('reflects news database create update and delete operations on public pages', function (): void {
    $author = User::factory()->create();
    $article = NewsArticle::create([
        'author_id' => $author->getKey(),
        'title' => 'Berita Baru dari Panel Admin',
        'slug' => 'berita-baru-dari-panel-admin',
        'category' => NewsCategory::PEMDES,
        'content' => '<p>Konten berita yang tersimpan di database.</p>',
        'is_published' => true,
        'published_at' => now()->subMinute(),
    ]);

    $this->get('/berita')
        ->assertOk()
        ->assertSee('Berita Baru dari Panel Admin');

    $this->get('/berita/berita-baru-dari-panel-admin')
        ->assertOk()
        ->assertSee('Konten berita yang tersimpan di database.');

    $article->update(['title' => 'Berita Diperbarui dari Panel Admin']);

    $this->get('/berita')
        ->assertOk()
        ->assertSee('Berita Diperbarui dari Panel Admin')
        ->assertDontSee('Berita Baru dari Panel Admin');

    $article->delete();

    $this->get('/berita')
        ->assertOk()
        ->assertDontSee('Berita Diperbarui dari Panel Admin');

    $this->get('/berita/berita-baru-dari-panel-admin')->assertNotFound();
});

it('hides draft and future news from public pages', function (): void {
    $author = User::factory()->create();
    NewsArticle::factory()->create([
        'author_id' => $author->getKey(),
        'title' => 'Draft Rahasia Desa',
        'is_published' => false,
        'published_at' => now()->subMinute(),
    ]);
    NewsArticle::factory()->create([
        'author_id' => $author->getKey(),
        'title' => 'Berita Terjadwal Besok',
        'is_published' => true,
        'published_at' => now()->addDay(),
    ]);

    $this->get('/berita')
        ->assertOk()
        ->assertDontSee('Draft Rahasia Desa')
        ->assertDontSee('Berita Terjadwal Besok');
});

it('removes obsolete public umkm and research routes', function (): void {
    expect(Route::has('public.umkm.directory'))->toBeFalse()
        ->and(Route::has('public.umkm.ledger'))->toBeFalse()
        ->and(Route::has('public.research.archive'))->toBeFalse();

    $this->get('/umkm')->assertNotFound();
    $this->get('/umkm/kas')->assertNotFound();
    $this->get('/riset')->assertNotFound();
});
