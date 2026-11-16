<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('character_spellcasting', function (Blueprint $table) {
            if (! Schema::hasColumn('character_spellcasting', 'spell_limit')) {
                $table->unsignedSmallInteger('spell_limit')
                    ->nullable()
                    ->after('spell_list_codes');
            }

            if (! Schema::hasColumn('character_spellcasting', 'prepared_limit')) {
                $table->unsignedSmallInteger('prepared_limit')
                    ->nullable()
                    ->after('spell_limit');
            }
        });

        Schema::table('character_spells', function (Blueprint $table) {
            if (! Schema::hasColumn('character_spells', 'cast_without_slot')) {
                $table->boolean('cast_without_slot')
                    ->default(false)
                    ->after('free_casts_recovery');
            }
        });

        if (! Schema::hasTable('character_spell_loadouts')) {
            Schema::create('character_spell_loadouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('character_id')
                    ->constrained('characters')
                    ->cascadeOnDelete();
                $table->foreignId('spellcasting_id')
                    ->nullable()
                    ->constrained('character_spellcasting')
                    ->nullOnDelete();
                $table->string('name', 80);
                $table->string('description', 255)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(
                    ['character_id', 'spellcasting_id', 'sort_order'],
                    'character_spell_loadouts_character_profile_sort_index'
                );
            });
        }

        if (! Schema::hasTable('character_spell_loadout_spells')) {
            Schema::create('character_spell_loadout_spells', function (Blueprint $table) {
                $table->id();
                $table->foreignId('spell_loadout_id')
                    ->constrained('character_spell_loadouts')
                    ->cascadeOnDelete();
                $table->foreignId('spell_id')
                    ->constrained('spells')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(
                    ['spell_loadout_id', 'spell_id'],
                    'character_spell_loadout_spells_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('character_spell_loadout_spells');
        Schema::dropIfExists('character_spell_loadouts');

        Schema::table('character_spells', function (Blueprint $table) {
            if (Schema::hasColumn('character_spells', 'cast_without_slot')) {
                $table->dropColumn('cast_without_slot');
            }
        });

        Schema::table('character_spellcasting', function (Blueprint $table) {
            foreach ([
                'prepared_limit',
                'spell_limit',
            ] as $column) {
                if (Schema::hasColumn('character_spellcasting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
