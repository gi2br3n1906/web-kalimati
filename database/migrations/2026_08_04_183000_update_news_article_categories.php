<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE news_articles MODIFY category ENUM('kegiatan', 'pengumuman', 'potensi_desa', 'kesehatan', 'kkn', 'karang_taruna', 'pemdes') NOT NULL");
        DB::table('news_articles')->where('category', 'kegiatan')->update(['category' => 'kkn']);
        DB::table('news_articles')->where('category', 'pengumuman')->update(['category' => 'pemdes']);
        DB::table('news_articles')->whereIn('category', ['potensi_desa', 'kesehatan'])->update(['category' => 'pemdes']);
        DB::statement("ALTER TABLE news_articles MODIFY category ENUM('kkn', 'karang_taruna', 'pemdes') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE news_articles MODIFY category ENUM('kkn', 'karang_taruna', 'pemdes', 'kegiatan', 'pengumuman', 'potensi_desa', 'kesehatan') NOT NULL");
        DB::table('news_articles')->where('category', 'kkn')->update(['category' => 'kegiatan']);
        DB::table('news_articles')->whereIn('category', ['karang_taruna', 'pemdes'])->update(['category' => 'pengumuman']);
        DB::statement("ALTER TABLE news_articles MODIFY category ENUM('kegiatan', 'pengumuman', 'potensi_desa', 'kesehatan') NOT NULL");
    }
};
