<?php

declare(strict_types=1);

namespace App\Filament\Resources\UmkmBusinessResource\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\UmkmBusinessResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUmkmBusiness extends CreateRecord
{
    protected static string $resource = UmkmBusinessResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->hasRole(RoleType::SUPER_ADMIN->value)) {
            $data['owner_id'] = auth()->id();
        }

        return $data;
    }
}
