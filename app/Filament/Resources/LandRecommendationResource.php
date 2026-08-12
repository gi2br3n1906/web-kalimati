<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AiConditionStatus;
use App\Filament\Resources\LandRecommendationResource\Pages;
use App\Models\AiRecommendation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LandRecommendationResource extends Resource
{
    protected static ?string $model = AiRecommendation::class;

    protected static ?string $slug = 'land-recommendations';

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Rekomendasi Lahan';

    protected static ?string $pluralModelLabel = 'Rekomendasi Lahan';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Analisis Gemini AI')
                ->schema([
                    Forms\Components\TextInput::make('device.name')->label('Perangkat')->disabled(),
                    Forms\Components\Select::make('condition_status')
                        ->label('Status Kondisi')
                        ->options(collect(AiConditionStatus::cases())->mapWithKeys(static fn (AiConditionStatus $status): array => [$status->value => $status->label()])->all())
                        ->disabled(),
                    Forms\Components\TextInput::make('action_title')->label('Headline')->disabled()->columnSpanFull(),
                    Forms\Components\Textarea::make('recommendation_text')->label('Isi Rekomendasi')->disabled()->rows(8)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('device.name')->label('Perangkat')->description(static fn (AiRecommendation $record): string => $record->device->device_code)->searchable()->sortable(),
            Tables\Columns\TextColumn::make('condition_status')->label('Status Kondisi')
                ->formatStateUsing(static fn (AiConditionStatus $state): string => $state->label())
                ->color(static fn (AiConditionStatus $state): string => $state->color())
                ->badge()
                ->sortable(),
            Tables\Columns\TextColumn::make('action_title')->label('Headline')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('recommendation_text')->label('Isi Rekomendasi')->limit(100)->wrap()->tooltip(static fn (string $state): string => $state),
            Tables\Columns\TextColumn::make('created_at')->label('Dianalisis')->dateTime('d M Y, H:i:s')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('iot_device_id')->relationship('device', 'name')->label('Perangkat')->searchable()->preload(),
            Tables\Filters\SelectFilter::make('condition_status')->label('Status Kondisi')
                ->options(collect(AiConditionStatus::cases())->mapWithKeys(static fn (AiConditionStatus $status): array => [$status->value => $status->label()])->all()),
        ])->actions([
            Tables\Actions\ViewAction::make(),
        ])->defaultSort('created_at', 'desc');
    }

    /**
     * @return Builder<AiRecommendation>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('device');
    }

    /** @return array<string, RelationManagerConfiguration> */
    public static function getRelations(): array
    {
        return [];
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandRecommendations::route('/'),
            'view' => Pages\ViewLandRecommendation::route('/{record}'),
        ];
    }
}
