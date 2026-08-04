<?php

declare(strict_types=1);

namespace App\Enums;

enum NewsCategory: string
{
    case KKN = 'kkn';
    case KARANG_TARUNA = 'karang_taruna';
    case PEMDES = 'pemdes';

    public function label(): string
    {
        return match ($this) {
            self::KKN => 'KKN',
            self::KARANG_TARUNA => 'Karang Taruna',
            self::PEMDES => 'Pemerintah Desa',
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
