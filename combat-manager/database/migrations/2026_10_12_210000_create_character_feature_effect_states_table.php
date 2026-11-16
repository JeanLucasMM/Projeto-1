<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('character_feature_effect_states', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('character_feature_id')->constrained('character_features')->cascadeOnDelete();
            $table->boolean('is_active')->default(false);
            $table->boolean('requires_concentration')->default(false);
            $table->unsignedInteger('duration_total_rounds')->nullable();
            $table->unsignedInteger('duration_remaining')->nullable();
            $table->string('roll_expression', 40)->nullable();
            $table->unsignedInteger('rolled_total')->nullable();
            $table->foreignId('combat_id')->nullable()->constrained('combats')->nullOnDelete();
            $table->unsignedInteger('activated_round')->nullable();
            $table->unsignedInteger('activated_turn')->nullable();
            $table->unsignedInteger('last_processed_round')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
            $table->unique('character_feature_id');
            $table->index(['character_id', 'is_active']);
            $table->index(['combat_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_feature_effect_states');
    }
};
