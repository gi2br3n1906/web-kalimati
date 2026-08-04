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
    public const CONTENT_DRAFT_STORAGE_KEY = 'news_draft_content';

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
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Informasi & Publikasi Artikel')
                        ->schema([
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Judul Artikel')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(static function (?string $state, Set $set): void {
                                            $set('slug', Str::slug((string) $state));
                                        })
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('slug')
                                        ->label('Slug URL')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->helperText('Digunakan pada URL artikel.')
                                        ->columnSpan(1),
                                ])
                                ->columnSpanFull(),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\Select::make('category')
                                        ->label('Kategori')
                                        ->options([
                                            'kkn' => 'KKN',
                                            'karang_taruna' => 'Karang Taruna',
                                            'pemdes' => 'Pemerintah Desa',
                                        ])
                                        ->required()
                                        ->native(false)
                                        ->searchable()
                                        ->columnSpan(1),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('thumbnail')
                                        ->label('Foto Thumbnail')
                                        ->collection(NewsArticle::THUMBNAIL_COLLECTION)
                                        ->conversion('preview')
                                        ->image()
                                        ->imageEditor()
                                        ->maxSize(5 * 1024)
                                        ->columnSpan(1),
                                    Forms\Components\Group::make([
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
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpanFull(),
                    Forms\Components\Section::make('Isi Berita')
                        ->schema([
                            Forms\Components\RichEditor::make('content')
                                ->hiddenLabel()
                                ->required()
                                ->extraAttributes([
                                    'style' => 'min-height: 550px;',
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ])
                    ->extraAttributes([
                        'x-data' => '{}',
                        'x-init' => <<<'JS'
                            if (window.location.pathname.endsWith('/create')) {
                                const draftKey = 'news_draft_content';
                                const savedDraft = localStorage.getItem(draftKey);

                                if (savedDraft && confirm('Ditemukan draft isi berita yang belum tersimpan. Pulihkan draft?')) {
                                    $wire.set('data.content', savedDraft);
                                }

                                const autosaveTimer = window.setInterval(() => {
                                    const content = $wire.get('data.content');

                                    if (typeof content === 'string' && content.trim() !== '') {
                                        localStorage.setItem(draftKey, content);
                                    } else {
                                        localStorage.removeItem(draftKey);
                                    }
                                }, 5000);

                                $cleanup(() => window.clearInterval(autosaveTimer));
                            }
                            JS,
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
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
