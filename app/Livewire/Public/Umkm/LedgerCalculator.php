<?php

declare(strict_types=1);

namespace App\Livewire\Public\Umkm;

use App\Actions\Umkm\CalculateLedgerSummaryAction;
use App\Enums\RoleType;
use App\Models\UmkmBusiness;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class LedgerCalculator extends Component
{
    public ?int $businessId = null;

    public string $from = '';

    public string $until = '';

    public function mount(): void
    {
        $user = auth()->user();

        abort_unless(
            $user !== null && $user->hasAnyRole([RoleType::SUPER_ADMIN->value, RoleType::UMKM->value]),
            403,
        );

        $this->businessId = $this->availableBusinesses()->first()?->getKey();
    }

    public function render(CalculateLedgerSummaryAction $calculateLedgerSummary): View
    {
        $business = $this->selectedBusiness();
        $summary = $business === null
            ? ['total_income' => 0.0, 'total_expense' => 0.0, 'net_balance' => 0.0]
            : $calculateLedgerSummary->execute($business, $this->from ?: null, $this->until ?: null);

        return view('livewire.public.umkm.ledger-calculator', [
            'businesses' => $this->availableBusinesses(),
            'business' => $business,
            'summary' => $summary,
            'entries' => $business?->ledgers()->withinDateRange($this->from ?: null, $this->until ?: null)->latest('transaction_date')->limit(12)->get() ?? collect(),
        ])->layout('components.layouts.app', [
            'title' => 'Ringkasan Kas UMKM',
        ]);
    }

    /**
     * @return Collection<int, UmkmBusiness>
     */
    private function availableBusinesses(): Collection
    {
        $user = auth()->user();

        return UmkmBusiness::query()
            ->when(! $user?->hasRole(RoleType::SUPER_ADMIN->value), static fn ($query) => $query->forOwner((int) $user?->getKey()))
            ->orderBy('business_name')
            ->get();
    }

    private function selectedBusiness(): ?UmkmBusiness
    {
        return $this->availableBusinesses()->firstWhere('id', $this->businessId);
    }
}
