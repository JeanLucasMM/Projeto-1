<?php

namespace App\Http\Controllers;

use App\Services\CombatNpcService;
use App\Services\CombatService;
use App\Services\NpcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Adicionado para registrar os eventos no banco
use App\Services\CombatPlayerService;
use App\Services\CombatInitiativeService;
use App\Factories\NpcViewModelFactory;
use App\Models\CombatNpc;



class CombatController extends Controller
{
    public function __construct(
        private CombatService $combatService,
        private NpcService $npcService,
        private CombatNpcService $combatNpcService,
        private CombatPlayerService $combatPlayerService,
        private CombatInitiativeService $combatInitiativeService,
        private NpcViewModelFactory $npcViewModelFactory
    ) {
    }

    /**
     * Lista todos os combates do usuário.
     */
    public function index()
    {
        $combats = $this->combatService
            ->getAllByUser(Auth::id());

        return view('combats.index', compact('combats'));
    }

    /**
     * Formulário para criar combate.
     */
    public function create()
    {
        return view('combats.create');
    }

    /**
     * Salva um novo combate.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100'
            ],
        ]);

        $combat = $this->combatService->create(
            Auth::id(),
            $request->name
        );

        return redirect()
            ->route('combats.show', $combat)
            ->with('success', 'Combate criado com sucesso.');
    }

    /**
     * Exibe um combate.
     */
    public function show(int $id)
    {
        $combat = $this->combatService->findById($id);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $npcs = $this->npcService->getAllByUser(Auth::id());

        $combatNpcs = $this->combatNpcService
            ->getByCombat($combat->id);

            foreach ($combatNpcs as $combatNpc) {

            $combatNpc->viewModel = $this->npcViewModelFactory
                ->make($combatNpc->npc);

}

        $combatPlayers = $this->combatPlayerService
            ->getByCombat($combat->id);

        $initiative = $this->combatInitiativeService
            ->participants($combat);

        return view('combats.show', compact(
            'combat',
            'npcs',
            'combatNpcs',
            'combatPlayers',
            'initiative'
        ));
    }

    /**
     * Exclui um combate.
     */
    public function destroy(int $id)
    {
        $combat = $this->combatService
            ->findById($id);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $this->combatService->delete($combat);

        return redirect()
            ->route('combats.index')
            ->with('success', 'Combate removido.');
    }

    /**
     * Inicia o combate ativo e gera o evento inicial.
     */
    public function start(int $combatId)
    {
        $combat = $this->combatService->findById($combatId);

        abort_if(!$combat || $combat->user_id !== Auth::id(), 404);

        $this->combatService->startCombat($combat);

        // Registro do evento de início de combate
        $this->logCombatEvent($combat->id, 1, 0, 'O combate foi iniciado!');

        return back()->with('success', 'O combate começou!');
    }

    /**
     * Reseta o progresso do combate e limpa o histórico de eventos se necessário.
     */
    public function reset(int $combatId)
    {
        $combat = $this->combatService->findById($combatId);

        abort_if(!$combat || $combat->user_id !== Auth::id(), 404);

        $this->combatService->resetCombat($combat);

        // Registro do evento de reset do combate
        $this->logCombatEvent($combat->id, 1, 0, 'O progresso do combate foi reiniciado.');

        return back()->with('success', 'Combate reiniciado.');
    }

    /**
     * Passa o turno e calcula se uma nova rodada se iniciou.
     */
    public function next(int $combatId)
    {
        $combat = $this->combatService->findById($combatId);

        abort_if(!$combat || $combat->user_id !== Auth::id(), 404);

        $participants = $this->combatInitiativeService->participants($combat);
        $participantsCount = $participants->count();

        if ($participantsCount === 0) {
            return back()->with('error', 'Não há participantes ativos para avançar o turno.');
        }

        // Descobre quem é o participante que está encerrando o turno atual
        $currentActor = $participants->values()->get($combat->current_turn);
        
        // Tratamento seguro para ler o nome do DTO (Objeto) ou Array
        $actorName = 'Desconhecido';
        if ($currentActor) {
            $actorName = is_array($currentActor) 
                ? ($currentActor['name'] ?? 'Desconhecido') 
                : ($currentActor->name ?? 'Desconhecido');
        }

        // Avança o turno usando o service
        $this->combatService->nextTurn($combat, $participantsCount);

        // Registra o evento de fim de turno
        $this->logCombatEvent(
            $combat->id, 
            $combat->current_round, 
            $combat->current_turn, 
            "Turno de {$actorName} finalizado. Rodada {$combat->current_round}."
        );

        return back();
    }

    /**
     * Adiciona um NPC ao combate.
     */
    public function addNpc(Request $request, int $combatId)
    {
        $request->validate([
            'npc_id' => ['required', 'exists:npcs,id'],
            'force' => ['nullable', 'boolean'],
        ]);

        $combat = $this->combatService->findById($combatId);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $npc = $this->npcService->findByIdAndUser(
            $request->npc_id,
            Auth::id()
        );

        abort_if(!$npc, 404);

        if (
            !$request->boolean('force') &&
            $this->combatNpcService->alreadyExists($combat, $npc)
        ) {
            return back()->with([
                'duplicateNpc' => [
                    'combat' => $combat->id,
                    'npc' => $npc->id,
                    'name' => $npc->name,
                ]
            ]);
        }

        try {
            $this->combatNpcService->addNpc($combat, $npc);
        } catch (\Exception $e) {
            return back()->with(
                'error',
                $e->getMessage()
            );
        }

        return back()->with(
            'success',
            "NPC '{$npc->name}' adicionado ao combate."
        );
    }

