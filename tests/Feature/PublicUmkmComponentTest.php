<?php

declare(strict_types=1);

use App\Enums\LedgerType;
use App\Enums\RoleType;
use App\Enums\UmkmCategory;
use App\Livewire\Public\Umkm\DirectoryIndex;
use App\Livewire\Public\Umkm\LedgerCalculator;
use App\Models\UmkmBusiness;
use App\Models\UmkmLedger;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

it('filters the public umkm directory and builds a normalized whatsapp cta', function (): void {
    $foodBusiness = UmkmBusiness::factory()->create([
        'business_name' => 'Pisang Lestari',
        'category' => UmkmCategory::KULINER,
        'phone_number' => '081234567890',
        'address' => 'Dusun Dampit',
    ]);
    UmkmBusiness::factory()->create([
        'business_name' => 'Bengkel Jaya',
        'category' => UmkmCategory::JASA,
        'address' => 'Dusun Brojo',
    ]);

    Livewire::test(DirectoryIndex::class)
        ->assertSee('Pisang Lestari')
        ->assertSee('Bengkel Jaya')
        ->set('category', UmkmCategory::KULINER->value)
        ->assertSee('Pisang Lestari')
        ->assertDontSee('Bengkel Jaya')
        ->set('search', 'Dampit')
        ->assertSee('Pisang Lestari')
        ->assertSee($foodBusiness->whatsapp_url);
});

it('shows cash flow only for the authenticated umkm owners business', function (): void {
    $this->seed(DatabaseSeeder::class);
    $owner = User::factory()->withRole(RoleType::UMKM)->create();
    $owner->assignRole(Role::findByName(RoleType::UMKM->value, 'web'));
    $business = UmkmBusiness::factory()->for($owner, 'owner')->create(['business_name' => 'Warung Milik Saya']);
    $otherBusiness = UmkmBusiness::factory()->create(['business_name' => 'Usaha Bukan Milik Saya']);
    UmkmLedger::factory()->for($business, 'business')->create(['type' => LedgerType::INCOME, 'amount' => 400_000]);
    UmkmLedger::factory()->for($business, 'business')->create(['type' => LedgerType::EXPENSE, 'amount' => 125_000]);
    UmkmLedger::factory()->for($otherBusiness, 'business')->create(['type' => LedgerType::INCOME, 'amount' => 1_000_000]);

    $this->actingAs($owner);

    Livewire::test(LedgerCalculator::class)
        ->assertSee('Warung Milik Saya')
        ->assertDontSee('Usaha Bukan Milik Saya')
        ->assertSee('Rp 400.000')
        ->assertSee('Rp 125.000')
        ->assertSee('Rp 275.000');
});
