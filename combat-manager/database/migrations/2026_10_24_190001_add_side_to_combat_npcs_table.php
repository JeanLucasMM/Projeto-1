<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combat_npcs', function (Blueprint $table): void {
            $table->string('side', 12)->default('enemy');
            $table->index(['combat_id', 'side'], 'combat_npcs_combat_side_idx');
        });
    }

    public function down(): void
    {
        Schema::table('combat_npcs', function (Blueprint $table): void {
            $table->dropIndex('combat_npcs_combat_side_idx');
            $table->dropColumn('side');
        });
    }
};
