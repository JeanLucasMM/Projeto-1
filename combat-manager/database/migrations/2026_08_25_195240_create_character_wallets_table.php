<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('copper')->default(0);
            $table->unsignedBigInteger('silver')->default(0);
            $table->unsignedBigInteger('electrum')->default(0);
            $table->unsignedBigInteger('gold')->default(0);
            $table->unsignedBigInteger('platinum')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_wallets');
    }
};