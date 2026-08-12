<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\NewsCategory;
use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class NewsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $category = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);

        $articles = NewsArticle::query()
            ->with(['author', 'media'])
            ->published()
            ->when(
                $this->category !== '',
                fn (Builder $query): Builder => $query->where('category', $this->category),
            )
            ->when(
                $search !== '',
                fn (Builder $query): Builder => $query->where(static function (Builder $query) use ($search): void {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                }),
            )
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.public.news-index', [
            'articles' => $articles,
            'categories' => NewsCategory::options(),
        ])
            ->layout('components.layouts.app', ['title' => 'Kabar & Publikasi Desa']);
    }
}
