<?php

declare(strict_types=1);

namespace App\Enums;

enum ResearchCategory: string
{
    case MONOGRAFI = 'monografi';
    case SAINTEK = 'saintek';
    case SOSHUM = 'soshum';
    case PETA = 'peta';
    case LAPORAN_KKN = 'laporan_kkn';

    public function label(): string
    {
        return match ($this) {
            self::MONOGRAFI => 'Monografi', self::SAINTEK => 'Saintek', self::SOSHUM => 'Soshum', self::PETA => 'Peta', self::LAPORAN_KKN => 'Laporan KKN',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return array_reduce(self::cases(), static fn (array $options, self $category): array => [...$options, $category->value => $category->label()], []);
    }
}
