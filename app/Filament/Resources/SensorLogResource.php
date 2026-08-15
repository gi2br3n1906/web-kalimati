<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SensorLogResource\Pages;
use App\Models\IotTelemetry;
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
    protected static ?string $model = IotTelemetry::class;

    protected static ?string $slug = 'sensor-logs';

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Log Sensor';

    protected static ?string $pluralModelLabel = 'Log Sensor';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('device.name')->label('Perangkat')->disabled(),
            Forms\Components\TextInput::make('temp_air')->label('Suhu Udara (C)')->disabled(),
            Forms\Components\TextInput::make('hum_air')->label('Kelembapan Udara (%)')->disabled(),
            Forms\Components\TextInput::make('temp_soil')->label('Suhu Tanah (C)')->disabled(),
            Forms\Components\TextInput::make('hum_soil_percent')->label('Kelembapan Tanah (%)')->disabled(),
            Forms\Components\TextInput::make('lux_light')->label('Cahaya (Lux)')->disabled(),
            Forms\Components\TextInput::make('latitude')->label('Latitude')->disabled(),
            Forms\Components\TextInput::make('longitude')->label('Longitude')->disabled(),
            Forms\Components\DateTimePicker::make('created_at')->label('Waktu Catat')->seconds(false)->disabled(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('device.name')->label('Perangkat')->description(static fn (IotTelemetry $record): string => $record->device->device_code)->searchable()->sortable(),
            Tables\Columns\TextColumn::make('temp_air')->label('Suhu Udara')->suffix(' C')->numeric(decimalPlaces: 1)->sortable(),
            Tables\Columns\TextColumn::make('hum_air')->label('Kelembapan Udara')->suffix('%')->numeric(decimalPlaces: 1)->sortable(),
            Tables\Columns\TextColumn::make('temp_soil')->label('Suhu Tanah')->suffix(' C')->numeric(decimalPlaces: 1)->sortable(),
            Tables\Columns\TextColumn::make('hum_soil_percent')->label('Kelembapan Tanah')->suffix('%')->numeric(decimalPlaces: 1)->sortable(),
            Tables\Columns\TextColumn::make('lux_light')->label('Cahaya')->suffix(' Lux')->numeric(decimalPlaces: 0)->sortable(),
            Tables\Columns\TextColumn::make('coordinates')
                ->label('Koordinat')
                ->state(static fn (IotTelemetry $record): string => sprintf('%.8f, %.8f', $record->latitude ?? $record->device->latitude, $record->longitude ?? $record->device->longitude))
                ->copyable()
                ->toggleable(),
            Tables\Columns\TextColumn::make('created_at')->label('Waktu Catat')->dateTime('d M Y, H:i:s')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('iot_device_id')->relationship('device', 'name')->label('Perangkat')->searchable()->preload(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ])->defaultSort('created_at', 'desc');
    }

    /**
     * @return Builder<IotTelemetry>
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
            'index' => Pages\ListSensorLogs::route('/'),
            'view' => Pages\ViewSensorLog::route('/{record}'),
        ];
    }
}
