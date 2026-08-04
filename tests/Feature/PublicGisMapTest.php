<?php

declare(strict_types=1);

it('renders the public livewire gis map with same-origin api configuration', function (): void {
    $this->get('/peta')
        ->assertOk()
        ->assertSee('Peta Desa Kalimati')
        ->assertSee('Pertanian / IoT')
        ->assertSee('data-gis-map', escape: false)
        ->assertSee('api\\/v1\\/gis\\/points-of-interest', escape: false)
        ->assertSee('livewire/livewire.js', escape: false);
});
