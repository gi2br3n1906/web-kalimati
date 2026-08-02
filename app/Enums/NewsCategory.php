<?php

declare(strict_types=1);

namespace App\Enums;

enum NewsCategory: string
{
    case KEGIATAN = 'kegiatan';
    case PENGUMUMAN = 'pengumuman';
    case POTENSI_DESA = 'potensi_desa';
    case KESEHATAN = 'kesehatan';

    public function label(): string
    {
        return match ($this) {
            self::KEGIATAN => 'Kegiatan',
            self::PENGUMUMAN => 'Pengumuman',
            self::POTENSI_DESA => 'Potensi Desa',
            self::KESEHATAN => 'Kesehatan',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $category) {
            $options[$category->value] = $category->label();
        }

        return $options;
    }
}
