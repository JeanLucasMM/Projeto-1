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
        | PERSONAGENS
        |--------------------------------------------------------------------------
        |
        | Guarda apenas os dados gerais da ficha.
        |
        | Dados específicos de combate ficam em character_combat.
        | Progressão individual de classe fica em character_classes.
        | Atributos ficam em character_abilities.
        |
        */

        Schema::create('characters', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Proprietário
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identidade
            |--------------------------------------------------------------------------
            */

            $table->string('name', 120);

            $table->string('species', 80)
                ->nullable();

            $table->string('background', 120)
                ->nullable();

            $table->string('alignment', 50)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Progressão geral
            |--------------------------------------------------------------------------
            |
            | level é o nível total exibido na ficha.
            |
            | Os níveis individuais das classes ficam em character_classes.
            |
            | Mantemos este valor separado porque o Spellbound permite
            | edição livre da ficha.
            |
            */

            $table->unsignedTinyInteger('level')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Proficiência
            |--------------------------------------------------------------------------
            |
            | Não calculamos rigidamente pelo nível.
            |
            | O valor pode ser alterado livremente pelo jogador.
            |
            | Exemplo:
            |
            | nível 20
            | proficiência +8
            |
            | O sistema deve aceitar.
            |
            */

            $table->integer('proficiency_bonus')
                ->default(2);

            /*
            |--------------------------------------------------------------------------
            | Inspiração Heroica
            |--------------------------------------------------------------------------
            |
            | Na ficha 2024 é essencialmente um estado booleano:
            | possui ou não possui inspiração.
            |
            */

            $table->boolean('heroic_inspiration')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Imagem
            |--------------------------------------------------------------------------
            */

            $table->string('image_path')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index([
                'user_id',
                'created_at',
            ]);

            $table->index([
                'user_id',
                'name',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | CLASSES
        |--------------------------------------------------------------------------
        |
        | Permite multiclass.
        |
        | Exemplo:
        |
        | Guerreiro 5
        | Mago 3
        | Ladino 2
        |
        | Não usamos character.level para representar o nível individual.
        |
        */

        Schema::create('character_classes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Classe
            |--------------------------------------------------------------------------
            */

            $table->string('class', 80);

            /*
            |--------------------------------------------------------------------------
            | Subclasse
            |--------------------------------------------------------------------------
            */

            $table->string('subclass', 80)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Nível da classe
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('level')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Ordem / classe principal
            |--------------------------------------------------------------------------
            |
            | Permite controlar qual classe deve aparecer como principal
            | sem depender exclusivamente do maior nível.
            |
            */

            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            $table->boolean('is_primary')
                ->default(false);

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
            | Um personagem não possui duas entradas da mesma classe.
            |
            */

            $table->unique([
                'character_id',
                'class',
            ]);

            $table->index([
                'character_id',
                'level',
            ]);

            $table->index([
                'character_id',
                'is_primary',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | ATRIBUTOS
        |--------------------------------------------------------------------------
        |
        | Cada personagem possui exatamente um conjunto de atributos.
        |
        | score = valor oficial da ficha.
        |
        | temporary_bonuses = modificadores temporários.
        |
        | overrides = modificações especiais que podem substituir cálculos
        | derivados, mantendo liberdade para regras alternativas.
        |
        */

        Schema::create('character_abilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Valores oficiais
            |--------------------------------------------------------------------------
            */

            $table->integer('strength')
                ->default(10);

            $table->integer('dexterity')
                ->default(10);

            $table->integer('constitution')
                ->default(10);

            $table->integer('intelligence')
                ->default(10);

            $table->integer('wisdom')
                ->default(10);

            $table->integer('charisma')
                ->default(10);

            /*
            |--------------------------------------------------------------------------
            | Modificadores temporários
            |--------------------------------------------------------------------------
            |
            | Exemplo:
            |
            | {
            |     "strength": 12,
            |     "dexterity": -2
            | }
            |
            | Força 13 + 12 = 25.
            |
            */

            $table->json('temporary_bonuses')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Overrides
            |--------------------------------------------------------------------------
            |
            | Permite substituir o valor calculado quando necessário.
            |
            | Exemplo:
            |
            | {
            |     "strength_modifier": 7
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

            $table->unique('character_id');
        });


        /*
        |--------------------------------------------------------------------------
        | PERÍCIAS
        |--------------------------------------------------------------------------
        |
        | Proficiencia e expertise são estados independentes.
        |
        | bonus_override permite qualquer valor, inclusive acima do padrão.
        |
        */

        Schema::create('character_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('skill', 80);

            /*
            |--------------------------------------------------------------------------
            | Treinamento
            |--------------------------------------------------------------------------
            */

            $table->boolean('proficient')
                ->default(false);

            $table->boolean('expertise')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Modificador personalizado
            |--------------------------------------------------------------------------
            |
            | Permite:
            |
            | +10
            | +12
            | -5
            | etc.
            |
            */

            $table->integer('bonus_override')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Modificador temporário
            |--------------------------------------------------------------------------
            */

            $table->integer('temporary_bonus')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->unique([
                'character_id',
                'skill',
            ]);

            $table->index([
                'character_id',
                'proficient',
            ]);

            $table->index([
                'character_id',
                'expertise',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | SALVAGUARDAS
        |--------------------------------------------------------------------------
        */

        Schema::create('character_saving_throws', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Atributo utilizado
            |--------------------------------------------------------------------------
            */

            $table->string('ability', 20);

            /*
            |--------------------------------------------------------------------------
            | Proficiência
            |--------------------------------------------------------------------------
            */

            $table->boolean('proficient')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Override livre
            |--------------------------------------------------------------------------
            */

            $table->integer('bonus_override')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Modificador temporário
            |--------------------------------------------------------------------------
            */

            $table->integer('temporary_bonus')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->unique([
                'character_id',
                'ability',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | FEATURES / HABILIDADES
        |--------------------------------------------------------------------------
        |
        | Aqui entram:
        |
        | características raciais
        | talentos
        | características de classe
        | habilidades especiais
        | habilidades personalizadas
        |
        */

        Schema::create('character_features', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identificação
            |--------------------------------------------------------------------------
            */

            $table->string('name', 120);

            $table->string('type', 50)
                ->nullable();

            $table->string('source', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Progressão
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('level_acquired')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Descrição
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Utilizações
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('uses_max')
                ->nullable();

            $table->unsignedInteger('uses_current')
                ->nullable();

            $table->string('recovery', 50)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Personalização
            |--------------------------------------------------------------------------
            |
            | Permite guardar parâmetros específicos da habilidade.
            |
            */

            $table->json('data')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->index([
                'character_id',
                'type',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | RECURSOS
        |--------------------------------------------------------------------------
        |
        | Recursos genéricos da ficha.
        |
        | Exemplo:
        |
        | Channel Divinity
        | Ki
        | Sorcery Points
        | Action Surge
        | recursos personalizados
        |
        */

        Schema::create('character_resources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identificação
            |--------------------------------------------------------------------------
            */

            $table->string('name', 120);

            $table->string('type', 50)
                ->nullable();

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
            | Recuperação
            |--------------------------------------------------------------------------
            */

            $table->string('recovery', 50)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Origem
            |--------------------------------------------------------------------------
            */

            $table->string('source', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Dados adicionais
            |--------------------------------------------------------------------------
            */

            $table->json('data')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Metadados
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->index([
                'character_id',
                'type',
            ]);

            $table->index([
                'character_id',
                'source',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_resources');
        Schema::dropIfExists('character_features');
        Schema::dropIfExists('character_saving_throws');
        Schema::dropIfExists('character_skills');
        Schema::dropIfExists('character_abilities');
        Schema::dropIfExists('character_classes');
        Schema::dropIfExists('characters');
    }
};