<?php

declare(strict_types=1);

use App\Enums\AiConditionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('iot_device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('iot_telemetry_id')->constrained()->cascadeOnDelete();
            $table->enum('condition_status', array_column(AiConditionStatus::cases(), 'value'));
            $table->string('action_title');
            $table->text('recommendation_text');
            $table->timestamps();

            $table->index(['iot_device_id', 'created_at']);
            $table->index(['condition_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_recommendations');
    }
};
