<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LedgerType;
use App\Models\UmkmBusiness;
use App\Models\UmkmLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UmkmLedger>
 */
class UmkmLedgerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'umkm_business_id' => UmkmBusiness::factory(),
            'transaction_date' => fake()->dateTimeBetween('-30 days'),
            'type' => fake()->randomElement(LedgerType::cases()),
            'amount' => fake()->randomFloat(2, 10_000, 500_000),
            'category' => fake()->randomElement(['Penjualan Harian', 'Bahan Baku', 'Operasional']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
