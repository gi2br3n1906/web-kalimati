<?php

declare(strict_types=1);

namespace App\Filament\Resources\NewsArticleResource\Pages;

use App\Filament\Resources\NewsArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;

class EditNewsArticle extends EditRecord
{
    protected static string $resource = NewsArticleResource::class;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
