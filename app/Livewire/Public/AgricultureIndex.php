<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class AgricultureIndex extends Component
{
    /** @return array<int, array{label: string, value: string, unit: string, status: string}> */
    public function farmingConditions(): array
    {
        return [
            ['label' => 'Sistem Pengairan', 'value' => '100%', 'unit' => 'tadah hujan', 'status' => 'Tergantung musim'],
            ['label' => 'Lahan Pertanian', 'value' => '97,38', 'unit' => 'Ha', 'status' => 'Profil Desa 2026'],
            ['label' => 'Kelompok Tani', 'value' => '6', 'unit' => 'Poktan', 'status' => '720 anggota'],
            ['label' => 'P3A / Pamsimas', 'value' => '1', 'unit' => 'kelompok', 'status' => '123 anggota'],
        ];
    }

    /** @return array{model: string, summary: string, priorities: array<int, string>} */
    public function aiNeed(): array
    {
        return [
            'model' => 'Kebutuhan Prioritas Pertanian Presisi',
            'summary' => 'Implementasi Telemetri IoT Sensor Tanah & Analisis AI Gemini sangat vital untuk efisiensi pemupukan dan pencegahan gagal panen akibat cuaca tidak menentu.',
            'priorities' => [
                'Telemetri IoT Sensor Tanah',
                'Analisis AI Gemini',
                'Efisiensi pemupukan',
                'Pencegahan risiko cuaca',
            ],
        ];
    }

    /** @return array<int, array{name: string, hamlet: string, members: string}> */
    public function farmerGroups(): array
    {
        return [
            ['name' => 'Poktan Ngudi Makmur', 'hamlet' => 'Dusun Dampit', 'members' => '176 anggota'],
            ['name' => 'Poktan Sumber Mulyo', 'hamlet' => 'Dusun Kalimati', 'members' => '155 anggota'],
            ['name' => 'Poktan Margoyoso Lestari', 'hamlet' => 'Dusun Dampit', 'members' => '150 anggota'],
            ['name' => 'Poktan Kismo Tani', 'hamlet' => 'Dusun Brojo', 'members' => '125 anggota'],
            ['name' => 'Poktan Sumber Rejeki', 'hamlet' => 'Dusun Kedungrandu', 'members' => '100 anggota'],
            ['name' => 'Poktan Seger Waras', 'hamlet' => 'Dusun Kalimati', 'members' => '14 anggota'],
        ];
    }

    /** @return array{name: string, hamlet: string, members: string} */
    public function waterUserAssociation(): array
    {
        return ['name' => 'TLOGO TIRTO', 'hamlet' => 'Dusun Kalimati', 'members' => '123'];
    }

    public function render(): View
    {
        return view('livewire.public.agriculture-index', [
            'farmingConditions' => $this->farmingConditions(),
            'aiNeed' => $this->aiNeed(),
            'farmerGroups' => $this->farmerGroups(),
            'waterUserAssociation' => $this->waterUserAssociation(),
        ])->layout('components.layouts.app', ['title' => 'Pertanian Presisi & Komoditas Kalimati']);
    }
}
