<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (!Schema::hasColumn('loot_drops', 'shop_has_counter')) {
                $table->boolean('shop_has_counter')
                    ->default(true)
                    ->after('shop_background_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (Schema::hasColumn('loot_drops', 'shop_has_counter')) {
                $table->dropColumn('shop_has_counter');
            }
        });
    }
};
