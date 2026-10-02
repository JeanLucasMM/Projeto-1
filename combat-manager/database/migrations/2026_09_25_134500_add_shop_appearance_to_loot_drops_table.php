<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            if (!Schema::hasColumn('loot_drops', 'shop_counter_color')) {
                $table->string('shop_counter_color', 7)
                    ->default('#6B3A24')
                    ->after('shop_background_path');
            }

            if (!Schema::hasColumn('loot_drops', 'shop_background_color')) {
                $table->string('shop_background_color', 7)
                    ->default('#6F4A33')
                    ->after('shop_counter_color');
            }

            if (!Schema::hasColumn('loot_drops', 'shop_image_zoom')) {
                $table->unsignedSmallInteger('shop_image_zoom')
                    ->default(100)
                    ->after('shop_background_color');
            }

            if (!Schema::hasColumn('loot_drops', 'shop_image_position')) {
                $table->string('shop_image_position', 10)
                    ->default('center')
                    ->after('shop_image_zoom');
            }

            if (!Schema::hasColumn('loot_drops', 'shop_background_zoom')) {
                $table->unsignedSmallInteger('shop_background_zoom')
                    ->default(100)
                    ->after('shop_image_position');
            }

            if (!Schema::hasColumn('loot_drops', 'shop_background_position')) {
                $table->string('shop_background_position', 10)
                    ->default('center')
                    ->after('shop_background_zoom');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loot_drops', function (Blueprint $table): void {
            foreach (
                [
                    'shop_background_position',
                    'shop_background_zoom',
                    'shop_image_position',
                    'shop_image_zoom',
                    'shop_background_color',
                    'shop_counter_color',
                ] as $column
            ) {
                if (Schema::hasColumn('loot_drops', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
