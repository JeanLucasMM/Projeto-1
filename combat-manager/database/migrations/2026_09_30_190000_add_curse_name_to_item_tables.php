<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['master_vault_items', 'loot_drop_items', 'character_items'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'curse_name')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->string('curse_name', 160)
                        ->nullable()
                        ->after('is_cursed');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['character_items', 'loot_drop_items', 'master_vault_items'] as $tableName) {
            if (Schema::hasColumn($tableName, 'curse_name')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('curse_name');
                });
            }
        }
    }
};
