<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LocationPointResource\Pages;
use App\Models\IotDevice;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LocationPointResource extends Resource
{
    protected static ?string $model = IotDevice::class;

    protected static ?string $slug = 'location-points';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Web GIS';

    protected static ?string $navigationLabel = 'Titik Lokasi';

    protected static ?string $modelLabel = 'Titik Lokasi IoT';

    protected static ?string $pluralModelLabel = 'Titik Lokasi IoT';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_code')->label('Kode')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Perangkat')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('crop_type')->label('Komoditas')->badge()->sortable(),
                Tables\Columns\TextColumn::make('landGrid.grid_code')->label('Grid Terdekat')->placeholder('Belum terasosiasi')->sortable(),
                Tables\Columns\TextColumn::make('latitude')->label('Latitude')->numeric(decimalPlaces: 8)->copyable(),
                Tables\Columns\TextColumn::make('longitude')->label('Longitude')->numeric(decimalPlaces: 8)->copyable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('last_active_at')->label('Telemetri Terakhir')->dateTime('d M Y, H:i:s')->placeholder('Belum pernah')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Perangkat'),
            ])
            ->defaultSort('last_active_at', 'desc');
    }

    /** @return Builder<IotDevice> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('landGrid');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('view_any_iot::device') === true
            || $user?->can('view_any_gis::point::of::interest') === true;
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocationPoints::route('/'),
        ];
    }
}
