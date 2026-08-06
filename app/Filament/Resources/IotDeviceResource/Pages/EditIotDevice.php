<?php

declare(strict_types=1);

namespace App\Filament\Resources\IotDeviceResource\Pages;

use App\Filament\Resources\IotDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIotDevice extends EditRecord
{
    protected static string $resource = IotDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['api_token'] = null;

        return $data;
    }
}
