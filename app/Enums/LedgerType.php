<?php

declare(strict_types=1);

namespace App\Enums;

enum LedgerType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::INCOME->value => self::INCOME->label(),
            self::EXPENSE->value => self::EXPENSE->label(),
        ];
    }
}
