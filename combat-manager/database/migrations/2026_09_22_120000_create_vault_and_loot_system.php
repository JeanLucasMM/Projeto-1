<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_vault_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('type', 50)->default('mundane');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('default_quantity')->default(1);
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_magical')->default(false);
            $table->string('rarity', 30)->nullable();
            $table->boolean('requires_attunement')->default(false);
            $table->boolean('is_cursed')->default(false);
            $table->text('curse_description')->nullable();
            $table->integer('armor_class')->nullable();
            $table->string('damage', 120)->nullable();
            $table->integer('attack_bonus')->nullable();
            $table->integer('damage_bonus')->nullable();
            $table->json('ability_bonuses')->nullable();
            $table->json('properties')->nullable();
            $table->json('modifiers')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('favorite')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'favorite']);
            $table->index(['user_id', 'name']);
        });

        Schema::create('loot_drops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('combat_id')->nullable()->constrained('combats')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('released_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index(['combat_id', 'status']);
        });

        Schema::create('loot_drop_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_drop_id')->constrained('loot_drops')->cascadeOnDelete();
            $table->foreignId('vault_item_id')->nullable()->constrained('master_vault_items')->nullOnDelete();
            $table->string('name', 120);
            $table->string('type', 50)->default('mundane');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_magical')->default(false);
            $table->string('rarity', 30)->nullable();
            $table->boolean('requires_attunement')->default(false);
            $table->boolean('is_cursed')->default(false);
            $table->text('curse_description')->nullable();
            $table->integer('armor_class')->nullable();
            $table->string('damage', 120)->nullable();
            $table->integer('attack_bonus')->nullable();
            $table->integer('damage_bonus')->nullable();
            $table->json('ability_bonuses')->nullable();
            $table->json('properties')->nullable();
            $table->json('modifiers')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('quantity_total')->default(1);
            $table->unsignedInteger('quantity_remaining')->default(1);
            $table->string('release_status', 20)->default('hidden');
            $table->string('visibility_mode', 20)->default('party');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('released_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('depleted_at')->nullable();
            $table->timestamps();

            $table->index(['loot_drop_id', 'release_status']);
            $table->index(['loot_drop_id', 'visibility_mode']);
        });

        Schema::create('loot_item_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_drop_item_id')->constrained('loot_drop_items')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['loot_drop_item_id', 'character_id'], 'loot_item_recipient_unique');
        });

        Schema::create('loot_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_drop_item_id')->constrained('loot_drop_items')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('character_item_id')->nullable()->constrained('character_items')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('claimed_at');
            $table->timestamps();

            $table->index(['character_id', 'claimed_at']);
            $table->index(['loot_drop_item_id', 'claimed_at']);
        });

        Schema::create('loot_currency_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_drop_id')->constrained('loot_drops')->cascadeOnDelete();
            $table->string('label', 120)->nullable();

            foreach (['copper', 'silver', 'electrum', 'gold', 'platinum'] as $coin) {
                $table->unsignedBigInteger($coin . '_total')->default(0);
                $table->unsignedBigInteger($coin . '_remaining')->default(0);
            }

            $table->string('release_status', 20)->default('hidden');
            $table->string('visibility_mode', 20)->default('party');
            $table->string('distribution_mode', 20)->default('first_claim');
            $table->timestamp('released_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('depleted_at')->nullable();
            $table->timestamps();

            $table->index(['loot_drop_id', 'release_status']);
        });

        Schema::create('loot_currency_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_currency_entry_id')->constrained('loot_currency_entries')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();

            foreach (['copper', 'silver', 'electrum', 'gold', 'platinum'] as $coin) {
                $table->unsignedBigInteger($coin . '_allocated')->default(0);
            }

            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
            $table->unique(['loot_currency_entry_id', 'character_id'], 'loot_currency_recipient_unique');
        });

        Schema::create('loot_currency_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loot_currency_entry_id')->constrained('loot_currency_entries')->cascadeOnDelete();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            foreach (['copper', 'silver', 'electrum', 'gold', 'platinum'] as $coin) {
                $table->unsignedBigInteger($coin)->default(0);
            }

            $table->timestamp('claimed_at');
            $table->timestamps();

            $table->index(['character_id', 'claimed_at']);
            $table->index(['loot_currency_entry_id', 'claimed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loot_currency_claims');
        Schema::dropIfExists('loot_currency_recipients');
        Schema::dropIfExists('loot_currency_entries');
        Schema::dropIfExists('loot_claims');
        Schema::dropIfExists('loot_item_recipients');
        Schema::dropIfExists('loot_drop_items');
        Schema::dropIfExists('loot_drops');
        Schema::dropIfExists('master_vault_items');
    }
};
