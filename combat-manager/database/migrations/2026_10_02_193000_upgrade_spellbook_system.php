<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            if (! Schema::hasColumn('spells', 'higher_level_text')) {
                $table->longText('higher_level_text')->nullable()->after('higher_level_data');
            }

            if (! Schema::hasColumn('spells', 'scaling_level_dice')) {
                $table->json('scaling_level_dice')->nullable()->after('higher_level_text');
            }

            if (! Schema::hasColumn('spells', 'reprinted_as')) {
                $table->json('reprinted_as')->nullable()->after('scaling_level_dice');
            }
        });

        Schema::table('character_spellcasting', function (Blueprint $table) {
            if (! Schema::hasColumn('character_spellcasting', 'caster_level')) {
                $table->unsignedTinyInteger('caster_level')->default(1)->after('ability');
            }

            if (! Schema::hasColumn('character_spellcasting', 'caster_type')) {
                $table->string('caster_type', 30)->default('full')->after('caster_level');
            }

            if (! Schema::hasColumn('character_spellcasting', 'slot_mode')) {
                $table->string('slot_mode', 20)->default('automatic')->after('caster_type');
            }

            if (! Schema::hasColumn('character_spellcasting', 'effective_caster_level_override')) {
                $table->unsignedTinyInteger('effective_caster_level_override')->nullable()->after('slot_mode');
            }

            if (! Schema::hasColumn('character_spellcasting', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('spell_attack_bonus_override');
            }

            if (! Schema::hasColumn('character_spellcasting', 'sort_order')) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
            }

            if (! Schema::hasColumn('character_spellcasting', 'notes')) {
                $table->text('notes')->nullable()->after('sort_order');
            }
        });

        Schema::table('character_spell_slots', function (Blueprint $table) {
            if (! Schema::hasColumn('character_spell_slots', 'calculated_maximum')) {
                $table->unsignedTinyInteger('calculated_maximum')->nullable()->after('maximum');
            }

            if (! Schema::hasColumn('character_spell_slots', 'manual_maximum')) {
                $table->unsignedTinyInteger('manual_maximum')->nullable()->after('calculated_maximum');
            }

            if (! Schema::hasColumn('character_spell_slots', 'recovery')) {
                $table->string('recovery', 30)->default('long_rest')->after('manual_maximum');
            }
        });

        Schema::table('character_spells', function (Blueprint $table) {
            if (! Schema::hasColumn('character_spells', 'spellcasting_id')) {
                $table->foreignId('spellcasting_id')
                    ->nullable()
                    ->after('spell_id')
                    ->constrained('character_spellcasting')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('character_spells', 'always_prepared')) {
                $table->boolean('always_prepared')->default(false)->after('prepared');
            }

            if (! Schema::hasColumn('character_spells', 'free_casts_current')) {
                $table->unsignedSmallInteger('free_casts_current')->nullable()->after('always_prepared');
            }

            if (! Schema::hasColumn('character_spells', 'free_casts_max')) {
                $table->unsignedSmallInteger('free_casts_max')->nullable()->after('free_casts_current');
            }

            if (! Schema::hasColumn('character_spells', 'free_casts_recovery')) {
                $table->string('free_casts_recovery', 30)->nullable()->after('free_casts_max');
            }
        });
    }

    public function down(): void
    {
        Schema::table('character_spells', function (Blueprint $table) {
            if (Schema::hasColumn('character_spells', 'spellcasting_id')) {
                $table->dropConstrainedForeignId('spellcasting_id');
            }

            foreach ([
                'always_prepared',
                'free_casts_current',
                'free_casts_max',
                'free_casts_recovery',
            ] as $column) {
                if (Schema::hasColumn('character_spells', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('character_spell_slots', function (Blueprint $table) {
            foreach ([
                'calculated_maximum',
                'manual_maximum',
                'recovery',
            ] as $column) {
                if (Schema::hasColumn('character_spell_slots', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('character_spellcasting', function (Blueprint $table) {
            foreach ([
                'caster_level',
                'caster_type',
                'slot_mode',
                'effective_caster_level_override',
                'is_active',
                'sort_order',
                'notes',
            ] as $column) {
                if (Schema::hasColumn('character_spellcasting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('spells', function (Blueprint $table) {
            foreach ([
                'higher_level_text',
                'scaling_level_dice',
                'reprinted_as',
            ] as $column) {
                if (Schema::hasColumn('spells', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
