<?php

declare(strict_types=1);

use App\Livewire\Public\AgricultureIndex;
use App\Livewire\Public\GisInteractiveMap;
use App\Livewire\Public\LandingIndex;
use App\Livewire\Public\NewsIndex;
use App\Livewire\Public\NewsShow;
use App\Livewire\Public\ProfileIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingIndex::class)->name('public.home');
Route::get('/profil', ProfileIndex::class)->name('public.profile');
Route::get('/pertanian', AgricultureIndex::class)->name('public.agriculture');
Route::get('/peta', GisInteractiveMap::class)->name('public.gis.map');
Route::get('/berita', NewsIndex::class)->name('public.news.index');
Route::get('/berita/{slug}', NewsShow::class)->name('public.news.show');
