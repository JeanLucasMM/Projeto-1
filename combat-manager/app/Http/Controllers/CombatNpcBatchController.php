<?php

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\CombatNpc;
use App\Services\CombatNpcService;
use App\Services\NpcService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Adiciona participantes NPC em lote preservando a lógica já usada
 * pelo CombatNpcService. "side" pertence à instância do combate,
 * nunca ao cadastro original do NPC.
 */
class CombatNpcBatchController extends Controller
{
    public function __construct(
        private CombatNpcService $combatNpcService,
        private NpcService $npcService,
    ) {
    }

    public function store(Request $request, Combat $combat): RedirectResponse
    {
        abort_unless((int) $combat->user_id === (int) Auth::id(), 404);

        $validated = $request->validate([
            'npcs' => ['required', 'array', 'min:1', 'max:60'],
            'npcs.*.npc_id' => ['required', 'integer', 'distinct', 'exists:npcs,id'],
            'npcs.*.side' => ['required', Rule::in([CombatNpc::SIDE_ALLY, CombatNpc::SIDE_ENEMY])],
            'npcs.*.quantity' => ['required', 'integer', 'between:1,8'],
            'allow_duplicates' => ['nullable', 'boolean'],
        ]);

        $entries = array_values($validated['npcs']);
        $requestedCount = array_sum(array_map(
            static fn (array $entry): int => (int) $entry['quantity'],
            $entries
        ));

        if ($requestedCount > 80) {
            throw ValidationException::withMessages([
                'npcs' => 'Você pode adicionar até 80 criaturas por operação.',
            ]);
        }

        // Verificamos a propriedade ANTES de qualquer gravação. Uma ID existente
        // no banco não significa que o mestre possa convocar essa criatura.
        $npcsById = [];
        foreach ($entries as $entry) {
            $npc = $this->npcService->findByIdAndUser(
                (int) $entry['npc_id'],
                (int) Auth::id()
            );
            abort_unless($npc, 404);
            $npcsById[(int) $entry['npc_id']] = $npc;
        }

        $allowDuplicates = $request->boolean('allow_duplicates');
        $added = 0;
        $allies = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $combat, $entries, $npcsById, $allowDuplicates,
            &$added, &$allies, &$skipped
        ): void {
            // Serializa inserções da ação em lote deste mesmo combate.
            Combat::query()->whereKey($combat->id)->lockForUpdate()->firstOrFail();

            foreach ($entries as $entry) {
                $npc = $npcsById[(int) $entry['npc_id']];
                $side = $entry['side'];
                $quantity = (int) $entry['quantity'];

                if (!$allowDuplicates && $this->combatNpcService->alreadyExists($combat, $npc)) {
                    $skipped += $quantity;
                    continue;
                }

                for ($i = 0; $i < $quantity; $i++) {
                    $previousId = (int) ($combat->npcs()->max('id') ?? 0);
                    // Serviço original mantém os cálculos de PV, iniciativa e recursos.
                    $this->combatNpcService->addNpc($combat, $npc);

                    $created = $combat->npcs()
                        ->where('npc_id', $npc->id)
                        ->where('id', '>', $previousId)
                        ->orderByDesc('id')
                        ->firstOrFail();

                    $created->side = $side;
                    $created->save();

                    $added++;
                    if ($side === CombatNpc::SIDE_ALLY) {
                        $allies++;
                    }
                }
            }
        });

        $message = $added === 0
            ? 'Nenhuma criatura nova adicionada.'
            : "{$added} criatura(s) adicionada(s), sendo {$allies} aliada(s).";

        if ($skipped > 0) {
            $message .= " {$skipped} ignorada(s) porque já estavam no combate."
                . ' Marque “Permitir cópias” para inseri-las novamente.';
        }

        return redirect()
            ->route('combats.show', $combat)
            ->with($added > 0 ? 'success' : 'error', $message);
    }
}
