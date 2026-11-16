<?php

namespace App\Services\Combats;

use App\Models\CombatRun;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/** Cronômetro por trechos ativos; só pausa mediante ação explícita do mestre. */
class CombatRunClockService
{
    public function change(int $combatId, string $action): bool
    {
        return DB::transaction(function () use ($combatId, $action): bool {
            $run = CombatRun::query()->where('combat_id', $combatId)
                ->whereNull('ended_at')->orderByDesc('id')->lockForUpdate()->first();
            if (!$run) return false;
            if ($run->active_seconds === null && $action === 'resume') {
                // Encontro ativo de antes da migração: começa daqui, sem inventar tempo anterior.
                $run->active_seconds = 0;
                $run->clock_paused = true;
            }
            if ($run->active_seconds === null) return false;
            $now = now();
            if ($action === 'pause' && !$run->clock_paused) {
                $this->accrue($run, $now);
                $run->clock_paused = true;
                $run->clock_started_at = null;
                $this->activity($run, 'pause', $now);
            } elseif ($action === 'resume' && $run->clock_paused) {
                $run->clock_paused = false;
                $run->clock_started_at = $now;
                $this->activity($run, 'resume', $now);
            } elseif ($action === 'finish' && $run->clock_started_at && !$run->clock_paused) {
                $this->accrue($run, $now);
                $run->clock_started_at = null;
                $run->clock_paused = true;
                $this->activity($run, 'finish', $now);
            } else {
                return false;
            }
            $run->save();
            return true;
        });
    }

    private function accrue(CombatRun $run, Carbon $now): void
    {
        if ($run->clock_started_at) {
            $elapsed = max(0, (int) $run->clock_started_at->diffInSeconds($now));
            $run->active_seconds = (int) $run->active_seconds + $elapsed;
        }
    }

    private function activity(CombatRun $run, string $kind, Carbon $now): void
    {
        DB::table('combat_run_activity')->insert([
            'combat_run_id' => $run->id, 'kind' => $kind, 'round' => null,
            'occurred_at' => $now,
        ]);
    }
}
