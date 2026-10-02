<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (!Schema::hasColumn('loot_drops', 'image_path')) {
                $table->string('image_path', 2048)
                    ->nullable()
                    ->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (Schema::hasColumn('loot_drops', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
