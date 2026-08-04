<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\PoiCategory;
use App\Filament\Resources\GisPointOfInterestResource\Pages;
use App\Models\GisPointOfInterest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GisPointOfInterestResource extends Resource
{
    protected static ?string $model = GisPointOfInterest::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Web GIS';

    protected static ?string $modelLabel = 'Titik Lokasi';

    protected static ?string $pluralModelLabel = 'Titik Lokasi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options(PoiCategory::options())
                            ->native(false)
                            ->live()
                            ->required()
                            ->afterStateUpdated(static function (?string $state, Set $set): void {
                                $category = PoiCategory::tryFrom((string) $state);

                                if ($category !== null) {
                                    $set('icon_marker', $category->defaultMarker());
                                }
                            }),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Koordinat & Marker')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step('0.00000001')
                            ->required(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step('0.00000001')
                            ->required(),
                        Forms\Components\Select::make('icon_marker')
                            ->label('Ikon Marker')
                            ->options(PoiCategory::markerOptions())
                            ->native(false)
                            ->searchable()
                            ->nullable(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(static fn (PoiCategory $state): string => $state->label())
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('geometry_type')
                    ->label('Tipe Geometri')
                    ->state(static fn (GisPointOfInterest $record): string => $record->geometryTypeLabel())
                    ->badge(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude')
                    ->numeric(decimalPlaces: 8),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude')
                    ->numeric(decimalPlaces: 8),
                Tables\Columns\TextColumn::make('icon_marker')
                    ->label('Marker')
                    ->placeholder('Default kategori')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(PoiCategory::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    /**
     * @return array<string, RelationManagerConfiguration>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGisPointsOfInterest::route('/'),
            'create' => Pages\CreateGisPointOfInterest::route('/create'),
            'edit' => Pages\EditGisPointOfInterest::route('/{record}/edit'),
        ];
    }
}
