<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\LedgerType;
use App\Enums\RoleType;
use App\Filament\Resources\UmkmLedgerResource\Pages;
use App\Models\UmkmBusiness;
use App\Models\UmkmLedger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UmkmLedgerResource extends Resource
{
    protected static ?string $model = UmkmLedger::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'UMKM & Ekonomi';

    protected static ?string $modelLabel = 'Catatan Kas';

    protected static ?string $pluralModelLabel = 'Catatan Kas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Transaksi')
                ->schema([
                    Forms\Components\Select::make('umkm_business_id')
                        ->label('Usaha')
                        ->relationship('business', 'business_name', modifyQueryUsing: static fn (Builder $query): Builder => static::businessQuery($query))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\DatePicker::make('transaction_date')->label('Tanggal')->default(today())->required(),
                    Forms\Components\Select::make('type')->label('Jenis')->options(LedgerType::options())->native(false)->required(),
                    Forms\Components\TextInput::make('amount')->label('Jumlah')->numeric()->prefix('Rp')->minValue(0.01)->required(),
                    Forms\Components\TextInput::make('category')->label('Kategori')->required()->maxLength(100),
                    Forms\Components\Textarea::make('notes')->label('Catatan')->rows(4)->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.business_name')->label('Usaha')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Jenis')->formatStateUsing(static fn (LedgerType $state): string => $state->label())->badge(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('IDR', locale: 'id_ID')->sortable(),
                Tables\Columns\TextColumn::make('category')->label('Kategori')->searchable(),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')->limit(40)->placeholder('-')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('Jenis')->options(LedgerType::options()),
                Tables\Filters\SelectFilter::make('umkm_business_id')->label('Usaha')->relationship('business', 'business_name', modifyQueryUsing: static fn (Builder $query): Builder => static::businessQuery($query)),
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
            ->defaultSort('transaction_date', 'desc');
    }

    /**
     * @return Builder<UmkmLedger>
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('business');
        $user = auth()->user();

        if ($user === null || $user->hasRole(RoleType::SUPER_ADMIN->value)) {
            return $query;
        }

        return $query->whereHas('business', static fn (Builder $businessQuery): Builder => $businessQuery->forOwner((int) $user->getKey()));
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
            'index' => Pages\ListUmkmLedgers::route('/'),
            'create' => Pages\CreateUmkmLedger::route('/create'),
            'edit' => Pages\EditUmkmLedger::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Builder<UmkmBusiness>  $query
     * @return Builder<UmkmBusiness>
     */
    private static function businessQuery(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user === null || $user->hasRole(RoleType::SUPER_ADMIN->value)) {
            return $query;
        }

        return $query->forOwner((int) $user->getKey());
    }
}
