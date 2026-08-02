<?php

declare(strict_types=1);

namespace App\Enums;

enum UmkmCategory: string
{
    case KULINER = 'kuliner';
    case KELONTONG = 'kelontong';
    case PERTANIAN = 'pertanian';
    case JASA = 'jasa';
    case KERAJINAN = 'kerajinan';

    public function label(): string
    {
        return match ($this) {
            self::KULINER => 'Kuliner',
            self::KELONTONG => 'Kelontong',
            self::PERTANIAN => 'Pertanian',
            self::JASA => 'Jasa',
            self::KERAJINAN => 'Kerajinan',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_reduce(self::cases(), static fn (array $options, self $category): array => [
            ...$options,
            $category->value => $category->label(),
        ], []);
    }
}
