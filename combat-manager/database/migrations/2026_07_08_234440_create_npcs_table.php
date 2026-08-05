<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('npcs', function (Blueprint $table) {

    $table->id();

    // Dono da ficha
    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();

    // Informações principais
    $table->string('name');

    $table->string('nickname')->nullable();

    $table->string('creature_type');

    $table->string('size');

    $table->string('alignment')->nullable();

    // Dados importantes para consultas
    $table->unsignedTinyInteger('armor_class');

    $table->decimal('challenge_rating', 4, 2);

    // Vida máxima calculada na importação
    $table->unsignedSmallInteger('max_hp');

    // JSON original da ficha
    $table->json('json_data');

    $table->timestamp('deceased_at')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npcs');
    }
};
