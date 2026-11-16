<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combat_runs', function (Blueprint $table): void {
            $table->dateTime('metrics_started_at')->nullable();
            $table->unsignedInteger('turns_advanced')->default(0);
            $table->json('final_snapshot')->nullable();
        });

        Schema::create('combat_run_metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combat_run_id')->constrained('combat_runs')->cascadeOnDelete();
            $table->string('target_group', 16); // player, ally, enemy
            $table->string('target_name', 160);
            $table->unsignedInteger('round')->nullable();
            $table->unsignedInteger('turn')->nullable();
            $table->string('kind', 16); // damage ou healing
            $table->unsignedInteger('amount');
            $table->timestamps();
            $table->index(['combat_run_id', 'target_group', 'kind']);
        });

        Schema::create('combat_run_archives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combat_run_id')->unique()->constrained('combat_runs')->cascadeOnDelete();
            $table->json('snapshot');
            $table->json('statistics');
            $table->longText('image_svg');
            $table->timestamp('saved_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combat_run_archives');
        Schema::dropIfExists('combat_run_metrics');
        Schema::table('combat_runs', function (Blueprint $table): void {
            $table->dropColumn(['metrics_started_at', 'turns_advanced', 'final_snapshot']);
        });
    }
};
