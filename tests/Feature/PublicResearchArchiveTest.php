<?php

declare(strict_types=1);

use App\Enums\ResearchCategory;
use App\Livewire\Public\ResearchHub\ArchiveIndex;
use App\Models\ResearchFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('searches and filters only public research archive files', function (): void {
    $monograph = ResearchFile::factory()->create([
        'title' => 'Monografi Kalimati 2026', 'abstract' => 'Pemetaan sosial Desa Kalimati.',
        'author_names' => 'Nadia Putri, Bima Saputra', 'category' => ResearchCategory::MONOGRAFI,
        'kkn_cohort' => 'Tim II 2026', 'is_public' => true,
    ]);
    ResearchFile::factory()->create([
        'title' => 'Analisis Tanah', 'category' => ResearchCategory::SAINTEK,
        'kkn_cohort' => 'Tim I 2025', 'is_public' => true,
    ]);
    ResearchFile::factory()->create([
        'title' => 'Dokumen Riset Privat', 'abstract' => 'Kata rahasia Kalimati.', 'is_public' => false,
    ]);

    Livewire::test(ArchiveIndex::class)
        ->assertSee($monograph->title)
        ->assertDontSee('Dokumen Riset Privat')
        ->set('search', 'Nadia Putri')
        ->assertSee($monograph->title)
        ->set('category', ResearchCategory::MONOGRAFI->value)
        ->set('cohort', 'Tim II 2026')
        ->assertSee($monograph->title)
        ->assertDontSee('Analisis Tanah');
});

it('serves public downloads but never serves private research files', function (): void {
    Storage::fake('local');
    Storage::disk('local')->put('research-files/public.pdf', '%PDF-1.4 public');
    Storage::disk('local')->put('research-files/private.pdf', '%PDF-1.4 private');
    $public = ResearchFile::factory()->create(['file_path' => 'research-files/public.pdf', 'is_public' => true]);
    $private = ResearchFile::factory()->create(['file_path' => 'research-files/private.pdf', 'is_public' => false]);

    $this->get(route('public.research.download', $public))->assertOk();
    $this->get(route('public.research.preview', $public))->assertOk();
    $this->get(route('public.research.download', $private))->assertNotFound();
    $this->get(route('public.research.preview', $private))->assertNotFound();
});
