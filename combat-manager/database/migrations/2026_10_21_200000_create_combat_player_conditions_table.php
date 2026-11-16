<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('combat_player_conditions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combat_id')->constrained('combats')->cascadeOnDelete();
            $table->foreignId('combat_player_id')->constrained('combat_players')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('condition_key', 32);
            $table->string('source', 120)->nullable();
            $table->unsignedInteger('applied_round')->default(0);
            $table->unsignedInteger('elapsed_turns')->default(0);
            $table->unsignedInteger('duration_turns')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['combat_player_id', 'condition_key'], 'combat_player_condition_unique');
            $table->index(['character_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combat_player_conditions');
    }
};
