<?php

declare(strict_types=1);

namespace App\Filament\Resources\UmkmBusinessResource\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\UmkmBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUmkmBusiness extends EditRecord
{
    protected static string $resource = UmkmBusinessResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! auth()->user()?->hasRole(RoleType::SUPER_ADMIN->value)) {
            $data['owner_id'] = $this->record->owner_id;
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
