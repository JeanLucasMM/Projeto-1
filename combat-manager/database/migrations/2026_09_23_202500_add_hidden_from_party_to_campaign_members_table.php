<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_members', function (Blueprint $table) {
            $table->boolean('hidden_from_party')
                ->default(false)
                ->after('role');

            $table->index(
                [
                    'campaign_id',
                    'hidden_from_party',
                ],
                'campaign_members_campaign_hidden_index'
            );
        });
    }


    public function down(): void
    {
        Schema::table('campaign_members', function (Blueprint $table) {
            $table->dropIndex(
                'campaign_members_campaign_hidden_index'
            );

            $table->dropColumn(
                'hidden_from_party'
            );
        });
    }
};