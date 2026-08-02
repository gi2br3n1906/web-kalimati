<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProfileIndex extends Component
{
    /** @return array{title: string, paragraphs: array<int, string>} */
    public function history(): array
    {
        return [
            'title' => 'Sejarah Desa Kalimati',
            'paragraphs' => [
                'Berasal dari keberadaan rumah petilasan Sunan Kalijogo di selatan sungai yang berpindah secara ajaib dalam semalam ke sebelah kiri Masjid Al-Muslimun di utara sungai. Sesepuh desa kemudian menamakan daerah tersebut Desa Kalimati.',
            ],
        ];
    }

    /** @return array<int, array{period: string, name: string, legacy: string}> */
    public function leaders(): array
    {
        return [
            ['period' => 'Kades I', 'name' => 'Partorejo', 'legacy' => 'Kepala Desa I'],
            ['period' => 'Kades II', 'name' => 'PJ Carik Suwito Sastro', 'legacy' => 'Penjabat Kepala Desa II'],
            ['period' => 'Kades III', 'name' => 'PJ Koramil Miswandi', 'legacy' => 'Penjabat Kepala Desa III'],
            ['period' => '1978–1996', 'name' => 'Sukono', 'legacy' => 'Kepala Desa Kalimati'],
            ['period' => '1997–2000', 'name' => 'Suwandi', 'legacy' => 'Kepala Desa Kalimati'],
            ['period' => '2001–2013', 'name' => 'Sri Sulastri', 'legacy' => 'Kepala Desa Kalimati'],
            ['period' => '2013–2027', 'name' => 'Darmanto', 'legacy' => 'Kepala Desa Kalimati'],
        ];
    }

    /** @return array<int, array{name: string, rw: string, rt: string, population: string, households: string, coverage: string}> */
    public function hamlets(): array
    {
        return [
            ['name' => 'Dusun Dampit', 'rw' => 'RW 01–02', 'rt' => '8 RT', 'population' => '1.823 Jiwa', 'households' => '584 KK', 'coverage' => 'Termasuk Dukuh Pondok'],
            ['name' => 'Dusun Kalimati', 'rw' => 'RW 03', 'rt' => '8 RT', 'population' => '1.438 Jiwa', 'households' => '463 KK', 'coverage' => 'Wilayah Dusun Kalimati'],
            ['name' => 'Dusun Brojo', 'rw' => 'RW 04', 'rt' => '3 RT', 'population' => '574 Jiwa', 'households' => '171 KK', 'coverage' => 'Termasuk Kedungploso & Kedungdondo'],
            ['name' => 'Dusun Kedungrandu', 'rw' => 'RW 05', 'rt' => '2 RT', 'population' => '386 Jiwa', 'households' => '125 KK', 'coverage' => 'Wilayah Dusun Kedungrandu'],
        ];
    }

    /** @return array{vision: string} */
    public function direction(): array
    {
        return [
            'vision' => 'Meningkatkan pelayanan kepada masyarakat, pemberdayaan masyarakat dan meningkatkan kesejahteraan masyarakat.',
        ];
    }

    /** @return array{name: string, chair: string, unit: string} */
    public function villageEnterprise(): array
    {
        return [
            'name' => 'Margi Lestari',
            'chair' => 'Eka Fajar Suryansyah',
            'unit' => 'Pulsa & Listrik',
        ];
    }

    /** @return array<int, string> */
    public function culturalArts(): array
    {
        return [
            'Seni Barong (Kalimati)',
            'Seni Rodat (Kalimati)',
            'Karawitan Madyo Laras (Dampit)',
            'Karawitan Puja Laras (Kedungrandu)',
            'Rebana Lintang Songo (Kalimati)',
            'Campursari Sahita',
            'Campursari Agung Wijaya',
        ];
    }

    /** @return array<int, array{type: string, count: string, detail: string}> */
    public function worshipPlaces(): array
    {
        return [
            ['type' => 'Masjid', 'count' => '6', 'detail' => 'Masjid di wilayah Desa Kalimati'],
            ['type' => 'Mushola', 'count' => '10', 'detail' => 'Mushola di wilayah Desa Kalimati'],
            ['type' => 'Pura', 'count' => '2', 'detail' => 'Pura Lingga Dharma Buana di Dampit & Pura Puja Resi Wiyasa di Kedungrandu'],
        ];
    }

    public function render(): View
    {
        return view('livewire.public.profile-index', [
            'history' => $this->history(),
            'leaders' => $this->leaders(),
            'hamlets' => $this->hamlets(),
            'direction' => $this->direction(),
            'villageEnterprise' => $this->villageEnterprise(),
            'culturalArts' => $this->culturalArts(),
            'worshipPlaces' => $this->worshipPlaces(),
        ])->layout('components.layouts.app', ['title' => 'Profil & Sejarah Desa Kalimati']);
    }
}
