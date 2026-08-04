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
        ->assertSee('Lahan & Kelembagaan 2026', escape: false)
        ->assertSee('100% Tadah Hujan')
        ->assertSee('97,38 Ha')
        ->assertSee('Jagung & Pisang')
        ->assertSee('Tembakau & Singkong')
        ->assertSee('110–120 HST')
        ->assertSee('Ulat Grayak')
        ->assertSee('Engkok (Uret)')
        ->assertSee('pH Tanah')
        ->assertSee('NPK Tanah')
        ->assertSee('Telemetri Sensor IoT Tanah & AI Gemini', escape: false)
        ->assertSee('sangat vital untuk efisiensi pemupukan')
        ->assertSee('Ngudi Makmur')
        ->assertSee('Seger Waras')
        ->assertSee('Kelompok Tani')
        ->assertSee('720 Petani')
        ->assertSee('TLOGO TIRTO')
        ->assertSee('123 anggota')
        ->assertSee(route('public.gis.map'));
});

it('exposes all shared public navigation routes', function (): void {
    expect(route('public.home', absolute: false))->toBe('/')
        ->and(route('public.profile', absolute: false))->toBe('/profil')
        ->and(route('public.agriculture', absolute: false))->toBe('/pertanian')
        ->and(route('public.gis.map', absolute: false))->toBe('/peta')
        ->and(route('public.news.index', absolute: false))->toBe('/berita')
        ->and(route('public.news.show', 'contoh', absolute: false))->toBe('/berita/contoh');
});
