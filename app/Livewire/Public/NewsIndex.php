<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Support\PublicNewsCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class NewsIndex extends Component
{
    public string $search = '';

    public string $category = '';

    public function render(): View
    {
        $articles = PublicNewsCatalog::articles()
            ->when($this->category !== '', fn ($items) => $items->where('category', $this->category))
            ->when($this->search !== '', fn ($items) => $items->filter(fn (array $article): bool => Str::contains(Str::lower($article['title'].' '.$article['excerpt']), Str::lower($this->search))))
            ->values();

        return view('livewire.public.news-index', ['articles' => $articles, 'categories' => PublicNewsCatalog::categories()])
            ->layout('components.layouts.app', ['title' => 'Kabar & Publikasi Desa']);
    }
}
