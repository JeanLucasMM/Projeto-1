<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('combat_players', function (Blueprint $table) {

    $table->id();

    $table->foreignId('combat_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('name');

    $table->integer('initiative')
        ->default(0);

    $table->timestamps();

});
    }

    public function down(): void
    {
        Schema::dropIfExists('combat_players');
    }
};