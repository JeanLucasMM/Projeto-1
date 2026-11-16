<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('combat_npcs', function (Blueprint $table): void {
            $table->unsignedInteger('base_max_hp')->nullable();
            $table->unsignedInteger('max_hp_bonus')->default(0);
            $table->unsignedInteger('max_hp_reduction')->default(0);
        });

        // Preserva o máximo já usado em combates existentes, inclusive NPCs builder.
        DB::table('combat_npcs')->update(['base_max_hp' => DB::raw('max_hp')]);
    }

    public function down(): void
    {
        Schema::table('combat_npcs', function (Blueprint $table): void {
            $table->dropColumn(['base_max_hp', 'max_hp_bonus', 'max_hp_reduction']);
        });
    }
};
