<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permitir nomes repetidos entre fontes
        |--------------------------------------------------------------------------
        |
        | O catálogo precisa permitir, por exemplo:
        |
        | Fireball | PHB
        | Fireball | XPHB
        |
        | Bancos mais antigos do Spellbound podem ou não possuir um UNIQUE
        | somente sobre `spells.name`, e o nome desse índice pode variar.
        |
        | Por isso não presumimos "spells_name_unique". Procuramos no MySQL
        | qualquer índice UNIQUE composto exclusivamente pela coluna `name`
        | e o removemos somente se ele realmente existir.
        |
        */

        $indexes = DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'spells'
              AND NON_UNIQUE = 0
              AND INDEX_NAME <> 'PRIMARY'
            GROUP BY INDEX_NAME
            HAVING COUNT(*) = 1
               AND MAX(COLUMN_NAME) = 'name'
        ");

        foreach ($indexes as $index) {
            $indexName = str_replace('`', '``', $index->INDEX_NAME);

            DB::statement(
                "ALTER TABLE `spells` DROP INDEX `{$indexName}`"
            );
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rollback seguro
        |--------------------------------------------------------------------------
        |
        | Depois que PHB, XPHB e outras fontes forem importadas, nomes iguais
        | passam a ser válidos. Recriar UNIQUE(name) poderia destruir essa
        | possibilidade ou fazer o rollback falhar por dados duplicados.
        |
        | Portanto esta migration não recria automaticamente o índice antigo.
        |
        */
    }
};