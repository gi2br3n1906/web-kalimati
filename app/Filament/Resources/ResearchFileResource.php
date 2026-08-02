<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ResearchCategory;
use App\Filament\Resources\ResearchFileResource\Pages;
use App\Models\ResearchFile;
use App\Support\ResearchFileUploadRules;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResearchFileResource extends Resource
{
    protected static ?string $model = ResearchFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationGroup = 'Research Hub';

    protected static ?string $modelLabel = 'Dokumen Riset';

    protected static ?string $pluralModelLabel = 'Arsip Riset KKN';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Arsip Dokumen')->schema([
                Forms\Components\TextInput::make('title')->label('Judul')->required()->maxLength(255),
                Forms\Components\TextInput::make('kkn_cohort')->label('Angkatan KKN')->required()->maxLength(50)->placeholder('Tim II 2026'),
                Forms\Components\Select::make('category')->label('Kategori')->options(ResearchCategory::options())->native(false)->required(),
                Forms\Components\Textarea::make('author_names')->label('Nama Penulis')->required()->rows(2)->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')->label('File Dokumen')->disk('local')->visibility('private')->directory('research-files')
                    ->acceptedFileTypes(ResearchFileUploadRules::MIME_TYPES)->maxSize(ResearchFileUploadRules::MAX_SIZE_KB)->rules(ResearchFileUploadRules::rules())->required(),
                Forms\Components\Textarea::make('abstract')->label('Abstrak')->required()->rows(6)->columnSpanFull(),
                Forms\Components\Toggle::make('is_public')->label('Tampilkan ke Publik')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable()->wrap(),
            Tables\Columns\TextColumn::make('category')->label('Kategori')->formatStateUsing(static fn (ResearchCategory $state): string => $state->label())->badge(),
            Tables\Columns\TextColumn::make('kkn_cohort')->label('Angkatan')->sortable(),
            Tables\Columns\TextColumn::make('human_readable_file_size')->label('Ukuran'),
            Tables\Columns\IconColumn::make('is_public')->label('Publik')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->label('Diunggah')->date('d M Y')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('category')->label('Kategori')->options(ResearchCategory::options()),
            Tables\Filters\SelectFilter::make('kkn_cohort')->label('Angkatan')->options(static fn (): array => ResearchFile::query()->distinct()->orderBy('kkn_cohort')->pluck('kkn_cohort', 'kkn_cohort')->all()),
            Tables\Filters\TernaryFilter::make('is_public')->label('Status Publik'),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])->defaultSort('created_at', 'desc');
    }

    /** @return array<string, RelationManagerConfiguration> */
    public static function getRelations(): array
    {
        return [];
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListResearchFiles::route('/'), 'create' => Pages\CreateResearchFile::route('/create'), 'edit' => Pages\EditResearchFile::route('/{record}/edit')];
    }
}
