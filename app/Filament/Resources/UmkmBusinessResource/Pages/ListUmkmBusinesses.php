<?php

declare(strict_types=1);

namespace App\Filament\Resources\UmkmBusinessResource\Pages;

use App\Filament\Resources\UmkmBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUmkmBusinesses extends ListRecords
{
    protected static string $resource = UmkmBusinessResource::class;

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
