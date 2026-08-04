<?php

declare(strict_types=1);

use App\Livewire\Public\NewsIndex;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

it('renders and filters the public village news catalog', function (): void {
    $this->get('/berita')
        ->assertOk()
        ->assertSee('Kabar & Publikasi Desa', escape: false)
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertSee('Karang Taruna Gelar Kerja Bakti Lingkungan')
        ->assertSee('Pemdes Perbarui Layanan Informasi Publik')
        ->assertSee('Panel Admin');

    Livewire::test(NewsIndex::class)
        ->set('category', 'KKN')
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertDontSee('Karang Taruna Gelar Kerja Bakti Lingkungan')
        ->set('search', 'literasi')
        ->assertSee('Pendampingan Literasi Digital bagi Warga');
});

it('renders a public news detail and rejects an unknown slug', function (): void {
    $this->get('/berita/pemetaan-potensi-desa-bersama-tim-kkn')
        ->assertOk()
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertSee('Tim KKN Desa Kalimati')
        ->assertSee('Artikel Terkait');

    $this->get('/berita/slug-tidak-tersedia')->assertNotFound();
});

it('removes obsolete public umkm and research routes', function (): void {
    expect(Route::has('public.umkm.directory'))->toBeFalse()
        ->and(Route::has('public.umkm.ledger'))->toBeFalse()
        ->and(Route::has('public.research.archive'))->toBeFalse();

    $this->get('/umkm')->assertNotFound();
    $this->get('/umkm/kas')->assertNotFound();
    $this->get('/riset')->assertNotFound();
});
