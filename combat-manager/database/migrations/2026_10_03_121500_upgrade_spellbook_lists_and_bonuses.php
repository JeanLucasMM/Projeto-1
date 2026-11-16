<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('character_spellcasting', function (Blueprint $table) {
            if (! Schema::hasColumn('character_spellcasting', 'spell_save_dc_bonus')) {
                $table->smallInteger('spell_save_dc_bonus')->default(0)->after('spell_save_dc_override');
            }

            if (! Schema::hasColumn('character_spellcasting', 'spell_attack_bonus_extra')) {
                $table->smallInteger('spell_attack_bonus_extra')->default(0)->after('spell_attack_bonus_override');
            }

            if (! Schema::hasColumn('character_spellcasting', 'spell_list_codes')) {
                $table->json('spell_list_codes')->nullable()->after('spell_attack_bonus_extra');
            }
        });

        if (! Schema::hasTable('spell_class_lists')) {
            Schema::create('spell_class_lists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('spell_id')->constrained('spells')->cascadeOnDelete();
                $table->string('provider', 30)->default('5etools')->index();
                $table->string('class_code', 40)->index();
                $table->string('class_name', 80);
                $table->string('class_source', 30)->nullable();
                $table->string('subclass_name', 120)->nullable();
                $table->string('subclass_source', 30)->nullable();
                $table->string('list_type', 20)->default('class');
                $table->string('normalized_key', 191)->unique();
                $table->timestamps();

                $table->index(['spell_id', 'class_code'], 'spell_class_lists_spell_class_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_class_lists');

        Schema::table('character_spellcasting', function (Blueprint $table) {
            foreach ([
                'spell_save_dc_bonus',
                'spell_attack_bonus_extra',
                'spell_list_codes',
            ] as $column) {
                if (Schema::hasColumn('character_spellcasting', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
