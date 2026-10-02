<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasColumn(
                'loot_claims',
                'reversed_at'
            )
        ) {
            Schema::table(
                'loot_claims',
                function (Blueprint $table): void {
                    $table
                        ->timestamp('reversed_at')
                        ->nullable()
                        ->after('claimed_at');
                }
            );
        }

        if (
            !Schema::hasColumn(
                'loot_claims',
                'reversed_by_user_id'
            )
        ) {
            Schema::table(
                'loot_claims',
                function (Blueprint $table): void {
                    $table
                        ->foreignId('reversed_by_user_id')
                        ->nullable()
                        ->after('reversed_at')
                        ->constrained('users')
                        ->nullOnDelete();
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasColumn(
                'loot_claims',
                'reversed_by_user_id'
            )
        ) {
            Schema::table(
                'loot_claims',
                function (Blueprint $table): void {
                    $table->dropConstrainedForeignId(
                        'reversed_by_user_id'
                    );
                }
            );
        }

        if (
            Schema::hasColumn(
                'loot_claims',
                'reversed_at'
            )
        ) {
            Schema::table(
                'loot_claims',
                function (Blueprint $table): void {
                    $table->dropColumn(
                        'reversed_at'
                    );
                }
            );
        }
    }
};