<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_combat', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Personagem
            |--------------------------------------------------------------------------
            */

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Experiência
            |--------------------------------------------------------------------------
            |
            | XP é independente do nível.
            |
            | O Mestre pode aumentar, reduzir ou definir livremente.
            |
            */

            $table->unsignedBigInteger('experience_points')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Pontos de Vida
            |--------------------------------------------------------------------------
            |
            | current_hp
            |     Vida atual.
            |
            | max_hp
            |     Vida máxima permanente/oficial.
            |
            | temporary_hp
            |     Vida temporária. É consumida antes da vida real.
            |
            | temporary_max_hp
            |     Extensão temporária da capacidade máxima.
            |
            | Exemplo:
            |
            | max_hp = 58
            | temporary_max_hp = 12
            |
            | effective_max_hp = 70
            |
            */

            $table->unsignedInteger('current_hp')
                ->default(0);

            $table->unsignedInteger('max_hp')
                ->default(0);

            $table->unsignedInteger('temporary_hp')
                ->default(0);

            $table->unsignedInteger('temporary_max_hp')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Dados de Vida
            |--------------------------------------------------------------------------
            |
            | Como o personagem pode possuir multiclasses, não devemos
            | armazenar apenas um tipo de dado.
            |
            | Exemplo:
            |
            | [
            |     {
            |         "die": "d10",
            |         "maximum": 5,
            |         "current": 4
            |     },
            |     {
            |         "die": "d6",
            |         "maximum": 3,
            |         "current": 2
            |     }
            | ]
            |
            | Isso permite:
            |
            | Guerreiro 5 = 5d10
            | Mago 3      = 3d6
            |
            | e também permite alterações manuais.
            |
            */

            $table->json('hit_dice')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Defesa
            |--------------------------------------------------------------------------
            |
            | Classe de Armadura pode ser modificada livremente.
            |
            */

            $table->integer('armor_class')
                ->default(10);

            /*
            |--------------------------------------------------------------------------
            | Movimento
            |--------------------------------------------------------------------------
            */

            $table->integer('speed')
                ->default(30);

            /*
            |--------------------------------------------------------------------------
            | Iniciativa
            |--------------------------------------------------------------------------
            |
            | Pode ser derivada da Destreza, mas fica persistida para permitir
            | modificadores e overrides.
            |
            */

            $table->integer('initiative_bonus')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Death Saves
            |--------------------------------------------------------------------------
            |
            | 0 até 3 normalmente, mas não colocamos uma regra rígida no
            | banco para manter liberdade para regras alternativas.
            |
            */

            $table->unsignedTinyInteger('death_save_successes')
                ->default(0);

            $table->unsignedTinyInteger('death_save_failures')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Concentration
            |--------------------------------------------------------------------------
            */

            $table->boolean('concentration_active')
                ->default(false);

            /*
            | ID da magia que está mantendo a concentração.
            |
            | A foreign key será criada na migration de spells, pois a tabela
            | spells ainda não existe neste momento.
            */

            $table->unsignedBigInteger('concentration_spell_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Exhaustion
            |--------------------------------------------------------------------------
            |
            | 0 = nenhuma
            |
            | O sistema não força um limite máximo aqui.
            |
            */

            $table->unsignedTinyInteger('exhaustion_level')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Condições
            |--------------------------------------------------------------------------
            |
            | Exemplos:
            |
            | [
            |     "blinded",
            |     "poisoned",
            |     "prone"
            | ]
            |
            | Também podemos posteriormente armazenar objetos completos.
            |
            */

            $table->json('conditions')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Resistências
            |--------------------------------------------------------------------------
            */

            $table->json('damage_resistances')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Imunidades
            |--------------------------------------------------------------------------
            */

            $table->json('damage_immunities')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Vulnerabilidades
            |--------------------------------------------------------------------------
            */

            $table->json('damage_vulnerabilities')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Modificadores de combate adicionais
            |--------------------------------------------------------------------------
            |
            | Guardamos overrides gerais para efeitos que ainda não possuem
            | uma coluna própria.
            |
            | Exemplo:
            |
            | {
            |     "armor_class": 2,
            |     "initiative": 1,
            |     "speed": 10
            | }
            |
            */

            $table->json('overrides')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Constraints
            |--------------------------------------------------------------------------
            |
            | Um personagem possui exatamente um estado de combate.
            |
            */

            $table->unique('character_id');

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('experience_points');

            $table->index('concentration_spell_id');

            $table->index('exhaustion_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_combat');
    }
};