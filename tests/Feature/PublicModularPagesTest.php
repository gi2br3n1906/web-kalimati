<?php

declare(strict_types=1);

it('renders the profile and history page through the shared civic layout', function (): void {
    $this->get('/profil')
        ->assertOk()
        ->assertSee('Profil & Sejarah Desa Kalimati', escape: false)
        ->assertSee('Sejarah Desa Kalimati')
        ->assertSee('rumah petilasan Sunan Kalijogo')
        ->assertSee('Silsilah Kepala Desa')
        ->assertSee('Partorejo')
        ->assertSee('Darmanto')
        ->assertSee('Dusun Dampit')
        ->assertSee('Dusun Kedungrandu')
        ->assertSee('1.823 Jiwa')
        ->assertSee('Meningkatkan pelayanan kepada masyarakat')
        ->assertSee('BUMDes Margi Lestari')
        ->assertSee('Eka Fajar Suryansyah')
        ->assertSee('Karawitan Madyo Laras')
        ->assertSee('Pura Lingga Dharma Buana')
        ->assertSee('Buka menu navigasi')
        ->assertSee('Kontak Balai Desa');
});

it('renders the smart agriculture page with telemetry AI and commodity modules', function (): void {
    $this->get('/pertanian')
        ->assertOk()
        ->assertSee('Pertanian Presisi Desa Kalimati')
        ->assertSee('Kondisi Pertanian 2026')
        ->assertSee('100% tadah hujan')
        ->assertSee('97,38 Ha')
        ->assertSee('Telemetri IoT & Analisis AI Gemini', escape: false)
        ->assertSee('sangat vital untuk efisiensi pemupukan')
        ->assertSee('Poktan Ngudi Makmur')
        ->assertSee('Poktan Seger Waras')
        ->assertSee('Kelompok Tani')
        ->assertSee('720 anggota')
        ->assertSee('TLOGO TIRTO')
        ->assertSee('123 anggota')
        ->assertSee(route('public.gis.map'));
});

it('exposes all shared public navigation routes', function (): void {
    expect(route('public.home', absolute: false))->toBe('/')
        ->and(route('public.profile', absolute: false))->toBe('/profil')
        ->and(route('public.agriculture', absolute: false))->toBe('/pertanian')
        ->and(route('public.gis.map', absolute: false))->toBe('/peta')
        ->and(route('public.umkm.directory', absolute: false))->toBe('/umkm')
        ->and(route('public.research.archive', absolute: false))->toBe('/riset');
});
