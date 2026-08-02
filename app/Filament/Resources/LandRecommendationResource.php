<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LandRecommendationResource\Pages;
use App\Models\LandRecommendation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LandRecommendationResource extends Resource
{
    protected static ?string $model = LandRecommendation::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Smart Agriculture';

    protected static ?string $modelLabel = 'Rekomendasi Lahan';

    protected static ?string $pluralModelLabel = 'Rekomendasi Lahan';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Diagnostik AI')
                ->schema([
                    Forms\Components\TextInput::make('landGrid.grid_code')->label('Kode Grid')->disabled(),
                    Forms\Components\TextInput::make('ai_model_used')->label('Model')->disabled(),
                    Forms\Components\Textarea::make('soil_condition_summary')->label('Ringkasan Kondisi Tanah')->disabled()->rows(4)->columnSpanFull(),
                    Forms\Components\Textarea::make('fertilizer_dosage')->label('Dosis Pupuk')->disabled()->rows(4)->columnSpanFull(),
                    Forms\Components\Textarea::make('lime_treatment')->label('Perlakuan Dolomit')->disabled()->rows(4)->columnSpanFull(),
                    Forms\Components\Textarea::make('action_plan')->label('Rencana Tindakan')->disabled()->rows(6)->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Pelaksanaan')
                ->schema([
                    Forms\Components\Toggle::make('is_applied')->label('Sudah Diterapkan'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('landGrid.grid_code')->label('Kode Grid')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('ai_model_used')->label('Model')->badge(),
            Tables\Columns\TextColumn::make('soil_condition_summary')->label('Ringkasan')->limit(60)->wrap(),
            Tables\Columns\IconColumn::make('is_applied')->label('Diterapkan')->boolean()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y, H:i')->sortable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_applied')->label('Status Pelaksanaan'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ])->defaultSort('created_at', 'desc');
    }

    /**
     * @return Builder<LandRecommendation>
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
            'index' => Pages\ListLandRecommendations::route('/'),
            'edit' => Pages\EditLandRecommendation::route('/{record}/edit'),
        ];
    }
}
