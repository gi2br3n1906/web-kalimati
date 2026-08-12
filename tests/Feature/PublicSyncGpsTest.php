<?php

declare(strict_types=1);

it('renders the mobile GPS synchronization tool', function (): void {
    $this->get('/sync-gps')
        ->assertOk()
        ->assertSee('Sinkron GPS HP ke Alat Sawah')
        ->assertSee('Terputus')
        ->assertSee('Hubungkan Bluetooth ke Alat Sawah')
        ->assertSee('Latitude')
        ->assertSee('Longitude')
        ->assertSee('Akurasi')
        ->assertSee('4fafc201-1fb5-459e-8fcc-c5c9c331914b')
        ->assertSee('data-sync-gps', escape: false)
        ->assertSee('min-h-16', escape: false)
        ->assertSee(route('public.gps.sync'));
});

it('exposes the GPS synchronization shortcut in the public map navigation', function (): void {
    $this->get('/peta')
        ->assertOk()
        ->assertSee(route('public.gps.sync'))
        ->assertSee('Sinkron GPS');
});
