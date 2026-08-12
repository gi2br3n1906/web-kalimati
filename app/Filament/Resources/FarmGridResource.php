<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\CommodityType;
use App\Enums\LandGridStatus;
use App\Filament\Resources\FarmGridResource\Pages;
use App\Models\IotDevice;
use App\Models\LandGrid;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmGridResource extends Resource
{
    protected static ?string $model = LandGrid::class;

    protected static ?string $slug = 'farm-grids';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Grid Lahan';

    protected static ?string $pluralModelLabel = 'Grid Lahan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grid_code')->label('Kode Grid')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('dusun_name')->label('Dusun')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('commodity_type')->label('Komoditas')->formatStateUsing(static fn (CommodityType $state): string => $state->label())->badge()->sortable(),
                Tables\Columns\TextColumn::make('coordinates')->label('Koordinat')->state(static fn (LandGrid $record): string => sprintf('%.8f, %.8f', $record->latitude, $record->longitude))->copyable(),
                Tables\Columns\TextColumn::make('active_iot_devices_count')->label('Perangkat Aktif')->alignCenter(),
                Tables\Columns\TextColumn::make('iot_device_summary')
                    ->label('Perangkat / Komoditas')
                    ->state(static fn (LandGrid $record): string => $record->iotDevices
                        ->map(static fn (IotDevice $device): string => $device->name.' - '.$device->crop_type)
                        ->join(', '))
                    ->placeholder('Belum ada perangkat')
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')->label('Status')->formatStateUsing(static fn (LandGridStatus $state): string => $state->label())->badge()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('commodity_type')->label('Komoditas')->options(CommodityType::options()),
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(LandGridStatus::options()),
            ])
            ->defaultSort('grid_code');
    }

    /** @return Builder<LandGrid> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['iotDevices' => static function (HasMany $query): void {
                $query->where('is_active', true);
            }])
            ->withCount(['iotDevices as active_iot_devices_count' => static fn (Builder $query): Builder => $query->active()]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmGrids::route('/'),
        ];
    }
}
