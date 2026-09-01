<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'characters',
            function (Blueprint $table) {
                /*
                 * Guarda preferências da ficha e regras opcionais.
                 *
                 * Estrutura inicial:
                 *
                 * {
                 *   "display": {
                 *     "show_empty_defenses": false,
                 *     "show_experience": false
                 *   },
                 *   "optional_rules": {}
                 * }
                 */
                $table
                    ->json('sheet_settings')
                    ->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'characters',
            function (Blueprint $table) {
                $table->dropColumn(
                    'sheet_settings'
                );
            }
        );
    }
};