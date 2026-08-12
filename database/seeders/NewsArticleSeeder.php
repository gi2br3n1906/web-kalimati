<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\NewsCategory;
use App\Enums\RoleType;
use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class NewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $authors = $this->authors();
            $articles = $this->articles();

            NewsArticle::query()
                ->whereIn('slug', array_column($articles, 'slug'))
                ->delete();

            foreach ($articles as $article) {
                $authorKey = $article['author'];
                unset($article['author']);

                NewsArticle::create([
                    ...$article,
                    'author_id' => $authors[$authorKey]->getKey(),
                ]);
            }
        });
    }

    /**
     * @return array<string, User>
     */
    private function authors(): array
    {
        return [
            'kkn' => $this->author('Tim KKN Desa Kalimati', 'kkn@kalimati.desa.id'),
            'karang_taruna' => $this->author('Karang Taruna Kalimati', 'karang-taruna@kalimati.desa.id'),
            'pemdes' => $this->author('Pemerintah Desa Kalimati', 'pemdes@kalimati.desa.id'),
        ];
    }

    private function author(string $name, string $email): User
    {
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role_type' => RoleType::ADMIN_DESA,
                'password' => Str::password(32),
            ],
        );

        $user->syncRoles([RoleType::ADMIN_DESA->value]);

        return $user;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function articles(): array
    {
        return [
            [
                'author' => 'kkn',
                'slug' => 'pemetaan-potensi-desa-bersama-tim-kkn',
                'category' => NewsCategory::KKN,
                'title' => 'Pemetaan Potensi Desa Bersama Tim KKN',
                'content' => '<p>Tim KKN bersama Pemerintah Desa Kalimati melaksanakan pemetaan potensi wilayah sebagai dasar penyusunan informasi publik desa.</p><p>Kegiatan mencakup pendataan fasilitas umum, lahan pertanian, kelembagaan warga, serta dokumentasi potensi budaya di setiap dusun.</p>',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=1200&q=85',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'author' => 'karang_taruna',
                'slug' => 'karang-taruna-gelar-kerja-bakti-lingkungan',
                'category' => NewsCategory::KARANG_TARUNA,
                'title' => 'Karang Taruna Gelar Kerja Bakti Lingkungan',
                'content' => '<p>Karang Taruna Desa Kalimati menggerakkan kerja bakti lintas dusun untuk membersihkan saluran air dan fasilitas publik.</p><p>Kegiatan ini memperkuat gotong royong sekaligus mengurangi risiko genangan saat musim hujan.</p>',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=1200&q=85',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'author' => 'pemdes',
                'slug' => 'pemdes-perbarui-layanan-informasi-publik',
                'category' => NewsCategory::PEMDES,
                'title' => 'Pemdes Perbarui Layanan Informasi Publik',
                'content' => '<p>Pemerintah Desa Kalimati memperbarui portal informasi publik agar data desa lebih mudah diakses oleh warga.</p><p>Portal mengintegrasikan profil desa, kabar kegiatan, pertanian presisi, dan peta spasial dalam satu layanan.</p>',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1200&q=85',
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'author' => 'kkn',
                'slug' => 'pendampingan-literasi-digital-kkn',
                'category' => NewsCategory::KKN,
                'title' => 'Pendampingan Literasi Digital bagi Warga',
                'content' => '<p>Pendampingan literasi digital membantu warga menggunakan layanan publik daring secara aman dan mandiri.</p>',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=85',
                'is_published' => true,
                'published_at' => now()->subDays(15),
            ],
            [
                'author' => 'karang_taruna',
                'slug' => 'forum-pemuda-rancang-agenda-kebudayaan',
                'category' => NewsCategory::KARANG_TARUNA,
                'title' => 'Forum Pemuda Rancang Agenda Kebudayaan',
                'content' => '<p>Forum pemuda membahas ruang tampil bagi kelompok seni Barong, Rodat, Karawitan, Rebana, dan Campursari.</p>',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1200&q=85',
                'is_published' => true,
                'published_at' => now()->subDays(19),
            ],
            [
                'author' => 'pemdes',
                'slug' => 'musyawarah-pembangunan-desa-2026',
                'category' => NewsCategory::PEMDES,
                'title' => 'Musyawarah Pembangunan Desa 2026',
                'content' => '<p>Musyawarah desa menjadi ruang partisipasi untuk menyepakati prioritas pembangunan dan pemberdayaan masyarakat.</p>',
                'thumbnail_path' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=85',
                'is_published' => true,
                'published_at' => now()->subDays(25),
            ],
        ];
    }
}
