<?php

declare(strict_types=1);

use App\Http\Controllers\PublicResearchFileController;
use App\Livewire\Public\AgricultureIndex;
use App\Livewire\Public\GisInteractiveMap;
use App\Livewire\Public\LandingIndex;
use App\Livewire\Public\ProfileIndex;
use App\Livewire\Public\ResearchHub\ArchiveIndex;
use App\Livewire\Public\Umkm\DirectoryIndex;
use App\Livewire\Public\Umkm\LedgerCalculator;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingIndex::class)->name('public.home');
Route::get('/profil', ProfileIndex::class)->name('public.profile');
Route::get('/pertanian', AgricultureIndex::class)->name('public.agriculture');
Route::get('/peta', GisInteractiveMap::class)->name('public.gis.map');
Route::get('/umkm', DirectoryIndex::class)->name('public.umkm.directory');
Route::get('/umkm/kas', LedgerCalculator::class)->name('public.umkm.ledger');
Route::get('/riset', ArchiveIndex::class)->name('public.research.archive');
Route::get('/riset/{researchFile}/unduh', [PublicResearchFileController::class, 'download'])->name('public.research.download');
Route::get('/riset/{researchFile}/preview', [PublicResearchFileController::class, 'preview'])->name('public.research.preview');
