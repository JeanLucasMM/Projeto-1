<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Ampliar campos legados do catálogo de feitiços
        |--------------------------------------------------------------------------
        |
        | A tabela `spells` foi criada originalmente para entradas manuais mais
        | curtas. O 5etools possui valores legítimos bem maiores, principalmente
        | em casting_time de reações condicionais.
        |
        | Mantemos esses campos como versões simples para exibição, enquanto os
        | dados estruturados completos continuam nas colunas *_data.
        |
        */

        DB::statement('ALTER TABLE `spells` MODIFY `casting_time` TEXT NULL');
        DB::statement('ALTER TABLE `spells` MODIFY `range` TEXT NULL');
        DB::statement('ALTER TABLE `spells` MODIFY `duration` TEXT NULL');
        DB::statement('ALTER TABLE `spells` MODIFY `saving_throw` TEXT NULL');
        DB::statement('ALTER TABLE `spells` MODIFY `attack_type` TEXT NULL');
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rollback intencionalmente conservador
        |--------------------------------------------------------------------------
        |
        | Depois de importar o catálogo completo, esses campos podem conter
        | valores maiores que os limites antigos. Reduzi-los automaticamente
        | poderia truncar dados ou fazer o rollback falhar.
        |
        | Por isso não reduzimos novamente os tipos das colunas.
        |
        */
    }
};
