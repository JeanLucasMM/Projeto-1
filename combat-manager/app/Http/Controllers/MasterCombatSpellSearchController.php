<?php

namespace App\Http\Controllers;

use App\Models\Combat;
use App\Models\Spell;
use App\Services\Spells\CampaignSpellAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class MasterCombatSpellSearchController extends Controller
{
    /**
     * Pesquisa somente no catálogo habilitado para a campanha do encontro.
     * Consulta IDs autorizados antes de carregar detalhes, evitando conflitos
     * entre seleções/joins do serviço de acesso e o payload do feitiço.
     */
    public function index(Request $request, CampaignSpellAccessService $access): JsonResponse
    {
        $validated = $request->validate([
            'combat_id' => ['required', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:80'],
        ]);

        $combat = Combat::query()
            ->where('user_id', Auth::id())
            ->findOrFail((int) $validated['combat_id']);

        if ($combat->campaign_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Este combate não possui uma campanha vinculada.',
                'spells' => [],
            ], 422);
        }

        $search = trim((string) ($validated['q'] ?? ''));

        if (mb_strlen($search) < 2) {
            return response()->json(['success' => true, 'spells' => []]);
        }

        try {
            // Reutiliza exatamente a política já usada pelo Grimório.
            // Pesquisar apenas nome/escola/fonte aqui evita percorrer milhares
            // de descrições extensas durante o combate.
            $permittedQuery = $access->allowedSpellQuery((int) $combat->campaign_id);
            $escaped = addcslashes($search, '%_\\');
            $pattern = '%' . $escaped . '%';
            $ids = (clone $permittedQuery)
                ->where(function ($query) use ($pattern): void {
                    $query->where('spells.name', 'like', $pattern)
                        ->orWhere('spells.school', 'like', $pattern)
                        ->orWhere('spells.source', 'like', $pattern);
                })
                ->limit(40)
                ->pluck('spells.id')
                ->unique()
                ->values()
                ->all();

            $spells = Spell::query()
                ->whereIn('id', $ids)
                ->orderBy('level')
                ->orderBy('name')
                ->get([
                    'id', 'name', 'level', 'school', 'casting_time', 'range',
                    'duration', 'ritual', 'concentration', 'description', 'source',
                ])
                ->map(static fn (Spell $spell): array => [
                    'id' => (int) $spell->id,
                    'name' => (string) $spell->name,
                    'level' => (int) $spell->level,
                    'school' => $spell->school,
                    'casting_time' => $spell->casting_time,
                    'range' => $spell->range,
                    'duration' => $spell->duration,
                    'ritual' => (bool) $spell->ritual,
                    'concentration' => (bool) $spell->concentration,
                    'description' => $spell->description,
                    'source' => $spell->source,
                ])
                ->values();

            return response()->json([
                'success' => true,
                'spells' => $spells,
                'message' => $spells->isEmpty()
                    ? 'Nenhum feitiço encontrado entre os livros liberados para esta campanha.'
                    : null,
            ]);
        } catch (Throwable $exception) {
            Log::error('Spellbound: falha na consulta de feitiços do combate', [
                'combat_id' => $combat->id,
                'campaign_id' => $combat->campaign_id,
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'spells' => [],
                'message' => config('app.debug')
                    ? 'Erro na consulta: ' . $exception->getMessage()
                    : 'Não foi possível consultar os feitiços autorizados. Veja storage/logs/laravel.log.',
            ], 500);
        }
    }
}
