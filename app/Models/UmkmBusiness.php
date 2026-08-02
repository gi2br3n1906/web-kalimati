<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UmkmCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UmkmBusiness extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'business_name',
        'category',
        'description',
        'phone_number',
        'logo_path',
        'address',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => UmkmCategory::class,
        ];
    }

    /**
     * @param  Builder<UmkmBusiness>  $query
     * @return Builder<UmkmBusiness>
     */
    public function scopeForOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<UmkmLedger, $this>
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(UmkmLedger::class);
    }

    protected function whatsappUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $phone = preg_replace('/\D+/', '', $this->phone_number) ?? '';

            if (str_starts_with($phone, '0')) {
                $phone = '62'.substr($phone, 1);
            }

            return sprintf(
                'https://wa.me/%s?text=%s',
                $phone,
                rawurlencode(sprintf('Halo, saya ingin mengetahui produk dari %s.', $this->business_name)),
            );
        });
    }
}
