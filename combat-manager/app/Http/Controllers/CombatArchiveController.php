<?php

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\CombatRun;
use App\Models\CombatRunArchive;
use App\Services\Combats\CombatArchiveIllustrator;
use App\Services\Combats\CombatArchiveSnapshotService;
use App\Services\Combats\CombatPortraitSnapshotService;
use App\Services\Combats\CombatRunClockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CombatArchiveController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        if (mb_strlen($search) > 80) {
            $search = mb_substr($search, 0, 80);
        }
        $archives = CombatRunArchive::query()
            ->whereHas('run.combat', function ($query) use ($search): void {
                $query->where('user_id', Auth::id());
                if ($search !== '') {
                    $query->where('name', 'like', '%' . addcslashes($search, '%_\\') . '%');
                }
            })
            ->with('run.combat.campaign')
            ->orderByDesc('saved_at')
            ->paginate(12)
            ->withQueryString();
        return view('combats.archives', compact('archives', 'search'));
    }

    public function save(Request $request, Combat $combat, CombatArchiveIllustrator $illustrator, CombatArchiveSnapshotService $snapshots): RedirectResponse
    {
        abort_unless((int) $combat->user_id === (int) Auth::id(), 404);
        if ($combat->is_active) return back()->with('error', 'Encerre o combate antes de salvar o resultado.');
        $run = $combat->runs()->whereNotNull('ended_at')->orderByDesc('id')->first();
        if (!$run) return back()->with('error', 'Não há uma execução encerrada para arquivar.');
        if ($run->archive()->exists()) return redirect()->route('master.combat.archives.index');

        // O snapshot é congelado no encerramento, antes de reinícios e edições.
        $snapshot = $run->final_snapshot ?? $snapshots->capture($combat, $run);
        $stats = $this->statistics($run, $snapshot);
        $svg = $illustrator->render($snapshot, $stats);
        DB::transaction(function () use ($run, $snapshot, $stats, $svg): void {
            CombatRunArchive::query()->firstOrCreate(['combat_run_id'=>$run->id], [
                'snapshot'=>$snapshot,'statistics'=>$stats,'image_svg'=>$svg,'saved_at'=>now(),
            ]);
        });
        return redirect()->route('master.combat.archives.index')
            ->with('success', 'Resultado preservado no Histórico de Batalhas.');
    }

    private function statistics(CombatRun $run, array $snapshot): array
    {
        $rows = DB::table('combat_run_metrics')->where('combat_run_id', $run->id)
            ->orderBy('id')->get(['participant_type','participant_id','target_name','target_group','kind','amount','round','turn','created_at']);
        $metrics = []; $events = []; $personal = [];
        $roster = DB::table('combat_run_participants')->where('combat_run_id', $run->id)
            ->orderBy('id')->get();
        foreach ($roster as $person) {
            $key = $person->participant_type.':'.$person->participant_id;
            $personal[$key] = [
                'key'=>$key,'name'=>$person->display_name,'group'=>$person->side,
                'joined_late'=>(bool)$person->joined_late,'joined_known'=>true,'joined_round'=>$person->joined_round,
                'left_at'=>$person->left_at,'damage'=>0,'healing'=>0,
            ];
        }
        foreach ($rows as $item) {
            $kind = $item->kind === 'damage' ? 'damage' : 'healing';
            $group = in_array($item->target_group, ['player','ally','enemy'], true) ? $item->target_group : 'enemy';
            $amount = max(0, (int)$item->amount);
            $metrics[$group][$kind] = ($metrics[$group][$kind] ?? 0) + $amount;
            $key = $item->participant_type && $item->participant_id
                ? $item->participant_type.':'.$item->participant_id : null;
            if ($key !== null) {
                $personal[$key] ??= [
                    'key'=>$key,'name'=>$item->target_name,'group'=>$group,
                    'joined_late'=>false,'joined_known'=>false,'joined_round'=>null,'left_at'=>null,'damage'=>0,'healing'=>0,
                ];
                $personal[$key][$kind] += $amount;
            }
            // Métricas antigas sem identidade permanecem disponíveis no total
            // e no log, sem atribuir arbitrariamente a participantes homônimos.
            $events[] = [
                'kind'=>$kind,'name'=>$item->target_name,'group'=>$group,
                'participant_key'=>$key,'amount'=>$amount,
                'round'=>$item->round,'turn'=>$item->turn,'at'=>$item->created_at,
            ];
        }
        // Arquivos de execuções iniciadas ANTES da migração podem não ter
        // roster inicial. Mostra quem estava presente no snapshot, mas a
        // entrada é "não registrada", jamais assume que estavam desde o começo.
        foreach (array_merge($snapshot['allies'] ?? [], $snapshot['enemies'] ?? []) as $entry) {
            if (!isset($entry['participant_id'])) continue;
            $key = ($entry['participant_type'] ?? $entry['type'] ?? 'npc').':'.$entry['participant_id'];
            $personal[$key] ??= [
                'key'=>$key,'name'=>$entry['name'] ?? 'Participante',
                'group'=>$entry['group'] ?? 'enemy',
                'joined_late'=>false,'joined_known'=>false,'joined_round'=>null,
                'left_at'=>null,'damage'=>0,'healing'=>0,
            ];
        }
        $activity = DB::table('combat_run_activity')->where('combat_run_id', $run->id)
            ->orderBy('occurred_at')->orderBy('id')->get()
            ->map(static fn ($a) => [
                'kind'=>$a->kind,'name'=>$a->participant_name,'group'=>$a->side,
                'round'=>$a->round,'at'=>$a->occurred_at,
            ])->all();
        $complete = $run->started_at && $run->metrics_started_at
            && $run->metrics_started_at->lessThanOrEqualTo($run->started_at);
        return [
            'metrics'=>$metrics,'complete'=>(bool)$complete,
            'events'=>$events,'people'=>array_values($personal),'activity'=>$activity,
            'rounds'=>(int)($run->end_round ?? 0),
            'turns_advanced'=>(int)$run->turns_advanced,
            'duration_seconds'=>$run->active_seconds, // NUNCA diff(started_at, ended_at)
            'duration_complete'=>(bool)$run->clock_complete,
            'turns_complete'=>(bool)$complete,
            'late_count'=>(int)$roster->where('joined_late', true)->count(),
        ];
    }

    /** Exclusão apenas do relatório arquivado: preserva combate, execução, PV e métricas originais. */
    public function destroy(CombatRunArchive $archive): RedirectResponse
    {
        $archive->load('run.combat');
        abort_unless(
            $archive->run && $archive->run->combat
            && (int) $archive->run->combat->user_id === (int) Auth::id(),
            404
        );
        $archive->delete();
        return redirect()->route('master.combat.archives.index')
            ->with('success', 'Registro removido do histórico. O combate original foi preservado.');
    }

    /** Atualiza apenas o título da batalha, sem reiniciar ou modificar a iniciativa. */
    public function rename(Request $request, Combat $combat): RedirectResponse
    {
        abort_unless((int) $combat->user_id === (int) Auth::id(), 404);
        $data = $request->validate(['name' => ['required', 'string', 'min:2', 'max:150']]);
        $combat->name = trim($data['name']);
        $combat->save();
        return redirect()->route('combats.index')->with('success', 'Nome do combate atualizado.');
    }

    public function clock(Request $request, Combat $combat, CombatRunClockService $clock): RedirectResponse
    {
        abort_unless((int)$combat->user_id === (int) Auth::id(), 404);
        abort_unless($combat->is_active, 422);
        $validated = $request->validate(['action'=>['required','in:pause,resume']]);
        $changed = $clock->change((int)$combat->id, $validated['action']);
        return back()->with($changed ? 'success' : 'error', $changed
            ? ($validated['action'] === 'pause' ? 'Cronômetro pausado.' : 'Cronômetro retomado.')
            : 'Não foi possível alterar o cronômetro deste encontro.');
    }

    /** Corrige imagens antigas se a foto original ainda estiver no armazenamento. */
    public function restorePortraits(CombatRunArchive $archive, CombatArchiveIllustrator $illustrator, CombatPortraitSnapshotService $portraits): RedirectResponse
    {
        $archive->load('run.combat');
        abort_unless((int)$archive->run->combat->user_id === (int) Auth::id(), 404);
        $snapshot = $archive->snapshot ?? [];
        $changed = 0;
        foreach (['allies', 'enemies'] as $group) {
            foreach (($snapshot[$group] ?? []) as $i=>$entry) {
                if (!empty($entry['portrait_data'])) continue;
                $original = $entry['image'] ?? null;
                if (!$original && ($entry['participant_type'] ?? $entry['type'] ?? '') === 'player') {
                    $original = $portraits->uniqueCampaignPortrait(
                        $archive->run->combat->campaign_id, (string)($entry['name'] ?? '')
                    );
                }
                $data = $portraits->freeze($original);
                if (!$data) continue;
                $snapshot[$group][$i]['portrait_data'] = $data;
                $changed++;
            }
        }
        if (!$changed) return back()->with('error', 'Nenhuma imagem original disponível para recuperar.');
        $archive->update([
            'snapshot'=>$snapshot,
            'image_svg'=>$illustrator->render($snapshot, $archive->statistics ?? []),
        ]);
        return back()->with('success', "{$changed} retrato(s) recuperado(s) para a imagem arquivada.");
    }

    public function image(CombatRunArchive $archive): Response
    {
        $archive->load('run.combat');
        abort_unless((int)$archive->run->combat->user_id === (int) Auth::id(), 404);
        return response($archive->image_svg, 200, [
            'Content-Type'=>'image/svg+xml; charset=UTF-8',
            'Content-Security-Policy'=>"default-src 'none'; img-src data:; style-src 'unsafe-inline'; sandbox",
            'X-Content-Type-Options'=>'nosniff',
            'Cache-Control'=>'private, max-age=0, no-store',
            'Content-Disposition'=>request()->boolean('download')
                ? 'attachment; filename="spellbound-combate-'.$archive->id.'.svg"' : 'inline',
        ]);
    }
}
