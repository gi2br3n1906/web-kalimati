<?php

declare(strict_types=1);

namespace App\Actions\Umkm;

use App\Enums\LedgerType;
use App\Models\UmkmBusiness;
use Illuminate\Database\Eloquent\Builder;

final class CalculateLedgerSummaryAction
{
    /**
     * @return array{total_income: float, total_expense: float, net_balance: float}
     */
    public function execute(UmkmBusiness $business, ?string $from = null, ?string $until = null): array
    {
        /** @var Builder $query */
        $query = $business->ledgers()->getQuery()->withinDateRange($from, $until);

        $totalIncome = (float) (clone $query)
            ->where('type', LedgerType::INCOME->value)
            ->sum('amount');
        $totalExpense = (float) (clone $query)
            ->where('type', LedgerType::EXPENSE->value)
            ->sum('amount');

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_balance' => $totalIncome - $totalExpense,
        ];
    }
}
