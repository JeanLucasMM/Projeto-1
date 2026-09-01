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
        | Combat -> Campaign
        |--------------------------------------------------------------------------
        |
        | Nullable para preservar todos os combates independentes já existentes.
        |
        */

        if (!Schema::hasColumn('combats', 'campaign_id')) {
            Schema::table('combats', function (Blueprint $table) {
                $table->foreignId('campaign_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('campaigns')
                    ->nullOnDelete();

                $table->index(
                    ['campaign_id', 'created_at'],
                    'combats_campaign_created_index'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | CombatPlayer -> Character
        |--------------------------------------------------------------------------
        |
        | Nullable mantém participantes manuais funcionando.
        | Se a Character for apagada, o participante permanece com o nome
        | snapshot salvo em combat_players.name.
        |
        */

        if (!Schema::hasColumn('combat_players', 'character_id')) {
            Schema::table('combat_players', function (Blueprint $table) {
                $table->foreignId('character_id')
                    ->nullable()
                    ->after('combat_id')
                    ->constrained('characters')
                    ->nullOnDelete();

                $table->unique(
                    ['combat_id', 'character_id'],
                    'combat_players_combat_character_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('combat_players', 'character_id')) {
            Schema::table('combat_players', function (Blueprint $table) {
                $table->dropUnique(
                    'combat_players_combat_character_unique'
                );

                $table->dropConstrainedForeignId(
                    'character_id'
                );
            });
        }

        if (Schema::hasColumn('combats', 'campaign_id')) {
            Schema::table('combats', function (Blueprint $table) {
                $table->dropIndex(
                    'combats_campaign_created_index'
                );

                $table->dropConstrainedForeignId(
                    'campaign_id'
                );
            });
        }
    }
};