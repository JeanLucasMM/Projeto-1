<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spell_class_lists')) {
            return;
        }

        Schema::table(
            'spell_class_lists',
            function (Blueprint $table): void {
                /*
                 * Sources e nomes vindos de homebrews não são
                 * obrigados a seguir os códigos curtos dos livros
                 * oficiais (PHB, XPHB, TCE etc.).
                 *
                 * Não truncamos esses valores: eles também fazem
                 * parte da identidade usada pelo sincronizador.
                 */
                $table
                    ->string(
                        'class_name',
                        255
                    )
                    ->change();

                $table
                    ->string(
                        'class_source',
                        255
                    )
                    ->nullable()
                    ->change();

                $table
                    ->string(
                        'subclass_name',
                        255
                    )
                    ->nullable()
                    ->change();

                $table
                    ->string(
                        'subclass_source',
                        255
                    )
                    ->nullable()
                    ->change();
            }
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('spell_class_lists')) {
            return;
        }

        Schema::table(
            'spell_class_lists',
            function (Blueprint $table): void {
                /*
                 * Atenção: o rollback só é seguro se nenhum dado
                 * atual ultrapassar os tamanhos antigos.
                 */
                $table
                    ->string(
                        'class_name',
                        80
                    )
                    ->change();

                $table
                    ->string(
                        'class_source',
                        30
                    )
                    ->nullable()
                    ->change();

                $table
                    ->string(
                        'subclass_name',
                        120
                    )
                    ->nullable()
                    ->change();

                $table
                    ->string(
                        'subclass_source',
                        30
                    )
                    ->nullable()
                    ->change();
            }
        );
    }
};
