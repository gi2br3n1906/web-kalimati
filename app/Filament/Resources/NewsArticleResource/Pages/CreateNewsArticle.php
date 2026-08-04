<?php

declare(strict_types=1);

namespace App\Filament\Resources\NewsArticleResource\Pages;

use App\Filament\Resources\NewsArticleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class CreateNewsArticle extends CreateRecord
{
    protected static string $resource = NewsArticleResource::class;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] = Auth::id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->js(sprintf(
            'localStorage.removeItem(%s)',
            json_encode(NewsArticleResource::CONTENT_DRAFT_STORAGE_KEY, JSON_THROW_ON_ERROR),
        ));
    }
}
