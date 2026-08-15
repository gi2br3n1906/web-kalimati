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
            ['name' => 'Poktan Sumber Rejeki', 'hamlet' => 'Dusun Kedungrandu', 'members' => '100 anggota'],
            ['name' => 'Poktan Kismo Tani', 'hamlet' => 'Dusun Brojo', 'members' => '125 anggota'],
            ['name' => 'Poktan Ngudi Makmur', 'hamlet' => 'Dusun Dampit', 'members' => '176 anggota'],
            ['name' => 'Poktan Sumber Mulyo', 'hamlet' => 'Dusun Kalimati', 'members' => '155 anggota'],
            ['name' => 'Poktan Seger Waras', 'hamlet' => 'Dusun Kalimati', 'members' => '14 anggota'],
            ['name' => 'Poktan Margoyoso Lestari', 'hamlet' => 'Dusun Dampit', 'members' => '150 anggota'],
        ];
    }

    /** @return array{name: string, hamlet: string, members: string} */
    public function waterUserAssociation(): array
    {
        return ['name' => 'TLOGO TIRTO', 'hamlet' => 'Dusun Kalimati', 'members' => '123'];
    }

    /** @return array<int, array{title: string, value: string, detail: string}> */
    public function highlights(): array
    {
        return [
            ['title' => 'Total Lahan Pertanian', 'value' => '97,38 Ha', 'detail' => 'Lahan Perhutani melalui Sistem Bagi Hasil'],
            ['title' => 'Jumlah Petani', 'value' => '720 Petani', 'detail' => 'Gapoktan dan 6 Kelompok Tani'],
            ['title' => 'Karakteristik Sawah', 'value' => '100% Tadah Hujan', 'detail' => 'Sangat membutuhkan IoT dan AI Gemini'],
        ];
    }

    /** @return array<int, array{title: string, description: string}> */
    public function cultivationStages(): array
    {
        return [
            ['title' => 'Tahap 1 — Persiapan', 'description' => 'Pembersihan lahan, pengolahan tanah, pembuatan drainase, dan penyiapan benih.'],
            ['title' => 'Tahap 2 — Penanaman & Pemeliharaan', 'description' => 'Tanam benih, pemupukan presisi, penyiangan gulma, dan pengamatan hama.'],
            ['title' => 'Tahap 3 — Panen Optimal', 'description' => 'Panen usia 110–120 HST untuk kualitas tongkol dan hasil terbaik.'],
        ];
    }

    /** @return array<int, array{name: string, impact: string}> */
    public function pests(): array
    {
        return [
            ['name' => 'Tikus', 'impact' => 'Menyerang batang dan tongkol jagung.'],
            ['name' => 'Ulat Grayak', 'impact' => 'Merusak daun hingga titik tumbuh.'],
            ['name' => 'Bule', 'impact' => 'Kerusakan pada bagian daun dan pertumbuhan.'],
            ['name' => 'Engkok (Uret)', 'impact' => 'Menyerang akar tanaman muda hingga layu atau mati.'],
        ];
    }

    /** @return array<int, array{label: string, value: string, detail: string}> */
    public function cropPatterns(): array
    {
        return [
            ['label' => 'Tumpang Sari Utama', 'value' => 'Jagung & Pisang', 'detail' => 'Optimalisasi lahan kering dan pangan'],
            ['label' => 'Komoditas Tambahan', 'value' => 'Tembakau & Singkong', 'detail' => 'Diversifikasi komoditas pertanian'],
            ['label' => 'Siklus Jagung', 'value' => '2–3 kali setahun', 'detail' => 'Panen optimal usia 110–120 HST'],
        ];
    }

    /** @return array<int, array{name: string, unit: string, purpose: string}> */
    public function sensorModules(): array
    {
        return [
            ['name' => 'pH Tanah', 'unit' => 'pH', 'purpose' => 'Kesesuaian tingkat keasaman tanah'],
            ['name' => 'Kelembapan Tanah', 'unit' => '%', 'purpose' => 'Ketersediaan air pada sawah tadah hujan'],
            ['name' => 'NPK Tanah', 'unit' => 'mg/kg', 'purpose' => 'Dasar rekomendasi pemupukan presisi'],
        ];
    }

    public function render(): View
    {
        return view('livewire.public.agriculture-index', [
            'iotMapConfiguration' => [
                'telemetryEndpoint' => route('v1.gis.telemetries', absolute: false),
                'tileProvider' => config('gis.tile_provider'),
                'tileAttribution' => config('gis.tile_attribution'),
                'center' => [(float) config('gis.center.latitude'), (float) config('gis.center.longitude')],
                'zoom' => (int) config('gis.default_zoom'),
            ],
            'farmingConditions' => $this->farmingConditions(),
            'aiNeed' => $this->aiNeed(),
            'farmerGroups' => $this->farmerGroups(),
            'waterUserAssociation' => $this->waterUserAssociation(),
            'highlights' => $this->highlights(),
            'cultivationStages' => $this->cultivationStages(),
            'pests' => $this->pests(),
            'cropPatterns' => $this->cropPatterns(),
            'sensorModules' => $this->sensorModules(),
        ])->layout('components.layouts.app', ['title' => 'Pertanian Presisi & Komoditas Kalimati']);
    }
}
