<?php

declare(strict_types=1);

use App\Actions\Umkm\CalculateLedgerSummaryAction;
use App\Enums\LedgerType;
use App\Models\UmkmBusiness;
use App\Models\UmkmLedger;

it('calculates income expense and net balance accurately within an optional date range', function (): void {
    $business = UmkmBusiness::factory()->create();

    UmkmLedger::factory()->for($business, 'business')->createMany([
        ['type' => LedgerType::INCOME, 'amount' => 150_000, 'transaction_date' => '2026-08-01', 'category' => 'Penjualan Harian'],
        ['type' => LedgerType::INCOME, 'amount' => 200_000, 'transaction_date' => '2026-08-02', 'category' => 'Penjualan Harian'],
        ['type' => LedgerType::INCOME, 'amount' => 150_000, 'transaction_date' => '2026-08-03', 'category' => 'Pesanan'],
        ['type' => LedgerType::EXPENSE, 'amount' => 120_000, 'transaction_date' => '2026-08-02', 'category' => 'Bahan Baku'],
        ['type' => LedgerType::EXPENSE, 'amount' => 80_000, 'transaction_date' => '2026-08-03', 'category' => 'Operasional'],
        ['type' => LedgerType::INCOME, 'amount' => 99_000, 'transaction_date' => '2026-07-30', 'category' => 'Lama'],
    ]);

    $summary = app(CalculateLedgerSummaryAction::class)->execute($business, '2026-08-01', '2026-08-03');

    expect($summary)->toBe([
        'total_income' => 500_000.0,
        'total_expense' => 200_000.0,
        'net_balance' => 300_000.0,
    ]);
});
