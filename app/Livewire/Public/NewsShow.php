<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NewsShow extends Component
{
    public NewsArticle $article;

    public function mount(string $slug): void
    {
        $this->article = NewsArticle::query()
            ->with(['author', 'media'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function render(): View
    {
        $related = NewsArticle::query()
            ->published()
            ->where('category', $this->article->category)
            ->whereKeyNot($this->article->getKey())
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('livewire.public.news-show', ['related' => $related])
            ->layout('components.layouts.app', ['title' => $this->article->title]);
    }
}
