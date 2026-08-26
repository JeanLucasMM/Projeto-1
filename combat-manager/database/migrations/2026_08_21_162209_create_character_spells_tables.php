<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CATÁLOGO GLOBAL DE MAGIAS
        |--------------------------------------------------------------------------
        */

        Schema::create('spells', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identificação
            |--------------------------------------------------------------------------
            */

            $table->string('name', 120);

            /*
            | 0 = Truque / Cantrip
            | 1+ = nível da magia
            */

            $table->unsignedTinyInteger('level')
                ->default(0);

            $table->string('school', 50);

            /*
            |--------------------------------------------------------------------------
            | Execução
            |--------------------------------------------------------------------------
            */

            $table->string('casting_time', 100);

            $table->string('range', 100);

            /*
            | Exemplo:
            |
            | {
            |     "verbal": true,
            |     "somatic": true,
            |     "material": false
            | }
            |
            */

            $table->json('components')
                ->nullable();

            $table->string('duration', 100);

            /*
            |--------------------------------------------------------------------------
            | Regras
            |--------------------------------------------------------------------------
            */

            $table->boolean('ritual')
                ->default(false);

            $table->boolean('concentration')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Descrição
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Efeitos
            |--------------------------------------------------------------------------
            |
            | Mantemos estruturado para permitir:
            |
            | - dano
            | - cura
            | - múltiplos danos
            | - condições
            | - duração
            | - áreas
            | - efeitos alternativos
            | - escalonamento
            |
            */

            $table->json('effects')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Salvaguarda
            |--------------------------------------------------------------------------
            */

            $table->string('saving_throw', 30)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Ataque mágico
            |--------------------------------------------------------------------------
            |
            | Exemplos:
            |
            | melee
            | ranged
            | null
            |
            */

            $table->string('attack_type', 30)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Escalonamento
            |--------------------------------------------------------------------------
            |
            | Informações de upcasting.
            |
            | Exemplo:
            |
            | {
            |     "damage": "1d6",
            |     "every": 1
            | }
            |
            */

            $table->json('upcast')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->string('source', 100)
                ->nullable();

            /*
            | Permite armazenar identificadores externos ou internos.
            |
            | Exemplo:
            |
            | "fireball"
            | "phb_2014_fireball"
            |
            */

            $table->string('slug', 150)
                ->nullable();

            /*
            | Permite indicar se a magia está disponível para o sistema.
            | Não precisamos apagar uma magia do catálogo para desativá-la.
            */

            $table->boolean('active')
                ->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('level');
            $table->index('school');
            $table->index('concentration');
            $table->index('ritual');
            $table->index('active');

            /*
            | Não usamos unique(name).
            |
            | Duas fontes diferentes podem possuir magias com o mesmo nome.
            | O source + slug permite diferenciá-las.
            */

            $table->index([
                'name',
                'source',
            ]);

            $table->unique([
                'slug',
                'source',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | CONFIGURAÇÃO DE CONJURAÇÃO DO PERSONAGEM
        |--------------------------------------------------------------------------
        |
        | Um personagem multiclass pode possuir múltiplas configurações.
        |
        | Exemplo:
        |
        | Mago 5
        | Clérigo 3
        |
        */

        Schema::create('character_spellcasting', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Classe responsável pela conjuração
            |--------------------------------------------------------------------------
            */

            $table->string('class', 80);

            /*
            |--------------------------------------------------------------------------
            | Atributo de conjuração
            |--------------------------------------------------------------------------
            |
            | intelligence
            | wisdom
            | charisma
            |
            */

            $table->string('ability', 30);

            /*
            |--------------------------------------------------------------------------
            | Overrides
            |--------------------------------------------------------------------------
            |
            | Quando NULL, o valor deve ser calculado normalmente.
            |
            */

            $table->integer('spell_save_dc_override')
                ->nullable();

            $table->integer('spell_attack_bonus_override')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'character_id',
                'class',
            ]);

            $table->index([
                'character_id',
                'ability',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | MAGIAS DO PERSONAGEM
        |--------------------------------------------------------------------------
        |
        | Relação entre personagem e catálogo global.
        |
        */

        Schema::create('character_spells', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Origem
            |--------------------------------------------------------------------------
            |
            | Exemplos:
            |
            | class
            | species
            | feat
            | item
            | background
            | custom
            |
            */

            $table->string('source_type', 50)
                ->nullable();

            /*
            | Classe que concedeu a magia.
            */

            $table->string('source_class', 80)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->boolean('known')
                ->default(true);

            $table->boolean('prepared')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Overrides
            |--------------------------------------------------------------------------
            |
            | Permite alterar a magia somente para este personagem.
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
            | Um personagem não possui a mesma magia duas vezes.
            |
            | Se futuramente houver necessidade de representar a mesma magia
            | proveniente de fontes diferentes, isso poderá ser alterado.
            */

            $table->unique([
                'character_id',
                'spell_id',
            ]);

            $table->index([
                'character_id',
                'prepared',
            ]);

            $table->index([
                'character_id',
                'source_class',
            ]);

            $table->index([
                'character_id',
                'source_type',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | SPELL SLOTS
        |--------------------------------------------------------------------------
        |
        | Guarda o estado atual dos slots.
        |
        | spell = slots normais
        | pact  = Pact Magic
        |
        */

        Schema::create('character_spell_slots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Tipo
            |--------------------------------------------------------------------------
            */

            $table->string('slot_type', 30)
                ->default('spell');

            /*
            |--------------------------------------------------------------------------
            | Nível
            |--------------------------------------------------------------------------
            |
            | 1 = primeiro nível
            | 2 = segundo nível
            | ...
            |
            */

            $table->unsignedTinyInteger('slot_level');

            /*
            |--------------------------------------------------------------------------
            | Quantidade
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('current')
                ->default(0);

            $table->unsignedInteger('maximum')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Constraints
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'character_id',
                'slot_type',
                'slot_level',
            ]);

            $table->index([
                'character_id',
                'slot_type',
            ]);

            $table->index([
                'character_id',
                'slot_level',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | CONCENTRAÇÃO → SPELL
        |--------------------------------------------------------------------------
        |
        | A tabela spells já existe neste ponto, então podemos criar a FK.
        |
        */

        Schema::table('character_combat', function (Blueprint $table) {
            $table->foreign('concentration_spell_id')
                ->references('id')
                ->on('spells')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Remover FK de concentração
        |--------------------------------------------------------------------------
        */

        Schema::table('character_combat', function (Blueprint $table) {
            $table->dropForeign([
                'concentration_spell_id',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Remover tabelas dependentes primeiro
        |--------------------------------------------------------------------------
        */

        Schema::dropIfExists('character_spell_slots');

        Schema::dropIfExists('character_spells');

        Schema::dropIfExists('character_spellcasting');

        Schema::dropIfExists('spells');
    }
};