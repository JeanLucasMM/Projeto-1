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
Schema::create('combat_npcs', function (Blueprint $table) {

    $table->id();

    $table->foreignId('combat_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('npc_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->integer('initiative')
        ->default(0);

    $table->integer('current_hp');

$table->unsignedInteger('temporary_hp')
    ->default(0);

$table->integer('max_hp');


    $table->boolean('is_dead')
        ->default(false);

    $table->json('resource_trackers')
    ->nullable();
    
    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combat_npcs');
    }
};
