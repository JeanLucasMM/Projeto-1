<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'character_transformations',
            function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Personagem dono
                |--------------------------------------------------------------------------
                */
                $table->foreignId('character_id')
                    ->constrained('characters')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Identidade da transformação
                |--------------------------------------------------------------------------
                */
                $table->string('name', 120);

                /*
                 * character_sheet = Nova Ficha
                 * monster_sheet   = Ficha de Monstro
                 */
                $table->string('type', 30);

                /*
                 * Nova Ficha:
                 * aponta para um Character auxiliar marcado com
                 * is_transformation_form = true.
                 */
                $table->foreignId('form_character_id')
                    ->nullable()
                    ->constrained('characters')
                    ->cascadeOnDelete();

                /*
                 * Ficha de Monstro:
                 * aponta para uma criatura existente no Cofre.
                 *
                 * A criatura é somente a fonte/template. O estado temporário
                 * da forma ativa será salvo em character_transformation_states.
                 */
                $table->foreignId('source_npc_id')
                    ->nullable()
                    ->constrained('npcs')
                    ->nullOnDelete();

                /*
                 * Foto alternativa da forma.
                 * Se null, a interface usa a foto do Character principal.
                 */
                $table->string('image_path')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Ativação
                |--------------------------------------------------------------------------
                |
                | none         = Nenhum
                | spell        = Feitiço (somente categoria visual)
                | bonus_action = Ação Bônus
                | action       = Ação Padrão
                |
                | "spell" NÃO possui vínculo com Grimório/Spell.
                |
                */
                $table->string('casting_time', 30)
                    ->default('none');

                /*
                 * Concentração própria do sistema de transformação.
                 * Não usa concentration_spell_id.
                 */
                $table->boolean('requires_concentration')
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | Duração
                |--------------------------------------------------------------------------
                |
                | indefinite = até ser encerrada
                | rounds     = rodadas
                | minutes    = minutos
                | hours      = horas
                |
                */
                $table->string('duration_type', 30)
                    ->default('indefinite');

                $table->unsignedInteger('duration_value')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Transformação especial / automática
                |--------------------------------------------------------------------------
                |
                | half_hp        = ao cruzar metade dos PV
                | one_or_zero_hp = ao chegar a 1 ou 0 PV
                |
                */
                $table->boolean('is_special')
                    ->default(false);

                $table->string('special_trigger', 40)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Usos
                |--------------------------------------------------------------------------
                |
                | uses_max = null significa ilimitado.
                |
                | recovery:
                | daily      = por dia
                | short_rest = por descanso curto
                |
                */
                $table->unsignedSmallInteger('uses_max')
                    ->nullable();

                $table->unsignedSmallInteger('uses_current')
                    ->nullable();

                $table->string('recovery', 30)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Consequência / Exaustão
                |--------------------------------------------------------------------------
                |
                | exhaustion_timing:
                | activation = ao ativar
                | end        = ao voltar à forma normal
                |
                */
                $table->boolean('applies_exhaustion')
                    ->default(false);

                $table->unsignedTinyInteger('exhaustion_levels')
                    ->default(0);

                $table->string('exhaustion_timing', 30)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Organização e expansão futura
                |--------------------------------------------------------------------------
                */
                $table->unsignedInteger('sort_order')
                    ->default(0);

                $table->json('settings')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'character_id',
                    'sort_order',
                ]);

                $table->index([
                    'character_id',
                    'type',
                ]);

                /*
                 * Uma ficha auxiliar só pode representar uma transformação.
                 * Valores NULL continuam permitidos para Ficha de Monstro.
                 */
                $table->unique('form_character_id');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'character_transformations'
        );
    }
};