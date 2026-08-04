<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\NewsCategory;
use App\Filament\Resources\NewsArticleResource\Pages;
use App\Models\NewsArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsArticleResource extends Resource
{
    public const CONTENT_DRAFT_STORAGE_KEY = 'filament.news-articles.create.content-draft';

    protected static ?string $model = NewsArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Informasi Publik';

    protected static ?string $modelLabel = 'Berita';

    protected static ?string $pluralModelLabel = 'Berita & Pengumuman';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Artikel Berita')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(static function (?string $state, Set $set): void {
                                $set('slug', Str::slug((string) $state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Digunakan pada URL artikel.'),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'kkn' => 'KKN',
                                'karang_taruna' => 'Karang Taruna',
                                'pemdes' => 'Pemerintah Desa',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Berita')
                            ->required()
                            ->extraAttributes([
                                'style' => 'min-height: 450px;',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->extraAttributes([
                        'x-data' => '{ autosaveTimer: null, destroy() { if (this.autosaveTimer !== null) window.clearInterval(this.autosaveTimer) } }',
                        'x-init' => <<<'JS'
                             if (window.location.pathname.endsWith('/create')) {
                                 const draftKey = 'filament.news-articles.create.content-draft';
                                 const savedDraft = localStorage.getItem(draftKey);

                                 if (savedDraft && confirm('Ditemukan draft isi berita yang belum tersimpan. Pulihkan draft?')) {
                                     $wire.set('data.content', savedDraft);
                                 }

                                autosaveTimer = window.setInterval(() => {
                                     const content = $wire.get('data.content');

                                     if (typeof content === 'string' && content.trim() !== '') {
                                         localStorage.setItem(draftKey, content);
                                     } else {
                                         localStorage.removeItem(draftKey);
                                     }
                                 }, 5000);
                             }
                             JS,
                    ])
                    ->columnSpan(2),
                Forms\Components\Section::make('Publikasi')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->collection(NewsArticle::THUMBNAIL_COLLECTION)
                            ->conversion('preview')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5 * 1024),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Terbitkan')
                            ->live()
                            ->default(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Waktu Terbit')
                            ->seconds(false)
                            ->required(static fn (Get $get): bool => (bool) $get('is_published')),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->collection(NewsArticle::THUMBNAIL_COLLECTION)
                    ->conversion('preview')
                    ->height(48)
                    ->width(80),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->formatStateUsing(static fn (NewsCategory $state): string => $state->label())
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Waktu Terbit')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum dijadwalkan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(NewsCategory::options()),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Terbit'),
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
            ->defaultSort('published_at', 'desc');
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
            'index' => Pages\ListNewsArticles::route('/'),
            'create' => Pages\CreateNewsArticle::route('/create'),
            'edit' => Pages\EditNewsArticle::route('/{record}/edit'),
        ];
    }
}
