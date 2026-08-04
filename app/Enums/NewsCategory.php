<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NewsCategory: string implements HasLabel
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

    public function getLabel(): string
    {
        return $this->label();
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
