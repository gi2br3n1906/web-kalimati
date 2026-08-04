<?php

declare(strict_types=1);

namespace App\Filament\Resources\GisPointOfInterestResource\Pages;

use App\Actions\Gis\ImportKmlLocationsAction;
use App\Enums\PoiCategory;
use App\Filament\Resources\GisPointOfInterestResource;
use App\Models\GisPointOfInterest;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class ListGisPointsOfInterest extends ListRecords
{
    protected static string $resource = GisPointOfInterestResource::class;

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_kml')
                ->label('Import KML Google Earth')
                ->authorize('create', GisPointOfInterest::class)
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import KML/KMZ Google Earth')
                ->modalDescription('Semua Placemark Point dan Polygon akan ditambahkan ke Web GIS.')
                ->modalSubmitActionLabel('Import Sekarang')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('File Google Earth')
                        ->helperText('Format KML, KMZ, atau XML. Maksimal 10 MB.')
                        ->acceptedFileTypes([
                            'application/vnd.google-earth.kml+xml',
                            'application/vnd.google-earth.kmz',
                            'application/octet-stream',
                            'application/xml',
                            'application/zip',
                            'application/x-zip-compressed',
                            'text/plain',
                            'text/xml',
                        ])
                        ->rules(['extensions:kml,kmz,xml'])
                        ->maxSize(10 * 1024)
                        ->storeFiles(false)
                        ->required(),
                    Forms\Components\Select::make('category')
                        ->label('Kategori Default')
                        ->options(PoiCategory::options())
                        ->default(PoiCategory::FASILITAS_UMUM->value)
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Gate::authorize('create', GisPointOfInterest::class);

                    $file = $data['file'] ?? null;
                    $category = PoiCategory::tryFrom((string) ($data['category'] ?? ''));

                    if (! $file instanceof TemporaryUploadedFile || $category === null) {
                        throw ValidationException::withMessages([
                            'file' => 'File atau kategori import tidak valid.',
                        ]);
                    }

                    try {
                        $count = app(ImportKmlLocationsAction::class)->execute(
                            filePath: $file->getRealPath(),
                            category: $category,
                            sourceName: $file->getClientOriginalName(),
                        );
                    } catch (InvalidArgumentException $exception) {
                        Notification::make()
                            ->title('Import KML gagal')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Import KML gagal')
                            ->body('Terjadi kesalahan saat menyimpan data. Silakan periksa file dan coba kembali.')
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Import KML berhasil')
                        ->body("{$count} lokasi/area berhasil ditambahkan ke Web GIS.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
