<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Collection;

final class PublicNewsCatalog
{
    /** @return Collection<int, array<string, string>> */
    public static function articles(): Collection
    {
        return collect([
            ['slug' => 'pemetaan-potensi-desa-bersama-tim-kkn', 'category' => 'KKN', 'date' => '03 Agustus 2026', 'author' => 'Tim KKN Desa Kalimati', 'title' => 'Pemetaan Potensi Desa Bersama Tim KKN', 'excerpt' => 'Mahasiswa KKN mendampingi pemetaan fasilitas publik, lahan pertanian, dan potensi sosial Desa Kalimati.', 'image' => 'https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=1200&q=85', 'content' => '<p>Tim KKN bersama Pemerintah Desa Kalimati melaksanakan pemetaan potensi wilayah sebagai dasar penyusunan informasi publik desa.</p><p>Kegiatan mencakup pendataan fasilitas umum, lahan pertanian, kelembagaan warga, serta dokumentasi potensi budaya di setiap dusun.</p>'],
            ['slug' => 'karang-taruna-gelar-kerja-bakti-lingkungan', 'category' => 'Karang Taruna', 'date' => '30 Juli 2026', 'author' => 'Karang Taruna Kalimati', 'title' => 'Karang Taruna Gelar Kerja Bakti Lingkungan', 'excerpt' => 'Pemuda desa membersihkan saluran air dan ruang publik untuk menghadapi musim hujan.', 'image' => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=1200&q=85', 'content' => '<p>Karang Taruna Desa Kalimati menggerakkan kerja bakti lintas dusun untuk membersihkan saluran air dan fasilitas publik.</p><p>Kegiatan ini memperkuat gotong royong sekaligus mengurangi risiko genangan saat musim hujan.</p>'],
            ['slug' => 'pemdes-perbarui-layanan-informasi-publik', 'category' => 'Pemerintah Desa', 'date' => '25 Juli 2026', 'author' => 'Pemerintah Desa Kalimati', 'title' => 'Pemdes Perbarui Layanan Informasi Publik', 'excerpt' => 'Portal desa diperbarui untuk memperluas akses warga terhadap profil, berita, pertanian, dan data spasial.', 'image' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1200&q=85', 'content' => '<p>Pemerintah Desa Kalimati memperbarui portal informasi publik agar data desa lebih mudah diakses oleh warga.</p><p>Portal mengintegrasikan profil desa, kabar kegiatan, pertanian presisi, dan peta spasial dalam satu layanan.</p>'],
            ['slug' => 'pendampingan-literasi-digital-kkn', 'category' => 'KKN', 'date' => '20 Juli 2026', 'author' => 'Tim KKN Desa Kalimati', 'title' => 'Pendampingan Literasi Digital bagi Warga', 'excerpt' => 'Tim KKN mengenalkan akses layanan digital dan praktik keamanan informasi dasar.', 'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=85', 'content' => '<p>Pendampingan literasi digital membantu warga menggunakan layanan publik daring secara aman dan mandiri.</p>'],
            ['slug' => 'forum-pemuda-rancang-agenda-kebudayaan', 'category' => 'Karang Taruna', 'date' => '16 Juli 2026', 'author' => 'Karang Taruna Kalimati', 'title' => 'Forum Pemuda Rancang Agenda Kebudayaan', 'excerpt' => 'Pemuda menyusun agenda kolaboratif untuk mendukung kesenian lokal Kalimati.', 'image' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1200&q=85', 'content' => '<p>Forum pemuda membahas ruang tampil bagi kelompok seni Barong, Rodat, Karawitan, Rebana, dan Campursari.</p>'],
            ['slug' => 'musyawarah-pembangunan-desa-2026', 'category' => 'Pemerintah Desa', 'date' => '10 Juli 2026', 'author' => 'Pemerintah Desa Kalimati', 'title' => 'Musyawarah Pembangunan Desa 2026', 'excerpt' => 'Warga dan pemerintah desa menyelaraskan prioritas pelayanan serta pembangunan wilayah.', 'image' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=85', 'content' => '<p>Musyawarah desa menjadi ruang partisipasi untuk menyepakati prioritas pembangunan dan pemberdayaan masyarakat.</p>'],
        ]);
    }

    /** @return array<string, string> */
    public static function categories(): array
    {
        return ['KKN' => 'KKN', 'Karang Taruna' => 'Karang Taruna', 'Pemerintah Desa' => 'Pemerintah Desa'];
    }
}
