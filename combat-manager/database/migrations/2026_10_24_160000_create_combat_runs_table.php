<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combat_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('combat_id')->constrained('combats')->cascadeOnDelete();
            $table->dateTime('started_at')->nullable(); // Combates anteriores à migração: início desconhecido.
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('end_round')->nullable();
            $table->unsignedInteger('end_turn')->nullable();
            $table->timestamps();
            $table->index(['combat_id', 'ended_at']);
        });

        // Combates já ativos não possuem horário de início confiável.
        // Registra-os sem inventar esse horário, mas permite gravar seu encerramento.
        DB::table('combats')->where('is_active', true)->select('id')
            ->orderBy('id')->chunkById(200, function ($activeCombats): void {
                $now = now();
                $rows = $activeCombats->map(fn ($combat) => [
                    'combat_id' => $combat->id,
                    'started_at' => null,
                    'ended_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();
                if ($rows) DB::table('combat_runs')->insert($rows);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('combat_runs');
    }
};