    public function removeNpc(int $combatId, int $combatNpcId)
    {
        $combat = $this->combatService->findById($combatId);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $combatNpc = $this->combatNpcService
            ->findById($combatNpcId);

        abort_if(
            !$combatNpc ||
            $combatNpc->combat_id !== $combat->id,
            404
        );

        $this->combatNpcService->remove($combatNpc);

        return back()->with(
            'success',
            'NPC removido do combate.'
        );
    }

    public function addPlayer(Request $request, int $combatId)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'initiative' => ['required', 'integer'],
        ]);

        $combat = $this->combatService->findById($combatId);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $this->combatPlayerService->addPlayer(
            $combat,
            $request->name,
            $request->initiative
        );

        return back()->with(
            'success',
            'Jogador adicionado.'
        );
    }

    public function removePlayer(int $combatId, int $player)
    {
        $combat = $this->combatService->findById($combatId);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $combatPlayer = $this->combatPlayerService->find($player);

        abort_if(
            !$combatPlayer ||
            $combatPlayer->combat_id != $combat->id,
            404
        );

        $this->combatPlayerService->remove($combatPlayer);

        return back()->with(
            'success',
            'Jogador removido.'
        );
    }

    public function updateInitiative(Request $request, int $combatId, int $combatNpcId)
    {
        $request->validate([
            'initiative' => [
                'required',
                'integer',
                'between:-20,99'
            ]
        ]);

        $combat = $this->combatService->findById($combatId);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $combatNpc = $this->combatNpcService
            ->findById($combatNpcId);

        abort_if(
            !$combatNpc ||
            $combatNpc->combat_id !== $combat->id,
            404
        );

        $this->combatNpcService->setInitiative(
            $combatNpc,
            $request->initiative
        );

        return back()->with(
            'success',
            'Iniciativa atualizada.'
        );
    }

    public function updatePlayerInitiative(Request $request, int $combatId, int $player)
    {
        $request->validate([
            'initiative' => ['required', 'integer'],
        ]);

        $combat = $this->combatService->findById($combatId);

        abort_if(
            !$combat ||
            $combat->user_id !== Auth::id(),
            404
        );

        $combatPlayer = $this->combatPlayerService->find($player);

        abort_if(
            !$combatPlayer ||
            $combatPlayer->combat_id != $combat->id,
            404
        );

        $this->combatPlayerService->setInitiative(
            $combatPlayer,
            $request->initiative
        );

        return back();
    }

    /**
     * Grava os eventos do combate na tabela correspondente.
     */
private function logCombatEvent(int $combatId, int $round, int $turn, string $description): void
    {
        // Alterado de 'combat_events' para 'combat_logs'
        DB::table('combat_logs')->insert([
            'combat_id'   => $combatId,
            'round'       => $round,
            'turn'        => $turn,
            'description' => $description,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function damageNpc(
    Request $request,
    int $combatId,
    int $combatNpcId
)
{
    $request->validate([
        'damage' => [
            'required',
            'integer',
            'min:1'
        ]
    ]);

    $combat = $this->combatService->findById($combatId);

    abort_if(
        !$combat ||
        $combat->user_id != Auth::id(),
        404
    );

    $combatNpc = $this->combatNpcService
        ->findById($combatNpcId);

    abort_if(
        !$combatNpc ||
        $combatNpc->combat_id != $combat->id,
        404
    );

    $this->combatNpcService->damage(
        $combatNpc,
        $request->damage
    );

    $this->logCombatEvent(
        $combat->id,
        $combat->current_round,
        $combat->current_turn,
        "{$combatNpc->npc->name} sofreu {$request->damage} de dano."
    );

    return back();
}
public function healNpc(
    Request $request,
    int $combatId,
    int $combatNpcId
)
{
    $request->validate([
        'heal' => [
            'required',
            'integer',
            'min:1'
        ]
    ]);

    $combat = $this->combatService->findById($combatId);

    abort_if(
        !$combat ||
        $combat->user_id != Auth::id(),
        404
    );

    $combatNpc = $this->combatNpcService
        ->findById($combatNpcId);

    abort_if(
        !$combatNpc ||
        $combatNpc->combat_id != $combat->id,
        404
    );

    $this->combatNpcService->heal(
        $combatNpc,
        $request->heal
    );

    $this->logCombatEvent(
        $combat->id,
        $combat->current_round,
        $combat->current_turn,
        "{$combatNpc->npc->name} recuperou {$request->heal} HP."
    );

    return back();
}
public function updateResource(Request $request)
{
    $request->validate([
        'combat_npc_id' => ['required', 'exists:combat_npcs,id'],
        'feature_title' => ['required', 'string'],
        'current_uses'  => ['required', 'integer', 'min:0'],
    ]);

    $combatNpc = CombatNpc::findOrFail($request->combat_npc_id);

    $combatNpc->setResource(
        $request->feature_title,
        $request->current_uses
    );

    return response()->json([
        'success' => true,
    ]);
}

public function temporaryHp(
    Request $request,
    int $combatId,
    int $combatNpcId
)
{
    $request->validate([
        'temporary_hp' => [
            'required',
            'integer',
            'min:0'
        ]
    ]);

    $combat = $this->combatService->findById($combatId);

    abort_if(
        !$combat ||
        $combat->user_id != Auth::id(),
        404
    );

    $combatNpc = $this->combatNpcService
        ->findById($combatNpcId);

    abort_if(
        !$combatNpc ||
        $combatNpc->combat_id != $combat->id,
        404
    );

    $this->combatNpcService->setTemporaryHp(
        $combatNpc,
        $request->temporary_hp
    );

    $this->logCombatEvent(
        $combat->id,
        $combat->current_round,
        $combat->current_turn,
        "{$combatNpc->npc->name} agora possui {$request->temporary_hp} HP Temporário."
    );

    return back();
}
}