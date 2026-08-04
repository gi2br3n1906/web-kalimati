<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Support\PublicNewsCatalog;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NewsShow extends Component
{
    /** @var array<string, string> */
    public array $article = [];

    public function mount(string $slug): void
    {
        $this->article = PublicNewsCatalog::articles()->firstWhere('slug', $slug) ?? abort(404);
    }

    public function render(): View
    {
        $related = PublicNewsCatalog::articles()->where('category', $this->article['category'])->where('slug', '!=', $this->article['slug'])->take(3);

        return view('livewire.public.news-show', ['related' => $related])
            ->layout('components.layouts.app', ['title' => $this->article['title']]);
    }
}
