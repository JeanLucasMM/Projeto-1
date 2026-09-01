<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable(
                'campaign_characters'
            )
            ||
            !Schema::hasTable(
                'characters'
            )
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Compatibilidade com vínculos criados antes da regra "Jogando agora"
        |--------------------------------------------------------------------------
        |
        | A implementação antiga marcava toda ficha compartilhada como ativa.
        | Mantemos apenas uma ativa para cada usuário em cada campanha.
        |
        | Preferimos o vínculo ativo atualizado mais recentemente.
        |
        */

        $links =
            DB::table(
                'campaign_characters'
            )
                ->join(
                    'characters',
                    'characters.id',
                    '=',
                    'campaign_characters.character_id'
                )
                ->where(
                    'campaign_characters.is_active',
                    true
                )
                ->orderBy(
                    'campaign_characters.campaign_id'
                )
                ->orderBy(
                    'characters.user_id'
                )
                ->orderByDesc(
                    'campaign_characters.updated_at'
                )
                ->orderByDesc(
                    'campaign_characters.id'
                )
                ->get([
                    'campaign_characters.id',
                    'campaign_characters.campaign_id',
                    'characters.user_id',
                ]);

        $seen =
            [];

        foreach ($links as $link) {
            $key =
                (string) $link->campaign_id
                .
                ':'
                .
                (string) $link->user_id;

            if (!isset($seen[$key])) {
                $seen[$key] =
                    true;

                continue;
            }

            DB::table(
                'campaign_characters'
            )
                ->where(
                    'id',
                    $link->id
                )
                ->update([
                    'is_active' =>
                        false,

                    'updated_at' =>
                        now(),
                ]);
        }
    }


    public function down(): void
    {
        /*
        | Migração de normalização de dados.
        | Não é possível reconstruir com segurança o estado inconsistente
        | anterior, portanto o rollback não altera os vínculos.
        */
    }
};