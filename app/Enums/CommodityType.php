<?php

declare(strict_types=1);

namespace App\Enums;

enum CommodityType: string
{
    case JAGUNG = 'jagung';
    case PISANG = 'pisang';
    case SINGKONG = 'singkong';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::JAGUNG => 'Jagung',
            self::PISANG => 'Pisang',
            self::SINGKONG => 'Singkong',
            self::LAINNYA => 'Lainnya',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $options, self $commodity): array => [
                ...$options,
                $commodity->value => $commodity->label(),
            ],
            [],
        );
    }
}
