<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\NewsArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
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
            ->layout('components.layouts.app', [
                'title' => $this->article->title,
                'description' => $this->metaDescription(),
                'image' => $this->socialImage(),
                'type' => 'article',
                'publishedAt' => $this->article->published_at?->toAtomString(),
                'updatedAt' => $this->article->updated_at?->toAtomString(),
                'authorName' => $this->article->author?->name,
            ]);
    }

    private function metaDescription(): string
    {
        $plainText = html_entity_decode(
            strip_tags($this->article->content),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8',
        );

        return Str::limit(Str::squish($plainText), 160, '');
    }

    private function socialImage(): ?string
    {
        $image = $this->article->getFirstMediaUrl(NewsArticle::THUMBNAIL_COLLECTION)
            ?: (string) $this->article->thumbnail_path;

        if ($image === '') {
            return null;
        }

        return Str::startsWith($image, ['http://', 'https://'])
            ? $image
            : asset(ltrim($image, '/'));
    }
}
