<?php

declare(strict_types=1);

namespace App\Enums;

enum LandGridStatus: string
{
    case ACTIVE = 'active';
    case FALLOW = 'fallow';
    case HARVESTED = 'harvested';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::FALLOW => 'Bera',
            self::HARVESTED => 'Panen',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $options, self $status): array => [
                ...$options,
                $status->value => $status->label(),
            ],
            [],
        );
    }
}
