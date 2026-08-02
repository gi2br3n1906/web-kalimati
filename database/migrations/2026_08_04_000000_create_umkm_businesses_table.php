<?php

declare(strict_types=1);

use App\Enums\UmkmCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_businesses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('business_name');
            $table->enum('category', array_column(UmkmCategory::cases(), 'value'));
            $table->text('description')->nullable();
            $table->string('phone_number', 20);
            $table->string('logo_path')->nullable();
            $table->text('address');
            $table->timestamps();

            $table->index(['category', 'business_name']);
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_businesses');
    }
};
