<?php

declare(strict_types=1);

use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Public\AgricultureIndex;
use App\Livewire\Public\GisInteractiveMap;
use App\Livewire\Public\LandingIndex;
use App\Livewire\Public\NewsIndex;
use App\Livewire\Public\NewsShow;
use App\Livewire\Public\ProfileIndex;
use App\Livewire\Public\SyncGps;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('seo.sitemap');
Route::get('/robots.txt', RobotsController::class)->name('seo.robots');

Route::get('/', LandingIndex::class)->name('public.home');
Route::get('/profil', ProfileIndex::class)->name('public.profile');
Route::get('/pertanian', AgricultureIndex::class)->name('public.agriculture');
Route::get('/peta', GisInteractiveMap::class)->name('public.gis.map');
Route::get('/sync-gps', SyncGps::class)->name('public.gps.sync');
Route::get('/berita', NewsIndex::class)->name('public.news.index');
Route::get('/berita/{slug}', NewsShow::class)->name('public.news.show');
