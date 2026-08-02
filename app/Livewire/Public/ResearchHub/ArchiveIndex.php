<?php

declare(strict_types=1);

namespace App\Livewire\Public\ResearchHub;

use App\Enums\ResearchCategory;
use App\Models\ResearchFile;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ArchiveIndex extends Component
{
    public string $search = '';

    public string $category = '';

    public string $cohort = '';

    public ?int $viewingDocumentId = null;

    public function openViewer(int $id): void
    {
        $this->viewingDocumentId = $id;
    }

    public function closeViewer(): void
    {
        $this->viewingDocumentId = null;
    }

    public function render(): View
    {
        $files = ResearchFile::query()->publiclyAccessible()->when($this->category !== '', fn ($query) => $query->where('category', $this->category))->when($this->cohort !== '', fn ($query) => $query->where('kkn_cohort', $this->cohort))->when($this->search !== '', function ($query): void {
            $query->where(fn ($searchQuery) => $searchQuery->where('title', 'like', '%'.$this->search.'%')->orWhere('abstract', 'like', '%'.$this->search.'%')->orWhere('author_names', 'like', '%'.$this->search.'%'));
        })->latest()->get();
        $viewer = $this->viewingDocumentId === null ? null : $files->firstWhere('id', $this->viewingDocumentId);

        return view('livewire.public.research-hub.archive-index', ['files' => $files, 'viewer' => $viewer, 'categories' => ResearchCategory::options(), 'cohorts' => ResearchFile::query()->publiclyAccessible()->distinct()->orderBy('kkn_cohort')->pluck('kkn_cohort')->all()])->layout('components.layouts.app', ['title' => 'Arsip Riset KKN']);
    }
}
