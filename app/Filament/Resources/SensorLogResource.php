<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SensorLogResource\Pages;
use App\Models\SensorLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SensorLogResource extends Resource
{
    protected static ?string $model = SensorLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Log Sensor';

    protected static ?string $pluralModelLabel = 'Log Sensor';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('landGrid.grid_code')->label('Kode Grid')->disabled(),
            Forms\Components\TextInput::make('device_id')->label('Perangkat')->disabled(),
            Forms\Components\TextInput::make('ph_level')->label('pH')->disabled(),
            Forms\Components\TextInput::make('moisture_percentage')->label('Kelembapan (%)')->disabled(),
            Forms\Components\TextInput::make('temperature_celsius')->label('Suhu (C)')->disabled(),
            Forms\Components\DateTimePicker::make('recorded_at')->label('Direkam')->seconds(false)->disabled(),
            Forms\Components\KeyValue::make('raw_payload')->label('Payload Perangkat')->disabled()->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('landGrid.grid_code')->label('Kode Grid')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('device_id')->label('Perangkat')->searchable(),
            Tables\Columns\TextColumn::make('ph_level')->label('pH')->numeric(decimalPlaces: 2),
            Tables\Columns\TextColumn::make('moisture_percentage')->label('Kelembapan')->suffix('%')->numeric(decimalPlaces: 2),
            Tables\Columns\TextColumn::make('temperature_celsius')->label('Suhu')->suffix(' C')->numeric(decimalPlaces: 2),
            Tables\Columns\TextColumn::make('recorded_at')->label('Direkam')->dateTime('d M Y, H:i')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('land_grid_id')->relationship('landGrid', 'grid_code')->label('Grid Lahan'),
        ])->actions([Tables\Actions\ViewAction::make()])->defaultSort('recorded_at', 'desc');
    }

    /**
     * @return Builder<SensorLog>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('landGrid');
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
            'index' => Pages\ListSensorLogs::route('/'),
            'view' => Pages\ViewSensorLog::route('/{record}'),
        ];
    }
}
