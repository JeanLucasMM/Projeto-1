<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_attacks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identificação
            |--------------------------------------------------------------------------
            |
            | Esta tabela guarda SOMENTE ataques personalizados.
            | Armas de character_items serão lidas diretamente do inventário.
            | Ataque desarmado será gerado virtualmente.
            |
            */

            $table->string('name', 120);

            $table->string('effect', 255)
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Acerto
            |--------------------------------------------------------------------------
            */

            $table->string('attack_ability', 20)
                ->nullable();

            $table->boolean('use_proficiency')
                ->default(true);

            $table->integer('attack_bonus')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Dano
            |--------------------------------------------------------------------------
            |
            | damage é a expressão base: 1d8, 2d6, 1d10+1d6 etc.
            |
            | damage_abilities permite um ou vários atributos:
            | ["strength", "charisma"]
            |
            */

            $table->string('damage', 100)
                ->nullable();

            $table->string('damage_type', 50)
                ->nullable();

            $table->json('damage_abilities')
                ->nullable();

            $table->integer('damage_bonus')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Utilizações / Contador
            |--------------------------------------------------------------------------
            |
            | Se uses_max for NULL, o ataque não possui contador.
            |
            | recovery:
            | null
            | none
            | short_rest
            | long_rest
            |
            */

            $table->unsignedInteger('uses_current')
                ->nullable();

            $table->unsignedInteger('uses_max')
                ->nullable();

            $table->string('recovery', 50)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Organização
            |--------------------------------------------------------------------------
            */

            $table->boolean('visible')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Extensibilidade
            |--------------------------------------------------------------------------
            */

            $table->json('data')
                ->nullable();

            $table->timestamps();

            $table->index([
                'character_id',
                'visible',
            ]);

            $table->index([
                'character_id',
                'sort_order',
            ]);

            $table->index([
                'character_id',
                'recovery',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_attacks');
    }
};