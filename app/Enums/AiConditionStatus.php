<?php

declare(strict_types=1);

namespace App\Enums;

enum AiConditionStatus: string
{
    case OPTIMAL = 'optimal';
    case CAUTION = 'caution';
    case WARNING = 'warning';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::OPTIMAL => 'Optimal',
            self::CAUTION => 'Waspada',
            self::WARNING => 'Peringatan',
            self::CRITICAL => 'Kritis',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPTIMAL => 'success',
            self::CAUTION => 'warning',
            self::WARNING, self::CRITICAL => 'danger',
        };
    }
}
