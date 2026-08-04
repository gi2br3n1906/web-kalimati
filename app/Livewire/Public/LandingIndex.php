<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Support\PublicNewsCatalog;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LandingIndex extends Component
{
    public int $activeHeroSlide = 0;

    /** @return array<int, array{image: string, eyebrow: string}> */
    public function heroSlides(): array
    {
        return [
            ['image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1800&q=85', 'eyebrow' => 'Portal Informasi & Pelayanan Publik'],
            ['image' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=1800&q=85', 'eyebrow' => 'Pertanian Presisi Berbasis Data'],
        ];
    }

    /** @return array<int, array{label: string, value: string, caption: string}> */
    public function stats(): array
    {
        return [
            ['label' => 'Total Penduduk', 'value' => '4.221 Jiwa', 'caption' => 'Laki-laki 2.119 • Perempuan 2.102'],
            ['label' => 'Kepala Keluarga', 'value' => '1.343 KK', 'caption' => 'Data per Juli 2026'],
            ['label' => 'Luas Wilayah', 'value' => '162,61 Ha', 'caption' => '4 Dusun • 5 RW • 23 RT'],
            ['label' => 'Wilayah Administratif', 'value' => '4 Dusun', 'caption' => 'Dampit, Kalimati, Brojo, Kedungrandu'],
        ];
    }

    /** @return array{name: string, position: string, term: string, location: string, message: string, photo: string} */
    public function headman(): array
    {
        return [
            'name' => 'Darmanto',
            'position' => 'Kepala Desa Kalimati',
            'term' => 'Masa jabatan 2013–2027',
            'location' => 'Kecamatan Juwangi, Kabupaten Boyolali, Jawa Tengah',
            'message' => 'Kami menghadirkan informasi, layanan, peta wilayah, dan potensi desa secara terbuka agar pembangunan Kalimati semakin partisipatif dan berkelanjutan.',
            'photo' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=400&q=80',
        ];
    }

    /** @return array<int, array{title: string, description: string, url: string}> */
    public function quickAccess(): array
    {
        return [
            ['title' => 'Profil & Sejarah Desa', 'description' => 'Asal-usul, wilayah, visi, dan pemerintahan desa.', 'url' => route('public.profile')],
            ['title' => 'Smart Agriculture & IoT', 'description' => 'Telemetri IoT dan rekomendasi pertanian AI.', 'url' => route('public.agriculture')],
            ['title' => 'Peta Spasial Lahan', 'description' => 'Jelajahi fasilitas publik dan petak lahan.', 'url' => route('public.gis.map')],
            ['title' => 'Kabar & Berita KKN', 'description' => 'Reportase KKN, Karang Taruna, dan pengumuman desa.', 'url' => route('public.news.index')],
        ];
    }

    /** @return array<int, array{date: string, category: string, title: string, excerpt: string, image: string}> */
    public function latestNews(): array
    {
        return PublicNewsCatalog::articles()->take(3)->all();
    }

    public function selectHeroSlide(int $slide): void
    {
        if (array_key_exists($slide, $this->heroSlides())) {
            $this->activeHeroSlide = $slide;
        }
    }

    public function render(): View
    {
        return view('livewire.public.landing-index', [
            'heroSlides' => $this->heroSlides(),
            'stats' => $this->stats(),
            'headman' => $this->headman(),
            'quickLinks' => $this->quickAccess(),
            'newsItems' => $this->latestNews(),
        ])->layout('components.layouts.app', ['title' => 'Portal Resmi Desa Kalimati']);
    }
}
