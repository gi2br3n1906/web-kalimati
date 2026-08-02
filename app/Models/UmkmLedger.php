<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LedgerType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmkmLedger extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'umkm_business_id',
        'transaction_date',
        'type',
        'amount',
        'category',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'immutable_date',
            'type' => LedgerType::class,
            'amount' => 'decimal:2',
        ];
    }

    /**
     * @param  Builder<UmkmLedger>  $query
     * @return Builder<UmkmLedger>
     */
    public function scopeWithinDateRange(Builder $query, ?string $from = null, ?string $until = null): Builder
    {
        return $query
            ->when($from !== null, static fn (Builder $builder): Builder => $builder->whereDate('transaction_date', '>=', $from))
            ->when($until !== null, static fn (Builder $builder): Builder => $builder->whereDate('transaction_date', '<=', $until));
    }

    /**
     * @return BelongsTo<UmkmBusiness, $this>
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(UmkmBusiness::class, 'umkm_business_id');
    }

    protected function formattedAmount(): Attribute
    {
        return Attribute::get(
            fn (mixed $value, array $attributes): string => 'Rp '.number_format((float) $attributes['amount'], 0, ',', '.'),
        );
    }
}
