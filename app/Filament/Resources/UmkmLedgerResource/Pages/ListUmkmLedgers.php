<?php

declare(strict_types=1);

namespace App\Filament\Resources\UmkmLedgerResource\Pages;

use App\Filament\Resources\UmkmLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUmkmLedgers extends ListRecords
{
    protected static string $resource = UmkmLedgerResource::class;

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
