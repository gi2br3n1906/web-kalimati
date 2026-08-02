<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\RoleType;
use App\Enums\UmkmCategory;
use App\Filament\Resources\UmkmBusinessResource\Pages;
use App\Models\UmkmBusiness;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UmkmBusinessResource extends Resource
{
    protected static ?string $model = UmkmBusiness::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'UMKM & Ekonomi';

    protected static ?string $modelLabel = 'Usaha UMKM';

    protected static ?string $pluralModelLabel = 'Usaha UMKM';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profil Usaha')
                ->schema([
                    Forms\Components\TextInput::make('business_name')->label('Nama Usaha')->required()->maxLength(255),
                    Forms\Components\Select::make('owner_id')
                        ->label('Pemilik')
                        ->relationship('owner', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(static fn (): bool => static::isSuperAdmin()),
                    Forms\Components\Select::make('category')->label('Kategori')->options(UmkmCategory::options())->native(false)->required(),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Nomor WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(20)
                        ->regex('/^(?:\+62|62|0)8[1-9][0-9]{6,12}$/'),
                    Forms\Components\FileUpload::make('logo_path')
                        ->label('Logo Usaha')
                        ->image()
                        ->imageEditor()
                        ->directory('umkm-logos')
                        ->disk('public')
                        ->maxSize(5 * 1024),
                    Forms\Components\Textarea::make('address')->label('Alamat')->required()->rows(3)->columnSpanFull(),
                    Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(5)->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')->label('Logo')->disk('public')->height(48)->width(48),
                Tables\Columns\TextColumn::make('business_name')->label('Nama Usaha')->searchable()->sortable()->wrap(),
                Tables\Columns\TextColumn::make('owner.name')->label('Pemilik')->placeholder('Belum terdaftar')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->formatStateUsing(static fn (UmkmCategory $state): string => $state->label())->badge()->sortable(),
                Tables\Columns\TextColumn::make('phone_number')->label('WhatsApp')->copyable(),
                Tables\Columns\TextColumn::make('ledgers_count')->label('Transaksi')->counts('ledgers')->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->label('Kategori')->options(UmkmCategory::options()),
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
            ->defaultSort('business_name');
    }

    /**
     * @return Builder<UmkmBusiness>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('owner');
        $user = auth()->user();

        if ($user === null || $user->hasRole(RoleType::SUPER_ADMIN->value)) {
            return $query;
        }

        return $query->forOwner((int) $user->getKey());
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
            'index' => Pages\ListUmkmBusinesses::route('/'),
            'create' => Pages\CreateUmkmBusiness::route('/create'),
            'edit' => Pages\EditUmkmBusiness::route('/{record}/edit'),
        ];
    }

    private static function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole(RoleType::SUPER_ADMIN->value) ?? false;
    }
}
