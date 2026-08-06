<?php

declare(strict_types=1);

namespace App\Filament\Resources\IotDeviceResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TelemetriesRelationManager extends RelationManager
{
    protected static string $relationship = 'telemetries';

    protected static ?string $title = 'Histori Telemetri';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
            Tables\Columns\TextColumn::make('temp_air')->label('Suhu Udara')->suffix(' °C'),
            Tables\Columns\TextColumn::make('hum_air')->label('Kelembapan Udara')->suffix('%'),
            Tables\Columns\TextColumn::make('temp_soil')->label('Suhu Tanah')->suffix(' °C'),
            Tables\Columns\TextColumn::make('hum_soil_percent')->label('Kelembapan Tanah')->suffix('%'),
            Tables\Columns\TextColumn::make('raw_soil')->label('Raw Soil'),
            Tables\Columns\TextColumn::make('lux_light')->label('Cahaya')->suffix(' lux'),
        ])->defaultSort('created_at', 'desc')->paginated([10, 25, 50]);
    }
}
