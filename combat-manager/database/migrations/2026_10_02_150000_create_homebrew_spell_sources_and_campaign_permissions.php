<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homebrew_spell_sources', function (Blueprint $table) {
            $table->id();

            $table->string('provider', 40)
                ->default('5etools-homebrew');

            $table->string('source_code', 191);

            $table->string('name')
                ->nullable();

            $table->json('authors')
                ->nullable();

            $table->string('url', 2048)
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->json('origin_files')
                ->nullable();

            $table->unsignedInteger('spell_count')
                ->default(0);

            $table->boolean('is_available')
                ->default(true);

            $table->timestamp('last_seen_at')
                ->nullable();

            $table->timestamps();

            $table->unique(
                ['provider', 'source_code'],
                'homebrew_sources_provider_code_unique'
            );

            $table->index('name');
            $table->index('is_available');
        });

        Schema::table('spells', function (Blueprint $table) {
            $table->foreignId('homebrew_spell_source_id')
                ->nullable()
                ->after('provider')
                ->constrained('homebrew_spell_sources')
                ->nullOnDelete();

            $table->index(
                ['provider', 'homebrew_spell_source_id'],
                'spells_provider_homebrew_source_index'
            );
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('allow_all_homebrew_spells')
                ->default(false);
        });

        Schema::create('campaign_homebrew_spell_sources', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            $table->foreignId('homebrew_spell_source_id')
                ->constrained('homebrew_spell_sources')
                ->cascadeOnDelete();

            $table->foreignId('enabled_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['campaign_id', 'homebrew_spell_source_id'],
                'campaign_homebrew_source_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Backfill dos homebrews já importados
        |--------------------------------------------------------------------------
        |
        | Nada é apagado. Cada source já existente entre os spells homebrew vira
        | uma entrada em homebrew_spell_sources e os spells são vinculados a ela.
        |
        */

        $sourceCodes = DB::table('spells')
            ->where('provider', '5etools-homebrew')
            ->whereNotNull('source')
            ->where('source', '<>', '')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        foreach ($sourceCodes as $sourceCode) {
            $now = now();

            $sourceId = DB::table('homebrew_spell_sources')->insertGetId([
                'provider' => '5etools-homebrew',
                'source_code' => $sourceCode,
                'name' => $sourceCode,
                'spell_count' => DB::table('spells')
                    ->where('provider', '5etools-homebrew')
                    ->where('source', $sourceCode)
                    ->count(),
                'is_available' => true,
                'last_seen_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('spells')
                ->where('provider', '5etools-homebrew')
                ->where('source', $sourceCode)
                ->update([
                    'homebrew_spell_source_id' => $sourceId,
                ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_homebrew_spell_sources');

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('allow_all_homebrew_spells');
        });

        Schema::table('spells', function (Blueprint $table) {
            $table->dropIndex('spells_provider_homebrew_source_index');
            $table->dropConstrainedForeignId('homebrew_spell_source_id');
        });

        Schema::dropIfExists('homebrew_spell_sources');
    }
};
