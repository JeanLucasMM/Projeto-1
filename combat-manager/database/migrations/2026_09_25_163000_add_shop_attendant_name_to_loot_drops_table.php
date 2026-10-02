<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (!Schema::hasColumn('loot_drops', 'shop_attendant_name')) {
                $table->string('shop_attendant_name', 150)
                    ->nullable()
                    ->after('shopkeeper_npc_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (Schema::hasColumn('loot_drops', 'shop_attendant_name')) {
                $table->dropColumn('shop_attendant_name');
            }
        });
    }
};