<?php

declare(strict_types=1);

use App\Enums\LedgerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_ledgers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('umkm_business_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date');
            $table->enum('type', array_column(LedgerType::cases(), 'value'));
            $table->decimal('amount', 12, 2);
            $table->string('category', 100);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['umkm_business_id', 'transaction_date']);
            $table->index(['type', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_ledgers');
    }
};
