<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('combat_runs', function (Blueprint $table): void {
            $table->unsignedBigInteger('active_seconds')->nullable();
            $table->dateTime('clock_started_at')->nullable();
            $table->boolean('clock_paused')->default(false);
            $table->boolean('clock_complete')->default(false);
        });
        Schema::table('combat_run_metrics', function (Blueprint $table): void {
            $table->string('participant_type', 10)->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->index(['combat_run_id', 'participant_type', 'participant_id'], 'run_metrics_person_idx');
        });
        Schema::create('combat_run_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combat_run_id')->constrained('combat_runs')->cascadeOnDelete();
            $table->string('participant_type', 10);
            $table->unsignedBigInteger('participant_id');
            $table->string('side', 10);
            $table->string('display_name', 160);
            $table->string('image_path', 1024)->nullable();
            $table->dateTime('joined_at');
            $table->unsignedInteger('joined_round')->nullable();
            $table->boolean('joined_late')->default(false);
            $table->dateTime('left_at')->nullable();
            $table->timestamps();
            $table->unique(['combat_run_id','participant_type','participant_id'], 'run_participant_unique');
        });
        Schema::create('combat_run_activity', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combat_run_id')->constrained('combat_runs')->cascadeOnDelete();
            $table->string('kind', 24);
            $table->string('participant_type', 10)->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->string('participant_name', 160)->nullable();
            $table->string('side', 10)->nullable();
            $table->unsignedInteger('round')->nullable();
            $table->dateTime('occurred_at');
            $table->index(['combat_run_id', 'occurred_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('combat_run_activity');
        Schema::dropIfExists('combat_run_participants');
        Schema::table('combat_run_metrics', function (Blueprint $table): void {
            $table->dropIndex('run_metrics_person_idx');
            $table->dropColumn(['participant_type','participant_id']);
        });
        Schema::table('combat_runs', function (Blueprint $table): void {
            $table->dropColumn(['active_seconds','clock_started_at','clock_paused','clock_complete']);
        });
    }
};
