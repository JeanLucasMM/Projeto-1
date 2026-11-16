<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'character_spellcasting',
            function (Blueprint $table) {
                if (
                    ! Schema::hasColumn(
                        'character_spellcasting',
                        'character_class_id'
                    )
                ) {
                    $table
                        ->foreignId('character_class_id')
                        ->nullable()
                        ->after('character_id')
                        ->constrained('character_classes')
                        ->nullOnDelete();
                }

                if (
                    ! Schema::hasColumn(
                        'character_spellcasting',
                        'sync_with_character_class'
                    )
                ) {
                    $table
                        ->boolean('sync_with_character_class')
                        ->default(false)
                        ->after('character_class_id');
                }
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'character_spellcasting',
            function (Blueprint $table) {
                if (
                    Schema::hasColumn(
                        'character_spellcasting',
                        'character_class_id'
                    )
                ) {
                    $table->dropConstrainedForeignId(
                        'character_class_id'
                    );
                }

                if (
                    Schema::hasColumn(
                        'character_spellcasting',
                        'sync_with_character_class'
                    )
                ) {
                    $table->dropColumn(
                        'sync_with_character_class'
                    );
                }
            }
        );
    }
};
