<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\IotDeviceResource\Pages;
use App\Filament\Resources\IotDeviceResource\RelationManagers\RecommendationsRelationManager;
use App\Filament\Resources\IotDeviceResource\RelationManagers\TelemetriesRelationManager;
use App\Models\IotDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class IotDeviceResource extends Resource
{
    protected static ?string $model = IotDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Perangkat IoT';

    protected static ?string $pluralModelLabel = 'Perangkat IoT';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas Perangkat')->schema([
                Forms\Components\TextInput::make('name')->label('Nama Alat')->required()->maxLength(255),
                Forms\Components\TextInput::make('device_code')->label('Kode Perangkat')->required()->maxLength(100)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('api_token')
                    ->label('API Token')
                    ->password()->revealable()
                    ->default(static fn (): string => Str::random(64))
                    ->required(static fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(static fn (?string $state): bool => filled($state))
                    ->helperText('Token dibuat otomatis. Kosongkan saat edit untuk mempertahankan token lama.'),
                Forms\Components\TextInput::make('crop_type')->label('Komoditas')->default('Jagung')->required()->maxLength(100),
                Forms\Components\Toggle::make('is_active')->label('Perangkat Aktif')->default(true)->required(),
            ])->columns(2),
            Forms\Components\Section::make('Lokasi & Jangkauan')->schema([
                Forms\Components\TextInput::make('latitude')->numeric()->minValue(-90)->maxValue(90)->step('0.00000001')->required(),
                Forms\Components\TextInput::make('longitude')->numeric()->minValue(-180)->maxValue(180)->step('0.00000001')->required(),
                Forms\Components\TextInput::make('coverage_radius_meters')->label('Radius Jangkauan (meter)')->numeric()->integer()->minValue(1)->maxValue(10000)->default(100)->required(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('device_code')->label('Kode')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->label('Nama Alat')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('crop_type')->label('Komoditas')->badge(),
            Tables\Columns\TextColumn::make('coverage_radius_meters')->label('Radius')->suffix(' m')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            Tables\Columns\TextColumn::make('last_active_at')->label('Terakhir Aktif')->dateTime('d M Y H:i')->placeholder('Belum pernah')->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ])->defaultSort('name');
    }

    /** @return array<string, RelationManagerConfiguration> */
    public static function getRelations(): array
    {
        return [
            TelemetriesRelationManager::class,
            RecommendationsRelationManager::class,
        ];
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIotDevices::route('/'),
            'create' => Pages\CreateIotDevice::route('/create'),
            'edit' => Pages\EditIotDevice::route('/{record}/edit'),
        ];
    }
}
