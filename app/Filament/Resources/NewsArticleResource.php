<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\NewsCategory;
use App\Filament\Resources\NewsArticleResource\Pages;
use App\Models\NewsArticle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsArticleResource extends Resource
{
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
                Section::make('Informasi & Publikasi Artikel')
                    ->description('Atur judul, kategori, thumbnail, dan status terbit artikel.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Artikel')
                            ->placeholder('Masukkan judul berita...')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(static function (?string $state, Set $set): void {
                                $set('slug', Str::slug((string) $state));
                            })
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Otomatis terisi dari judul.')
                                    ->columnSpan(1),
                                Select::make('category')
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
                            ]),
                        Grid::make(3)
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->label('Foto Thumbnail')
                                    ->collection(NewsArticle::THUMBNAIL_COLLECTION)
                                    ->conversion('preview')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5 * 1024)
                                    ->columnSpan(1),
                                Toggle::make('is_published')
                                    ->label('Terbitkan Langsung')
                                    ->default(true)
                                    ->columnSpan(1),
                                DateTimePicker::make('published_at')
                                    ->label('Waktu Terbit')
                                    ->seconds(false)
                                    ->default(now())
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make('Isi Berita')
                    ->schema([
                        RichEditor::make('content')
                            ->hiddenLabel()
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                                'attachFiles',
                            ])
                            ->extraAttributes([
                                'class' => '[&_.trix-content]:min-h-[450px] [&_.trix-content]:bg-white [&_.trix-content]:p-4 [&_.trix-content]:border [&_.trix-content]:border-gray-300 [&_.trix-content]:rounded-lg',
                            ]),
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
