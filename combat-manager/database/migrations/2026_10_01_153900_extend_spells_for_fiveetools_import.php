<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Origem / identificação externa
            |--------------------------------------------------------------------------
            */

            $table->string('provider', 30)
                ->default('custom')
                ->index();

            $table->string('external_key', 191)
                ->nullable();

            $table->string('rules_version', 20)
                ->nullable()
                ->index();

            $table->unsignedSmallInteger('page')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Dados estruturados do 5etools
            |--------------------------------------------------------------------------
            */

            $table->json('other_sources')
                ->nullable();

            $table->json('casting_time_data')
                ->nullable();

            $table->json('range_data')
                ->nullable();

            $table->json('duration_data')
                ->nullable();

            $table->json('description_data')
                ->nullable();

            $table->json('higher_level_data')
                ->nullable();

            $table->json('damage_types')
                ->nullable();

            $table->json('saving_throws')
                ->nullable();

            $table->json('area_tags')
                ->nullable();

            $table->json('misc_tags')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Snapshot original do registro upstream
            |--------------------------------------------------------------------------
            */

            $table->json('raw_data')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Sincronização
            |--------------------------------------------------------------------------
            */

            $table->char('upstream_hash', 64)
                ->nullable();

            $table->timestamp('imported_at')
                ->nullable();

            $table->timestamp('last_seen_at')
                ->nullable();

            $table->boolean('upstream_missing')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['provider', 'external_key'],
                'spells_provider_external_key_unique'
            );

            $table->index(
                ['provider', 'source'],
                'spells_provider_source_index'
            );

            $table->index(
                ['level', 'school'],
                'spells_level_school_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            $table->dropUnique('spells_provider_external_key_unique');
            $table->dropIndex('spells_provider_source_index');
            $table->dropIndex('spells_level_school_index');

            $table->dropColumn([
                'provider',
                'external_key',
                'rules_version',
                'page',
                'other_sources',
                'casting_time_data',
                'range_data',
                'duration_data',
                'description_data',
                'higher_level_data',
                'damage_types',
                'saving_throws',
                'area_tags',
                'misc_tags',
                'raw_data',
                'upstream_hash',
                'imported_at',
                'last_seen_at',
                'upstream_missing',
            ]);
        });
    }
};
