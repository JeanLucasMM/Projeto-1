<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('character_items', function (Blueprint $table) {
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
            | Identificação
            |--------------------------------------------------------------------------
            */

            $table->string('name', 120);

            /*
            | Tipo livre.
            |
            | Exemplos:
            |
            | weapon
            | armor
            | shield
            | consumable
            | adventuring_gear
            | tool
            | treasure
            | wondrous_item
            |
            */

            $table->string('type', 50)
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Imagem
            |--------------------------------------------------------------------------
            |
            | Guarda apenas o caminho relativo da imagem do item.
            |
            | Exemplo:
            |
            | character-items/15/abc123.webp
            |
            | O arquivo físico fica em:
            |
            | storage/app/public/character-items/{character_id}/
            |
            */

            $table->string('image_path')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Quantidade e peso
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('quantity')
                ->default(1);

            /*
            | Peso individual do item.
            |
            | Exemplo:
            |
            | quantity = 3
            | weight   = 2.00
            |
            | Peso total = 6 lb.
            |
            */

            $table->decimal('weight', 8, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado do equipamento
            |--------------------------------------------------------------------------
            */

            $table->boolean('equipped')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Item mágico
            |--------------------------------------------------------------------------
            */

            /*
            | Define explicitamente se o item é mágico.
            |
            | Não inferimos isso pela raridade porque o sistema poderá
            | receber conteúdo personalizado no futuro.
            */

            $table->boolean('is_magical')
                ->default(false);

            /*
            | Raridade.
            |
            | Exemplos:
            |
            | common
            | uncommon
            | rare
            | very_rare
            | legendary
            | artifact
            |
            | Usamos string em vez de enum para permitir conteúdo
            | personalizado posteriormente.
            */

            $table->string('rarity', 30)
                ->nullable();

            /*
            | O item EXIGE sintonização?
            |
            | Isso é diferente de "attuned".
            |
            | requires_attunement = true
            | attuned             = false
            |
            | significa:
            |
            | "este item requer sintonização, mas o personagem ainda
            | não está sintonizado com ele."
            */

            $table->boolean('requires_attunement')
                ->default(false);

            /*
            | Estado atual da sintonização do personagem.
            */

            $table->boolean('attuned')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Maldição
            |--------------------------------------------------------------------------
            |
            | A permissão "somente Mestre pode criar item amaldiçoado"
            | NÃO deve ser controlada pela migration.
            |
            | Isso será protegido posteriormente em Controller / Policy.
            |
            */

            $table->boolean('is_cursed')
                ->default(false);

            /*
            | Texto completo da maldição.
            |
            | Exemplo:
            |
            | "Enquanto estiver sintonizado com este item, o usuário
            | não pode removê-lo voluntariamente..."
            |
            */

            $table->text('curse_description')
                ->nullable();

            /*
            | Permite que o Mestre mantenha a maldição escondida.
            |
            | Um item pode ser amaldiçoado sem que o jogador saiba.
            |
            | is_cursed      = true
            | curse_revealed = false
            |
            | No futuro, a interface do jogador simplesmente não
            | mostrará curse_description enquanto estiver false.
            */

            $table->boolean('curse_revealed')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Dados de combate
            |--------------------------------------------------------------------------
            */

            /*
            | Classe de Armadura fornecida pelo item.
            |
            | Exemplo:
            |
            | Armadura de placas = 18
            |
            | Para modificadores como "+1 CA", continuaremos usando
            | modifiers/properties.
            */

            $table->integer('armor_class')
                ->nullable();

            /*
            | Campo de compatibilidade para dano simples.
            |
            | Exemplo:
            |
            | 1d8
            | 2d6
            |
            | Armas com múltiplos danos poderão utilizar
            | properties.weapon.damage_parts.
            */

            $table->string('damage', 50)
                ->nullable();

            /*
            | Bônus adicional de ataque fornecido pelo próprio item.
            |
            | Exemplo:
            |
            | Espada +1:
            |
            | attack_bonus = 1
            */

            $table->integer('attack_bonus')
                ->nullable();

            /*
            | Bônus adicional de dano fornecido pelo item.
            */

            $table->integer('damage_bonus')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Bônus de atributos
            |--------------------------------------------------------------------------
            |
            | Exemplos:
            |
            | {
            |     "strength": 2
            | }
            |
            | ou:
            |
            | {
            |     "constitution": {
            |         "mode": "override",
            |         "value": 19
            |     }
            | }
            |
            */

            $table->json('ability_bonuses')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Propriedades do item
            |--------------------------------------------------------------------------
            |
            | Informações estruturadas específicas do item.
            |
            | Exemplo de arma:
            |
            | {
            |     "weapon": {
            |         "attack_ability": "dexterity",
            |         "proficient": true,
            |         "finesse": true,
            |         "range": "30/120 ft",
            |         "masteries": [
            |             "Vex"
            |         ],
            |         "damage_parts": [
            |             {
            |                 "expression": "1d8",
            |                 "type": "piercing",
            |                 "abilities": [
            |                     "dexterity"
            |                 ],
            |                 "bonus": 0
            |             },
            |             {
            |                 "expression": "1d8",
            |                 "type": "radiant",
            |                 "abilities": [],
            |                 "bonus": 0
            |             }
            |         ]
            |     }
            | }
            |
            */

            $table->json('properties')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Modificadores
            |--------------------------------------------------------------------------
            |
            | Efeitos numéricos ou mecânicos que não justificam uma
            | coluna própria.
            |
            | Exemplo:
            |
            | {
            |     "armor_class": 1,
            |     "saving_throws": 1
            | }
            |
            */

            $table->json('modifiers')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Anotações
            |--------------------------------------------------------------------------
            |
            | Campo livre do jogador.
            |
            | Não deve ser confundido com curse_description.
            |
            */

            $table->text('notes')
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
                'character_id',
                'type',
            ]);

            $table->index([
                'character_id',
                'equipped',
            ]);

            $table->index([
                'character_id',
                'attuned',
            ]);

            $table->index([
                'character_id',
                'is_magical',
            ]);

            $table->index([
                'character_id',
                'requires_attunement',
            ]);

            $table->index([
                'character_id',
                'is_cursed',
            ]);

            $table->index([
                'is_magical',
                'rarity',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_items');
    }
};