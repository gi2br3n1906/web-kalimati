<?php

declare(strict_types=1);

use App\Livewire\Public\LandingIndex;
use Livewire\Livewire;

it('renders the public village landing page with the expected public service links', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('Portal Resmi Pemerintah Desa Kalimati')
        ->assertSee('Mewujudkan Desa Digital, Mandiri, dan Pertanian Presisi Berkelanjutan')
        ->assertSee('4.221 Jiwa')
        ->assertSee('1.343 KK')
        ->assertSee('162,61 Ha')
        ->assertSee('Kecamatan Juwangi, Kabupaten Boyolali, Jawa Tengah')
        ->assertSee('Darmanto')
        ->assertSee('Masa jabatan 2013–2027')
        ->assertSee('Akses Cepat')
        ->assertSee('Sambutan Kepala Desa')
        ->assertSee('Berita & Pengumuman Terbaru', escape: false)
        ->assertSee('Kabar & Berita KKN')
        ->assertSee('Pemetaan Potensi Desa Bersama Tim KKN')
        ->assertSee(route('public.profile'))
        ->assertSee(route('public.agriculture'))
        ->assertSee(route('public.gis.map'))
        ->assertSee(route('public.news.index'))
        ->assertDontSee('/umkm')
        ->assertDontSee('/riset');
});

it('switches the civic hero carousel while rejecting an unavailable slide', function (): void {
    Livewire::test(LandingIndex::class)
        ->assertSet('activeHeroSlide', 0)
        ->assertSee('Portal Informasi &amp; Pelayanan Publik', escape: false)
        ->call('selectHeroSlide', 1)
        ->assertSet('activeHeroSlide', 1)
        ->assertSee('Pertanian Presisi Berbasis Data')
        ->call('selectHeroSlide', 99)
        ->assertSet('activeHeroSlide', 1);
});
