<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('character_spells', function (Blueprint $table): void {
            $table->text('personal_notes')->nullable();
            $table->string('personal_media_path', 500)->nullable();
        });

        Schema::create('character_spell_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('character_spell_id')->constrained('character_spells')->cascadeOnDelete();
            $table->foreignId('spellcasting_id')->constrained('character_spellcasting')->cascadeOnDelete();
            $table->boolean('known')->default(true);
            $table->boolean('prepared')->default(false);
            $table->boolean('always_prepared')->default(false);
            $table->timestamps();
            $table->unique(['character_spell_id', 'spellcasting_id'], 'spell_profiles_unique');
        });

        // Apenas copia os vínculos já existentes. Nada é removido do histórico.
        DB::table('character_spells')
            ->whereNotNull('spellcasting_id')
            ->orderBy('id')
            ->chunkById(500, function ($spells): void {
                $rows = [];
                foreach ($spells as $spell) {
                    $rows[] = [
                        'character_spell_id' => $spell->id,
                        'spellcasting_id' => $spell->spellcasting_id,
                        'known' => (bool) $spell->known,
                        'prepared' => (bool) $spell->prepared,
                        'always_prepared' => (bool) $spell->always_prepared,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if ($rows !== []) {
                    DB::table('character_spell_profiles')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_spell_profiles');
        Schema::table('character_spells', function (Blueprint $table): void {
            $table->dropColumn(['personal_notes', 'personal_media_path']);
        });
    }
};
