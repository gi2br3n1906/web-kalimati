<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NewsCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class NewsArticle extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const THUMBNAIL_COLLECTION = 'thumbnail';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'category',
        'content',
        'thumbnail_path',
        'is_published',
        'published_at',
    ];

    protected static function booted(): void
    {
        static::saving(static function (NewsArticle $article): void {
            if (blank($article->slug)) {
                $article->slug = static::uniqueSlugFor(
                    title: $article->title,
                    ignoredId: $article->exists ? (int) $article->getKey() : null,
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => NewsCategory::class,
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::THUMBNAIL_COLLECTION)
            ->useDisk('public')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
            ])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit('crop', 640, 360)
            ->nonQueued();
    }

    /**
     * @param  Builder<NewsArticle>  $query
     * @return Builder<NewsArticle>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    private static function uniqueSlugFor(string $title, ?int $ignoredId = null): string
    {
        $baseSlug = Str::slug($title);
        $baseSlug = $baseSlug !== '' ? mb_substr($baseSlug, 0, 240) : 'berita';
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()
            ->when(
                $ignoredId !== null,
                static fn (Builder $query): Builder => $query->whereKeyNot($ignoredId),
            )
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
