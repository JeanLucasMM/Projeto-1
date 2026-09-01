<?php

namespace App\Http\Controllers;

use App\Services\CombatNpcService;
use App\Services\CombatService;
use App\Services\NpcService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Adicionado para registrar os eventos no banco
use App\Services\CombatPlayerService;
use App\Services\CombatInitiativeService;
use App\Factories\NpcViewModelFactory;
use App\Models\Combat;
use App\Models\CombatNpc;
use App\Models\Campaign;
use App\Models\Character;



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
            ->getAllByUser(
                Auth::id()
            );

        /*
        |--------------------------------------------------------------------------
        | Relações usadas diretamente pelos cards do index
        |--------------------------------------------------------------------------
        */

        $combats->loadMissing([
            'campaign',
            'npcs.npc',
            'players.character',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Campanhas disponíveis no modal "Novo Combate"
        |--------------------------------------------------------------------------
        |
        | Apenas campanhas das quais o usuário autenticado é Mestre.
        |
        */

        $campaigns =
            Campaign::query()
                ->where(
                    'owner_user_id',
                    Auth::id()
                )
                ->orderBy(
                    'name'
                )
                ->get();

        return view(
            'combats.index',
            compact(
                'combats',
                'campaigns'
            )
        );
    }

    /**
     * Formulário para criar combate.
     */
    public function create(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | A criação real acontece no modal de combats.index.
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('combats.index');
    }

    /**
     * Salva um novo combate.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'campaign_id' => [
                'nullable',
                'integer',
                'exists:campaigns,id',
            ],
        ]);

        $campaignId =
            isset(
                $validated['campaign_id']
            )
                ? (int) $validated[
                    'campaign_id'
                ]
                : null;

        if ($campaignId !== null) {
            $campaignExists =
                Campaign::query()
                    ->whereKey(
                        $campaignId
                    )
                    ->where(
                        'owner_user_id',
                        Auth::id()
                    )
                    ->exists();

            abort_unless(
                $campaignExists,
                404
            );
        }

        $combat = $this->combatService->create(
            Auth::id(),
            $validated['name'],
            $campaignId
        );

        return redirect()
            ->route(
                'combats.show',
                $combat
            )
            ->with(
                'success',
                'Combate criado com sucesso.'
            );
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

        /*
        |--------------------------------------------------------------------------
        | Iniciativa das fichas vinculadas
        |--------------------------------------------------------------------------
        |
        | A Character salva o resultado da rolagem em:
        |
        | character_combat.overrides.quick_stats.initiative.combat_value
        |
        | Antes de montar a tela, trazemos esse valor para CombatPlayer.
        |
        */

        $this->syncLinkedPlayerInitiatives(
            $combat
        );

        $combatPlayers = $this->combatPlayerService
            ->getByCombat($combat->id);

        /*
        |--------------------------------------------------------------------------
        | Characters disponíveis da campanha
        |--------------------------------------------------------------------------
        |
        | Só aparecem fichas explicitamente compartilhadas com a campanha.
        | Fichas já presentes no combate são removidas do seletor.
        |
        */

        $availableCharacters =
            collect();

        if ($combat->campaign_id !== null) {
            $campaign =
                Campaign::query()
                    ->whereKey(
                        $combat->campaign_id
                    )
                    ->where(
                        'owner_user_id',
                        Auth::id()
                    )
                    ->first();

            abort_unless(
                $campaign,
                404
            );

            $usedCharacterIds =
                $combatPlayers
                    ->pluck(
                        'character_id'
                    )
                    ->filter()
                    ->map(
                        fn ($id) =>
                            (int) $id
                    )
                    ->values();

            $availableCharacters =
                $campaign
                    ->characters()
                    ->wherePivot(
                        'is_active',
                        true
                    )
                    ->whereNotIn(
                        'characters.id',
                        $usedCharacterIds
                    )
                    ->with([
                        'combat',
                        'classes',
                        'user',
                    ])
                    ->orderBy(
                        'characters.name'
                    )
                    ->get();

            $combat->setRelation(
                'campaign',
                $campaign
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Campanhas do Mestre
        |--------------------------------------------------------------------------
        |
        | Usadas no modal +Jogador quando um combate antigo ainda está
        | independente e precisa ser vinculado sem recriar o encontro.
        |
        */

        $campaigns =
            Campaign::query()
                ->where(
                    'owner_user_id',
                    Auth::id()
                )
                ->orderBy(
                    'name'
                )
                ->get();

        $initiative = $this->combatInitiativeService
            ->participants($combat);

        return view('combats.show', compact(
            'combat',
            'npcs',
            'combatNpcs',
            'combatPlayers',
            'availableCharacters',
            'campaigns',
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

        /*
        | Garante que as últimas rolagens dos Players estejam gravadas
        | antes de congelar a ordem inicial do combate.
        */
        $this->syncLinkedPlayerInitiatives(
            $combat
        );

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

        $this->syncLinkedPlayerInitiatives(
            $combat
        );

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

    /*
    |--------------------------------------------------------------------------
    | Vincular campanha ao combate
    |--------------------------------------------------------------------------
    |
    | Permite corrigir combates independentes já existentes.
    | Não permitimos trocar a campanha se já existem Characters vinculadas.
    |
    */

    public function updateCampaign(
        Request $request,
        int $combatId
    ) {
        $validated = $request->validate([
            'campaign_id' => [
                'required',
                'integer',
                'exists:campaigns,id',
            ],
        ]);

        $combat = $this->combatService
            ->findById(
                $combatId
            );

        abort_if(
            !$combat
            || (int) $combat->user_id
                !== (int) Auth::id(),
            404
        );

        $campaign = Campaign::query()
            ->whereKey(
                (int) $validated['campaign_id']
            )
            ->where(
                'owner_user_id',
                Auth::id()
            )
            ->first();

        abort_unless(
            $campaign,
            404
        );

        $hasLinkedCharacters = $combat
            ->players()
            ->whereNotNull(
                'character_id'
            )
            ->exists();

        if (
            $combat->campaign_id !== null
            && (int) $combat->campaign_id
                !== (int) $campaign->id
            && $hasLinkedCharacters
        ) {
            return back()->with(
                'error',
                'Remova as fichas vinculadas antes de trocar a campanha deste combate.'
            );
        }

        $combat->campaign_id =
            $campaign->id;

        $this->combatService
            ->save(
                $combat
            );

        return redirect()
            ->route(
                'combats.show',
                [
                    'combat' =>
                        $combat->id,

                    'player_modal' =>
                        1,
                ]
            )
            ->with(
                'success',
                "Combate vinculado à campanha '{$campaign->name}'."
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Estado em tempo real dos Players vinculados
    |--------------------------------------------------------------------------
    |
    | Retorna somente estado de combate das Characters já adicionadas.
    | A fonte de verdade continua sendo character_combat.
    |
    */

    public function playerStates(
        int $combatId
    ): JsonResponse {
        $combat =
            $this->combatService
                ->findById(
                    $combatId
                );

        abort_if(
            !$combat
            || (int) $combat->user_id
                !== (int) Auth::id(),
            404
        );

        /*
        | O painel consulta este endpoint a cada segundo.
        | Aproveitamos a mesma chamada para consumir a iniciativa que o
        | Player rolou na própria ficha.
        */
        $this->syncLinkedPlayerInitiatives(
            $combat
        );

        $players =
            $combat
                ->players()
                ->with([
                    'character.combat',
                ])
                ->whereNotNull(
                    'character_id'
                )
                ->get();

        $states =
            $players
                ->mapWithKeys(
                    function ($player): array {
                        $character =
                            $player->character;

                        $characterCombat =
                            $character?->combat;

                        if (!$characterCombat) {
                            return [
                                (string) $player->id => [
                                    'combat_player_id' =>
                                        (int) $player->id,

                                    'character_id' =>
                                        $character
                                            ? (int) $character->id
                                            : null,

                                    'has_combat' =>
                                        false,

                                    'initiative' =>
                                        (int) $player->initiative,
                                ],
                            ];
                        }

                        $maxHp =
                            max(
                                1,
                                (int) $characterCombat->max_hp
                                +
                                (int) $characterCombat->temporary_max_hp
                            );

                        $currentHp =
                            max(
                                0,
                                (int) $characterCombat->current_hp
                            );

                        $temporaryHp =
                            max(
                                0,
                                (int) $characterCombat->temporary_hp
                            );

                        $percent =
                            max(
                                0,
                                min(
                                    100,
                                    (
                                        $currentHp
                                        /
                                        $maxHp
                                    )
                                    * 100
                                )
                            );

                        return [
                            (string) $player->id => [
                                'combat_player_id' =>
                                    (int) $player->id,

                                'character_id' =>
                                    (int) $character->id,

                                'has_combat' =>
                                    true,

                                'current_hp' =>
                                    $currentHp,

                                'max_hp' =>
                                    $maxHp,

                                'temporary_hp' =>
                                    $temporaryHp,

                                'percent' =>
                                    round(
                                        $percent,
                                        2
                                    ),

                                'at_zero_hp' =>
                                    $currentHp <= 0,

                                'initiative' =>
                                    (int) $player->initiative,
                            ],
                        ];
                    }
                );

        return response()->json([
            'success' =>
                true,

            'players' =>
                $states->all(),
        ]);
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

    /*
    |--------------------------------------------------------------------------
    | Adiciona Character compartilhada pela campanha
    |--------------------------------------------------------------------------
    */

    public function addCharacter(
        Request $request,
        int $combatId
    ) {
        $validated = $request->validate([
            'character_id' => [
                'required',
                'integer',
                'exists:characters,id',
            ],

            'initiative' => [
                'nullable',
                'integer',
                'between:-20,99',
            ],
        ]);

        $combat =
            $this->combatService
                ->findById(
                    $combatId
                );

        abort_if(
            !$combat
            || (int) $combat->user_id
                !== (int) Auth::id(),
            404
        );

        if ($combat->campaign_id === null) {
            return back()->with(
                'error',
                'Este combate não está vinculado a uma campanha.'
            );
        }

        $campaign =
            Campaign::query()
                ->whereKey(
                    $combat->campaign_id
                )
                ->where(
                    'owner_user_id',
                    Auth::id()
                )
                ->first();

        abort_unless(
            $campaign,
            404
        );

        /*
        | A Character precisa estar explicitamente compartilhada e ativa
        | nesta campanha. Não basta pertencer a algum membro.
        */
        $character =
            $campaign
                ->characters()
                ->where(
                    'characters.id',
                    (int) $validated[
                        'character_id'
                    ]
                )
                ->wherePivot(
                    'is_active',
                    true
                )
                ->with([
                    'combat',
                    'classes',
                    'user',
                ])
                ->first();

        abort_unless(
            $character,
            404
        );

        if (
            $this
                ->combatPlayerService
                ->characterAlreadyExists(
                    $combat,
                    $character
                )
        ) {
            return back()->with(
                'error',
                'Este personagem já está no combate.'
            );
        }

        $this->combatPlayerService
            ->addCharacter(
                $combat,
                $character,
                (int) (
                    $validated[
                        'initiative'
                    ]
                    ?? 0
                )
            );

        return back()->with(
            'success',
            "Personagem '{$character->name}' adicionado ao combate."
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

        $initiative =
            (int) $request->initiative;

        $this->combatPlayerService->setInitiative(
            $combatPlayer,
            $initiative
        );

        /*
        |--------------------------------------------------------------------------
        | Sincronização inversa
        |--------------------------------------------------------------------------
        |
        | Se o Mestre corrigir a iniciativa pelo Combat Manager, mantemos
        | quick_stats.initiative.combat_value coerente na ficha real.
        |
        */

        $this->syncCombatPlayerInitiativeToCharacter(
            $combatPlayer,
            $initiative
        );

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | SINCRONIZAÇÃO DE INICIATIVA — CHARACTER -> COMBAT PLAYER
    |--------------------------------------------------------------------------
    |
    | A ficha do Player já possui um sistema próprio para rolar iniciativa.
    | O resultado fica em:
    |
    | character_combat.overrides.quick_stats.initiative.combat_value
    |
    | CombatPlayer continua guardando a iniciativa usada pela fila do combate.
    | Este método conecta as duas camadas.
    |
    */

    private function syncLinkedPlayerInitiatives(
        Combat $combat
    ): bool {
        $changed =
            false;

        $players =
            $combat
                ->players()
                ->with([
                    'character.combat',
                ])
                ->whereNotNull(
                    'character_id'
                )
                ->get();

        foreach ($players as $player) {
            $state =
                $this->characterInitiativeState(
                    $player
                );

            /*
            | Character antiga ou ainda sem o namespace quick_stats:
            | não sobrescreve a iniciativa manual existente.
            */
            if (!$state['exists']) {
                continue;
            }

            $initiative =
                $state['value']
                ?? 0;

            if (
                (int) $player->initiative
                === (int) $initiative
            ) {
                continue;
            }

            $player->initiative =
                (int) $initiative;

            $player->save();

            $changed =
                true;
        }

        return $changed;
    }


    private function characterInitiativeState(
        $combatPlayer
    ): array {
        $characterCombat =
            $combatPlayer
                ->character
                ?->combat;

        if (!$characterCombat) {
            return [
                'exists' =>
                    false,

                'value' =>
                    null,
            ];
        }

        $overrides =
            $characterCombat->overrides
            ?? [];

        if (is_string($overrides)) {
            $decoded =
                json_decode(
                    $overrides,
                    true
                );

            $overrides =
                is_array($decoded)
                    ? $decoded
                    : [];
        }

        if (!is_array($overrides)) {
            $overrides =
                [];
        }

        $path =
            'quick_stats.initiative.combat_value';

        if (!Arr::has(
            $overrides,
            $path
        )) {
            return [
                'exists' =>
                    false,

                'value' =>
                    null,
            ];
        }

        $value =
            data_get(
                $overrides,
                $path
            );

        return [
            'exists' =>
                true,

            'value' =>
                $value === null
                    || $value === ''
                        ? null
                        : (int) $value,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | SINCRONIZAÇÃO INVERSA — COMBAT PLAYER -> CHARACTER
    |--------------------------------------------------------------------------
    */

    private function syncCombatPlayerInitiativeToCharacter(
        $combatPlayer,
        int $initiative
    ): void {
        $characterCombat =
            $combatPlayer
                ->character
                ?->combat;

        if (!$characterCombat) {
            return;
        }

        $overrides =
            $characterCombat->overrides
            ?? [];

        if (is_string($overrides)) {
            $decoded =
                json_decode(
                    $overrides,
                    true
                );

            $overrides =
                is_array($decoded)
                    ? $decoded
                    : [];
        }

        if (!is_array($overrides)) {
            $overrides =
                [];
        }

        data_set(
            $overrides,
            'quick_stats.initiative.combat_value',
            $initiative
        );

        $casts =
            method_exists(
                $characterCombat,
                'getCasts'
            )
                ? $characterCombat->getCasts()
                : [];

        $characterCombat->overrides =
            array_key_exists(
                'overrides',
                $casts
            )
                ? $overrides
                : json_encode(
                    $overrides,
                    JSON_UNESCAPED_UNICODE
                );

        $characterCombat->save();
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
    $validated = $request->validate([
        'combat_npc_id' => [
            'required',
            'exists:combat_npcs,id',
        ],

        'feature_title' => [
            'required',
            'string',
        ],

        'current_uses' => [
            'required',
            'integer',
            'min:0',
        ],
    ]);

    $combatNpc =
        CombatNpc::findOrFail(
            $validated[
                'combat_npc_id'
            ]
        );

    $combat =
        $this->combatService
            ->findById(
                (int) $combatNpc->combat_id
            );

    abort_if(
        !$combat
        || (int) $combat->user_id
            !== (int) Auth::id(),
        404
    );

    $combatNpc->setResource(
        $validated[
            'feature_title'
        ],
        (int) $validated[
            'current_uses'
        ]
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