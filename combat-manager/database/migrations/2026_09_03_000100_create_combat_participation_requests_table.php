<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'combat_participation_requests',
            function (Blueprint $table): void {
                $table->id();
                $table->foreignId('combat_id')->constrained('combats')->cascadeOnDelete();
                $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
                $table->string('status', 20)->default('pending');
                $table->integer('initiative')->nullable();
                $table->timestamp('expires_at');
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();

                $table->unique(
                    ['combat_id', 'character_id'],
                    'combat_participation_combat_character_unique'
                );

                $table->index(
                    ['status', 'expires_at'],
                    'combat_participation_status_expiry_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('combat_participation_requests');
    }
};