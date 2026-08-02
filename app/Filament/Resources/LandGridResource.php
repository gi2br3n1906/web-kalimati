<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\CommodityType;
use App\Enums\LandGridStatus;
use App\Filament\Actions\RequestAiRecommendationAction;
use App\Filament\Resources\LandGridResource\Pages;
use App\Models\LandGrid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LandGridResource extends Resource
{
    protected static ?string $model = LandGrid::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Grid Lahan';

    protected static ?string $pluralModelLabel = 'Grid Lahan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas Lahan')
                ->schema([
                    Forms\Components\TextInput::make('grid_code')->label('Kode Grid')->required()->maxLength(50)->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('dusun_name')->label('Dusun')->required()->maxLength(100),
                    Forms\Components\Select::make('commodity_type')->label('Komoditas')->options(CommodityType::options())->native(false)->required(),
                    Forms\Components\TextInput::make('owner_name')->label('Pemilik Lahan')->maxLength(255),
                    Forms\Components\Select::make('status')->label('Status')->options(LandGridStatus::options())->native(false)->default(LandGridStatus::ACTIVE->value)->required(),
                ])->columns(2),
            Forms\Components\Section::make('Titik Tengah & Batas')
                ->schema([
                    Forms\Components\TextInput::make('latitude')->label('Latitude')->numeric()->minValue(-90)->maxValue(90)->step('0.00000001')->required(),
                    Forms\Components\TextInput::make('longitude')->label('Longitude')->numeric()->minValue(-180)->maxValue(180)->step('0.00000001')->required(),
                    Forms\Components\ViewField::make('geojson_polygon')
                        ->label('Batas Polygon GeoJSON')
                        ->view('filament.forms.components.land-grid-polygon-editor')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grid_code')->label('Kode Grid')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('dusun_name')->label('Dusun')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('commodity_type')->label('Komoditas')->formatStateUsing(static fn (CommodityType $state): string => $state->label())->badge()->sortable(),
                Tables\Columns\TextColumn::make('owner_name')->label('Pemilik')->placeholder('-')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->formatStateUsing(static fn (LandGridStatus $state): string => $state->label())->badge()->sortable(),
                Tables\Columns\TextColumn::make('sensor_logs_count')->label('Log')->counts('sensorLogs')->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commodity_type')->label('Komoditas')->options(CommodityType::options()),
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(LandGridStatus::options()),
            ])
            ->actions([
                RequestAiRecommendationAction::make('request_ai_recommendation'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('grid_code');
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
            'index' => Pages\ListLandGrids::route('/'),
            'create' => Pages\CreateLandGrid::route('/create'),
            'edit' => Pages\EditLandGrid::route('/{record}/edit'),
        ];
    }
}
