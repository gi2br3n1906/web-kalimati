<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SyncGps extends Component
{
    private const DEVICE_NAME = 'ESP32-GPS-Sync';

    private const SERVICE_UUID = '4fafc201-1fb5-459e-8fcc-c5c9c331914b';

    public function render(): View
    {
        return view('livewire.public.sync-gps', [
            'bluetoothConfiguration' => [
                'deviceName' => self::DEVICE_NAME,
                'serviceUuid' => self::SERVICE_UUID,
            ],
        ])->layout('components.layouts.app', [
            'title' => 'Sinkron GPS ke Alat Sawah',
        ]);
    }
}
