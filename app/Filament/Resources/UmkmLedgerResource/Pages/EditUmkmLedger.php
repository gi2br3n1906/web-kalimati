<?php

declare(strict_types=1);

namespace App\Filament\Resources\UmkmLedgerResource\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\UmkmLedgerResource;
use App\Models\UmkmBusiness;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUmkmLedger extends EditRecord
{
    protected static string $resource = UmkmLedgerResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        if (! $user?->hasRole(RoleType::SUPER_ADMIN->value)) {
            $ownsBusiness = UmkmBusiness::query()
                ->forOwner((int) $user?->getKey())
                ->whereKey($data['umkm_business_id'] ?? null)
                ->exists();

            abort_unless($ownsBusiness, 403);
        }

        return $data;
    }

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
