<?php

declare(strict_types=1);

namespace App\Filament\Resources\IotDeviceResource\RelationManagers;

use App\Enums\AiConditionStatus;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RecommendationsRelationManager extends RelationManager
{
    protected static string $relationship = 'recommendations';

    protected static ?string $title = 'Riwayat Rekomendasi AI';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable(),
            Tables\Columns\TextColumn::make('condition_status')->label('Status')
                ->formatStateUsing(static fn (AiConditionStatus $state): string => $state->label())
                ->color(static fn (AiConditionStatus $state): string => $state->color())->badge(),
            Tables\Columns\TextColumn::make('action_title')->label('Tindakan')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('recommendation_text')->label('Rekomendasi')->limit(120)->wrap()->tooltip(static fn (string $state): string => $state),
        ])->defaultSort('created_at', 'desc')->paginated([10, 25, 50]);
    }
}
