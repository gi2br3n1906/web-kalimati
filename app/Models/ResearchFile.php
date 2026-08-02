<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResearchCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchFile extends Model
{
    use HasFactory;

    protected $fillable = ['uploader_id', 'title', 'kkn_cohort', 'category', 'author_names', 'file_path', 'file_size_kb', 'abstract', 'is_public'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['category' => ResearchCategory::class, 'is_public' => 'boolean', 'file_size_kb' => 'integer'];
    }

    /** @param Builder<ResearchFile> $query @return Builder<ResearchFile> */
    public function scopePubliclyAccessible(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /** @return BelongsTo<User, $this> */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    protected function humanReadableFileSize(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->file_size_kb < 1024) {
                return $this->file_size_kb.' KB';
            }

            return number_format($this->file_size_kb / 1024, 1, ',', '.').' MB';
        });
    }

    protected function isPdf(): Attribute
    {
        return Attribute::get(fn (): bool => str_ends_with(strtolower($this->file_path), '.pdf'));
    }
}
