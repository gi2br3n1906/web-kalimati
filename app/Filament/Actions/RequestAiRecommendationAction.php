<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\Agriculture\FetchLLMRecommendationAction;
use App\Models\LandGrid;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

final class RequestAiRecommendationAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Minta Rekomendasi AI')
            ->icon('heroicon-o-sparkles')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Minta Rekomendasi Tanah')
            ->modalDescription('Rekomendasi dibuat dari pembacaan sensor terbaru pada grid ini.')
            ->action(function (LandGrid $record, FetchLLMRecommendationAction $fetchRecommendation): void {
                $recommendation = $fetchRecommendation->execute($record);
                $isFallback = $recommendation->ai_model_used === 'fallback-offline';

                Notification::make()
                    ->title($isFallback ? 'Rekomendasi fallback tersimpan' : 'Rekomendasi AI tersimpan')
                    ->body($isFallback ? 'Layanan AI tidak tersedia, sehingga panduan aman sementara telah dibuat.' : 'Panduan tindakan untuk lahan ini sudah tersedia.')
                    ->{$isFallback ? 'warning' : 'success'}()
                    ->send();
            });
    }
}
