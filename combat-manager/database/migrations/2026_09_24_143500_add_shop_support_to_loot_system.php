<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loot_drops', function (Blueprint $table) {
            if (!Schema::hasColumn('loot_drops', 'is_shop')) {
                $isShop = $table->boolean('is_shop')
                    ->default(false);

                if (Schema::hasColumn('loot_drops', 'is_quick_access')) {
                    $isShop->after('is_quick_access');
                } else {
                    $isShop->after('closed_at');
                }

                $table->index(
                    ['campaign_id', 'is_shop', 'status'],
                    'loot_drops_campaign_shop_status_index'
                );
            }

            if (!Schema::hasColumn('loot_drops', 'closed_reason')) {
                $table->string('closed_reason', 30)
                    ->nullable()
                    ->after('status');
            }

            if (!Schema::hasColumn('loot_drops', 'shopkeeper_npc_id')) {
                $table->foreignId('shopkeeper_npc_id')
                    ->nullable()
                    ->after('is_shop')
                    ->constrained('npcs')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('loot_drops', 'shop_image_path')) {
                $table->string('shop_image_path', 2048)
                    ->nullable()
                    ->after('shopkeeper_npc_id');
            }

            if (!Schema::hasColumn('loot_drops', 'shop_background_path')) {
                $table->string('shop_background_path', 2048)
                    ->nullable()
                    ->after('shop_image_path');
            }
        });

        Schema::table('loot_drop_items', function (Blueprint $table) {
            $columns = [
                'price_copper',
                'price_silver',
                'price_electrum',
                'price_gold',
                'price_platinum',
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('loot_drop_items', $column)) {
                    $table->unsignedBigInteger($column)
                        ->default(0)
                        ->after('quantity_remaining');
                }
            }
        });

        Schema::table('loot_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('loot_claims', 'claim_type')) {
                $table->string('claim_type', 20)
                    ->default('loot')
                    ->after('quantity');

                $table->index(
                    ['claim_type', 'claimed_at'],
                    'loot_claims_type_claimed_at_index'
                );
            }

            if (!Schema::hasColumn('loot_claims', 'price_paid_copper_value')) {
                $table->unsignedBigInteger('price_paid_copper_value')
                    ->default(0)
                    ->after('claim_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loot_claims', function (Blueprint $table) {
            if (Schema::hasColumn('loot_claims', 'price_paid_copper_value')) {
                $table->dropColumn('price_paid_copper_value');
            }

            if (Schema::hasColumn('loot_claims', 'claim_type')) {
                $table->dropIndex('loot_claims_type_claimed_at_index');
                $table->dropColumn('claim_type');
            }
        });

        Schema::table('loot_drop_items', function (Blueprint $table) {
            foreach (
                [
                    'price_copper',
                    'price_silver',
                    'price_electrum',
                    'price_gold',
                    'price_platinum',
                ] as $column
            ) {
                if (Schema::hasColumn('loot_drop_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('loot_drops', function (Blueprint $table) {
            if (Schema::hasColumn('loot_drops', 'shop_background_path')) {
                $table->dropColumn('shop_background_path');
            }

            if (Schema::hasColumn('loot_drops', 'shop_image_path')) {
                $table->dropColumn('shop_image_path');
            }

            if (Schema::hasColumn('loot_drops', 'shopkeeper_npc_id')) {
                $table->dropConstrainedForeignId('shopkeeper_npc_id');
            }

            if (Schema::hasColumn('loot_drops', 'closed_reason')) {
                $table->dropColumn('closed_reason');
            }

            if (Schema::hasColumn('loot_drops', 'is_shop')) {
                $table->dropIndex('loot_drops_campaign_shop_status_index');
                $table->dropColumn('is_shop');
            }
        });
    }
};