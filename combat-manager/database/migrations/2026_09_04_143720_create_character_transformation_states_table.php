<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'character_transformation_states',
            function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Personagem
                |--------------------------------------------------------------------------
                |
                | Existe no máximo um estado de transformação por personagem.
                |
                */
                $table->foreignId('character_id')
                    ->unique();

                $table->foreign(
                    'character_id',
                    'cts_character_fk'
                )
                    ->references('id')
                    ->on('characters')
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | Transformação ativa
                |--------------------------------------------------------------------------
                |
                | Nome curto da constraint porque o MySQL limita identificadores
                | a 64 caracteres.
                |
                */
                $table->foreignId(
                    'character_transformation_id'
                )
                    ->nullable();

                $table->foreign(
                    'character_transformation_id',
                    'cts_transformation_fk'
                )
                    ->references('id')
                    ->on('character_transformations')
                    ->nullOnDelete();

                $table->boolean('is_active')
                    ->default(false);


                /*
                |--------------------------------------------------------------------------
                | Momento de ativação
                |--------------------------------------------------------------------------
                */
                $table->timestamp('activated_at')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | Combate / iniciativa
                |--------------------------------------------------------------------------
                |
                | Quando a transformação é iniciada durante um combate,
                | registramos o combate, rodada e posição da iniciativa.
                |
                */
                $table->foreignId('combat_id')
                    ->nullable();

                $table->foreign(
                    'combat_id',
                    'cts_combat_fk'
                )
                    ->references('id')
                    ->on('combats')
                    ->nullOnDelete();

                $table->unsignedInteger('activated_round')
                    ->nullable();

                $table->unsignedInteger('activated_turn')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | Duração restante
                |--------------------------------------------------------------------------
                |
                | O significado depende de duration_type da transformação:
                | rodadas, minutos ou horas.
                |
                */
                $table->unsignedInteger('duration_remaining')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | Estado temporário da Ficha de Monstro
                |--------------------------------------------------------------------------
                |
                | O NPC do Cofre nunca é alterado. PV, recursos, condições e
                | demais valores mutáveis da forma ficam neste JSON.
                |
                */
                $table->json('monster_state')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | Estado de execução
                |--------------------------------------------------------------------------
                |
                | Espaço para concentração da transformação, gatilhos já
                | processados e futuras extensões que não pertencem ao template.
                |
                */
                $table->json('runtime_state')
                    ->nullable();

                $table->timestamps();


                /*
                |--------------------------------------------------------------------------
                | Índices
                |--------------------------------------------------------------------------
                */
                $table->index(
                    [
                        'is_active',
                        'character_transformation_id',
                    ],
                    'cts_active_transformation_idx'
                );

                $table->index(
                    [
                        'combat_id',
                        'activated_round',
                    ],
                    'cts_combat_round_idx'
                );
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'character_transformation_states'
        );
    }
};