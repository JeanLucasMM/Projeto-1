<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('campaigns', 'experience_enabled')) {
            Schema::table('campaigns', function (Blueprint $table): void {
                $table->boolean('experience_enabled')
                    ->default(false)
                    ->after('description');
            });
        }

        if (!Schema::hasColumn('campaign_characters', 'experience_points')) {
            Schema::table('campaign_characters', function (Blueprint $table): void {
                $table->unsignedBigInteger('experience_points')
                    ->default(0)
                    ->after('is_active');
            });
        }

        /*
        | Migração conservadora do XP legado:
        | só copiamos character_combat.experience_points quando a Character está
        | vinculada a exatamente uma campanha. Em múltiplas mesas não adivinhamos.
        */
        if (
            Schema::hasTable('character_combat')
            && Schema::hasColumn('character_combat', 'experience_points')
        ) {
            $legacyRows = DB::table('character_combat')
                ->where('experience_points', '>', 0)
                ->get(['character_id', 'experience_points']);

            foreach ($legacyRows as $legacy) {
                $links = DB::table('campaign_characters')
                    ->where('character_id', $legacy->character_id)
                    ->pluck('id');

                if ($links->count() !== 1) {
                    continue;
                }

                DB::table('campaign_characters')
                    ->where('id', $links->first())
                    ->where('experience_points', 0)
                    ->update([
                        'experience_points' => max(
                            0,
                            (int) $legacy->experience_points
                        ),
                    ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('campaign_characters', 'experience_points')) {
            Schema::table('campaign_characters', function (Blueprint $table): void {
                $table->dropColumn('experience_points');
            });
        }

        if (Schema::hasColumn('campaigns', 'experience_enabled')) {
            Schema::table('campaigns', function (Blueprint $table): void {
                $table->dropColumn('experience_enabled');
            });
        }
    }
};
