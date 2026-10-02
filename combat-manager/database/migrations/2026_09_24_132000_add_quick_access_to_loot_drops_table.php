<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'loot_drops',
            function (Blueprint $table): void {
                $table->boolean('is_quick_access')
                    ->default(false)
                    ->after('status');

                $table->index(
                    [
                        'campaign_id',
                        'is_quick_access',
                    ],
                    'loot_drops_campaign_quick_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'loot_drops',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'loot_drops_campaign_quick_idx'
                );

                $table->dropColumn(
                    'is_quick_access'
                );
            }
        );
    }
};