<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\CharacterItem;
use App\Models\CharacterPartyNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class CharacterPartyController extends Controller
{
    public function index(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $campaigns = $this->activeCampaigns($character);

        $campaign = $this->resolveSelectedCampaign(
            $campaigns,
            $request->integer('campaign_id')
        );

        if (!$campaign) {
            return response()->json([
                'success' => true,
                'campaigns' => [],
                'campaign' => null,
                'members' => [],
                'pages' => [
                    [
                        'id' => 'page-1',
                        'content' => '',
                    ],
                ],
                'items' => $this->itemsPayload($character),
            ]);
        }

        $note = CharacterPartyNote::query()->firstOrNew([
            'campaign_id' => $campaign->id,
            'character_id' => $character->id,
        ]);

        return response()->json([
            'success' => true,
            'campaigns' => $campaigns
                ->map(fn (Campaign $campaign) => [
                    'id' => (int) $campaign->id,
                    'name' => $campaign->name,
                ])
                ->values()
                ->all(),
            'campaign' => [
                'id' => (int) $campaign->id,
                'name' => $campaign->name,
            ],
            'members' => $this->memberPayloads($campaign, $character),
            'pages' => $this->diaryPages($note),
            'items' => $this->itemsPayload($character),
        ]);
    }

    public function states(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $campaigns = $this->activeCampaigns($character);

        $campaign = $this->resolveSelectedCampaign(
            $campaigns,
            $request->integer('campaign_id')
        );

        if (!$campaign) {
            return response()->json([
                'success' => true,
                'campaign_id' => null,
                'members' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'campaign_id' => (int) $campaign->id,
            'members' => collect(
                $this->memberPayloads($campaign, $character)
            )
                ->mapWithKeys(fn (array $member) => [
                    (string) $member['id'] => [
                        'current_hp' => $member['current_hp'],
                        'max_hp' => $member['max_hp'],
                        'temporary_hp' => $member['temporary_hp'],
                        'hp_percent' => $member['hp_percent'],
                        'health_state' => $member['health_state'],
                        'exhaustion' => $member['exhaustion'],
                        'exhaustion_enabled' => $member['exhaustion_enabled'],
                    ],
                ])
                ->all(),
        ]);
    }

    public function memberImage(
        Character $character,
        Campaign $campaign,
        Character $member
    ): BinaryFileResponse {
        Gate::authorize(
            'update',
            $character
        );

        $this->assertCharacterInCampaign(
            $character,
            $campaign
        );

        $this->assertCharacterInCampaign(
            $member,
            $campaign
        );

        abort_if(
            (int) $member->id === (int) $character->id,
            404
        );

        $path = is_string($member->image_path)
            ? trim($member->image_path)
            : '';

        abort_if(
            $path === '',
            404
        );

        $path = str_replace(
            '\\',
            '/',
            $path
        );

        $path = preg_replace(
            '#^/?storage/#',
            '',
            $path
        ) ?? $path;

        $path = ltrim(
            $path,
            '/'
        );

        abort_if(
            $path === ''
            || str_contains($path, '../')
            || str_contains($path, '..\\'),
            404
        );

        $fullPath = storage_path(
            'app/public/' . $path
        );

        abort_unless(
            is_file($fullPath),
            404
        );

        return response()->file(
            $fullPath,
            [
                'Cache-Control' =>
                    'private, no-cache, no-store, must-revalidate',
                'Pragma' =>
                    'no-cache',
                'Expires' =>
                    '0',
                'X-Content-Type-Options' =>
                    'nosniff',
            ]
        );
    }


    public function updateNotes(
        Request $request,
        Character $character,
        Campaign $campaign
    ): JsonResponse {
        Gate::authorize(
            'update',
            $character
        );

        $this->assertCharacterInCampaign(
            $character,
            $campaign
        );

        $validated = $request->validate([
            'pages' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'pages.*.id' => [
                'required',
                'string',
                'max:80',
            ],
            'pages.*.content' => [
                'nullable',
                'string',
                'max:20000',
            ],
        ]);

        $pages = collect($validated['pages'])
            ->map(fn (array $page) => [
                'id' => trim((string) $page['id']),
                'content' => (string) ($page['content'] ?? ''),
            ])
            ->values()
            ->all();

        $serialized = json_encode(
            [
                'version' => 2,
                'pages' => $pages,
            ],
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );

        $note = CharacterPartyNote::query()
            ->updateOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'character_id' => $character->id,
                ],
                [
                    'notes' => $serialized,
                ]
            );

        return response()->json([
            'success' => true,
            'pages' => $this->diaryPages($note),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CUTUCAR
    |--------------------------------------------------------------------------
    */

    public function sendPoke(
        Request $request,
        Character $character,
        Campaign $campaign
    ): JsonResponse {
        Gate::authorize(
            'update',
            $character
        );

        $this->assertCharacterInCampaign(
            $character,
            $campaign
        );

        $validated = $request->validate([
            'recipient_character_id' => [
                'required',
                'integer',
                'exists:characters,id',
            ],

            'emoji' => [
                'required',
                'string',
                'in:👉,👀,💩,💀,❤️,🔥',
            ],
        ]);

        $recipient = Character::query()
            ->findOrFail(
                (int) $validated[
                    'recipient_character_id'
                ]
            );

        if (
            (int) $recipient->id
            ===
            (int) $character->id
        ) {
            throw ValidationException::withMessages([
                'recipient_character_id' =>
                    'Você não pode cutucar a própria ficha.',
            ]);
        }

        $this->assertCharacterInCampaign(
            $recipient,
            $campaign
        );

        $emoji =
            (string) $validated['emoji'];

        $message =
            $this->randomPokeMessage(
                $emoji,
                $character->name
            );

        $pokeId =
            (string) Str::uuid();

        DB::transaction(
            function () use (
                $campaign,
                $recipient,
                $character,
                $emoji,
                $message,
                $pokeId
            ): void {
                $note = CharacterPartyNote::query()
                    ->where(
                        'campaign_id',
                        $campaign->id
                    )
                    ->where(
                        'character_id',
                        $recipient->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$note) {
                    $note = CharacterPartyNote::query()
                        ->create([
                            'campaign_id' =>
                                $campaign->id,

                            'character_id' =>
                                $recipient->id,

                            'notes' =>
                                null,

                            'pokes' =>
                                [],
                        ]);
                }

                $pokes =
                    is_array(
                        $note->pokes
                    )
                        ? $note->pokes
                        : [];

                $pokes[] = [
                    'id' =>
                        $pokeId,

                    'from_character_id' =>
                        (int) $character->id,

                    'from_character_name' =>
                        $character->name,

                    'emoji' =>
                        $emoji,

                    'message' =>
                        $message,

                    'created_at' =>
                        now()->toISOString(),

                    'seen' =>
                        false,
                ];

                /*
                | Evita que uma ficha acumule um JSON infinito.
                | Mantemos as 100 interações mais recentes.
                */

                $note->pokes =
                    array_values(
                        array_slice(
                            $pokes,
                            -100
                        )
                    );

                $note->save();
            }
        );

        return response()->json([
            'success' =>
                true,

            'emoji' =>
                $emoji,

            'message' =>
                "{$emoji} Enviado para {$recipient->name}.",
        ]);
    }


    public function pokes(
        Character $character
    ): JsonResponse {
        Gate::authorize(
            'update',
            $character
        );

        $delivered = [];

        DB::transaction(
            function () use (
                $character,
                &$delivered
            ): void {
                $notes = CharacterPartyNote::query()
                    ->where(
                        'character_id',
                        $character->id
                    )
                    ->whereNotNull(
                        'pokes'
                    )
                    ->with(
                        'campaign:id,name'
                    )
                    ->orderBy(
                        'id'
                    )
                    ->lockForUpdate()
                    ->get();

                foreach ($notes as $note) {
                    $pokes =
                        is_array(
                            $note->pokes
                        )
                            ? $note->pokes
                            : [];

                    if ($pokes === []) {
                        continue;
                    }

                    $changed =
                        false;

                    foreach ($pokes as &$poke) {
                        if (
                            count($delivered)
                            >=
                            8
                        ) {
                            break 2;
                        }

                        if (
                            !is_array($poke)
                            ||
                            (bool) (
                                $poke['seen']
                                ?? false
                            )
                        ) {
                            continue;
                        }

                        $delivered[] = [
                            'id' =>
                                (string) (
                                    $poke['id']
                                    ?? Str::uuid()
                                ),

                            'campaign_id' =>
                                (int) $note->campaign_id,

                            'campaign_name' =>
                                $note->campaign?->name
                                ?? 'Campanha',

                            'sender_character_id' =>
                                (int) (
                                    $poke[
                                        'from_character_id'
                                    ]
                                    ?? 0
                                ),

                            'sender_name' =>
                                (string) (
                                    $poke[
                                        'from_character_name'
                                    ]
                                    ?? 'Alguém'
                                ),

                            'emoji' =>
                                (string) (
                                    $poke['emoji']
                                    ?? '👉'
                                ),

                            'message' =>
                                (string) (
                                    $poke['message']
                                    ?? 'Alguém te cutucou.'
                                ),

                            'created_at' =>
                                $poke['created_at']
                                ?? null,
                        ];

                        $poke['seen'] =
                            true;

                        $poke['seen_at'] =
                            now()->toISOString();

                        $changed =
                            true;
                    }

                    unset($poke);

                    if ($changed) {
                        $note->pokes =
                            array_values(
                                $pokes
                            );

                        $note->save();
                    }
                }
            }
        );

        return response()->json([
            'success' =>
                true,

            'pokes' =>
                $delivered,
        ]);
    }


    public function transferItem(
        Request $request,
        Character $character,
        Campaign $campaign
    ): JsonResponse {
        Gate::authorize('update', $character);

        $this->assertCharacterInCampaign($character, $campaign);

        $validated = $request->validate([
            'recipient_character_id' => [
                'required',
                'integer',
                'exists:characters,id',
            ],
            'item_id' => [
                'required',
                'integer',
                'exists:character_items,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $recipient = Character::query()
            ->with(['combat', 'classes', 'user'])
            ->findOrFail((int) $validated['recipient_character_id']);

        if ((int) $recipient->id === (int) $character->id) {
            throw ValidationException::withMessages([
                'recipient_character_id' => 'Escolha outro personagem da Party.',
            ]);
        }

        $this->assertCharacterInCampaign($recipient, $campaign);

        $item = CharacterItem::query()
            ->where('character_id', $character->id)
            ->findOrFail((int) $validated['item_id']);

        $quantity = (int) $validated['quantity'];

        if ($quantity > (int) $item->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'A quantidade escolhida é maior que a disponível.',
            ]);
        }

        $copiedImagePath = null;

        try {
            DB::transaction(function () use (
                $recipient,
                $item,
                $quantity,
                &$copiedImagePath
            ) {
                if ($quantity === (int) $item->quantity) {
                    $item->forceFill([
                        'character_id' => $recipient->id,
                        'equipped' => false,
                        'attuned' => false,
                    ]);

                    $item->save();

                    return;
                }

                $recipientItem = $item->replicate();

                $recipientItem->character_id = $recipient->id;
                $recipientItem->quantity = $quantity;
                $recipientItem->equipped = false;
                $recipientItem->attuned = false;

                if (
                    is_string($item->image_path)
                    && trim($item->image_path) !== ''
                    && Storage::disk('public')->exists($item->image_path)
                ) {
                    $extension = pathinfo(
                        $item->image_path,
                        PATHINFO_EXTENSION
                    );

                    $directory = 'character-items/' . $recipient->id;

                    Storage::disk('public')->makeDirectory($directory);

                    $copiedImagePath = $directory
                        . '/'
                        . Str::uuid()->toString()
                        . ($extension ? '.' . $extension : '');

                    $copied = Storage::disk('public')->copy(
                        $item->image_path,
                        $copiedImagePath
                    );

                    if (!$copied) {
                        throw new \RuntimeException(
                            'Não foi possível copiar a imagem do item.'
                        );
                    }

                    $recipientItem->image_path = $copiedImagePath;
                }

                $recipientItem->save();

                $item->quantity = max(
                    0,
                    (int) $item->quantity - $quantity
                );

                $item->save();
            });
        } catch (Throwable $exception) {
            if (
                $copiedImagePath
                && Storage::disk('public')->exists($copiedImagePath)
            ) {
                Storage::disk('public')->delete($copiedImagePath);
            }

            throw $exception;
        }

        return response()->json([
            'success' => true,
            'message' => "{$quantity}× {$item->name} entregue para {$recipient->name}.",
            'items' => $this->itemsPayload($character->fresh()),
        ]);
    }

    private function activeCampaigns(Character $character)
    {
        return Campaign::query()
            ->whereHas('characters', function ($query) use ($character) {
                $query
                    ->where('characters.id', $character->id)
                    ->where('campaign_characters.is_active', true);
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function resolveSelectedCampaign(
        $campaigns,
        ?int $campaignId
    ): ?Campaign {
        if ($campaigns->isEmpty()) {
            return null;
        }

        if ($campaignId) {
            $selected = $campaigns->firstWhere('id', $campaignId);

            if ($selected) {
                return $selected;
            }
        }

        return $campaigns->first();
    }

    private function assertCharacterInCampaign(
        Character $character,
        Campaign $campaign
    ): void {
        $exists = DB::table('campaign_characters')
            ->where('campaign_id', $campaign->id)
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->exists();

        abort_unless($exists, 404);
    }

    private function memberPayloads(
        Campaign $campaign,
        Character $character
    ): array {
        $members = $campaign
            ->characters()
            ->wherePivot('is_active', true)
            ->where('characters.id', '!=', $character->id)
            ->with(['combat', 'classes', 'user'])
            ->orderBy('characters.name')
            ->get();

        return $members
            ->map(
                fn (Character $member) =>
                    $this->memberPayload(
                        $campaign,
                        $character,
                        $member
                    )
            )
            ->values()
            ->all();
    }

    private function memberPayload(
        Campaign $campaign,
        Character $character,
        Character $member
    ): array
    {
        $combat = $member->combat;

        $maxHp = $combat
            ? max(
                1,
                (int) $combat->max_hp
                + (int) $combat->temporary_max_hp
            )
            : null;

        $currentHp = $combat
            ? max(0, (int) $combat->current_hp)
            : null;

        $temporaryHp = $combat
            ? max(0, (int) $combat->temporary_hp)
            : 0;

        $hpPercent = (
            $maxHp !== null
            && $maxHp > 0
            && $currentHp !== null
        )
            ? round(
                max(
                    0,
                    min(100, ($currentHp / $maxHp) * 100)
                ),
                2
            )
            : null;

        $healthState = match (true) {
            $currentHp === null => 'unknown',
            $currentHp <= 0 => 'down',
            $hpPercent <= 25 => 'critical',
            $hpPercent <= 50 => 'wounded',
            default => 'healthy',
        };

        $settings = $this->normalizedSettings(
            $member->sheet_settings ?? []
        );

        $exhaustionEnabled = (bool) data_get(
            $settings,
            'optional_rules.exhaustion',
            false
        );

        $exhaustion = $exhaustionEnabled
            ? min(
                6,
                max(0, (int) ($combat?->exhaustion_level ?? 0))
            )
            : 0;

        $classes = $member->classes
            ->sortByDesc(fn ($class) => [
                (int) ($class->is_primary ?? false),
                (int) ($class->level ?? 0),
            ])
            ->values();

        $primaryClass = $classes->first();

        $imageUrl = (
            is_string($member->image_path)
            && trim($member->image_path) !== ''
        )
            ? route(
                'characters.party.members.image',
                [
                    'character' =>
                        $character->id,
                    'campaign' =>
                        $campaign->id,
                    'member' =>
                        $member->id,
                ]
            )
            : null;

        return [
            'id' => (int) $member->id,
            'name' => $member->name,
            'player_name' => $member->user?->name,
            'level' => max(1, (int) ($member->level ?? 1)),
            'class_name' => $primaryClass?->class ?? 'Personagem',
            'image_url' => $imageUrl,
            'current_hp' => $currentHp,
            'max_hp' => $maxHp,
            'temporary_hp' => $temporaryHp,
            'hp_percent' => $hpPercent,
            'health_state' => $healthState,
            'exhaustion_enabled' => $exhaustionEnabled,
            'exhaustion' => $exhaustion,
        ];
    }

    private function itemsPayload(Character $character): array
    {
        return $character
            ->items()
            ->orderBy('name')
            ->get()
            ->map(function (CharacterItem $item) use ($character): array {
                return [
                    'id' => (int) $item->id,
                    'name' => $item->name,
                    'type' => $item->type,
                    'quantity' => max(0, (int) $item->quantity),
                    'equipped' => (bool) $item->equipped,
                    'attuned' => (bool) $item->attuned,
                    'is_magical' => (bool) $item->is_magical,
                    'rarity' => $item->rarity,
                    'rarity_label' => $item->rarity_label,
                    'image_url' => (
                        is_string($item->image_path)
                        && trim($item->image_path) !== ''
                    )
                        ? route('characters.items.image', [
                            'character' => $character->id,
                            'item' => $item->id,
                        ])
                        : null,
                ];
            })
            ->filter(fn (array $item) => $item['quantity'] > 0)
            ->values()
            ->all();
    }

    private function diaryPages(
        CharacterPartyNote $note
    ): array {
        $raw = (string) ($note->notes ?? '');

        if (trim($raw) === '') {
            return [
                [
                    'id' => 'page-1',
                    'content' => '',
                ],
            ];
        }

        $decoded = json_decode(
            $raw,
            true
        );

        if (
            is_array($decoded)
            && (int) ($decoded['version'] ?? 0) === 2
            && is_array($decoded['pages'] ?? null)
        ) {
            $pages = collect($decoded['pages'])
                ->filter(fn ($page) => is_array($page))
                ->map(fn (array $page) => [
                    'id' => trim(
                        (string) ($page['id'] ?? '')
                    ) ?: ('page-' . uniqid()),
                    'content' => (string) ($page['content'] ?? ''),
                ])
                ->take(50)
                ->values()
                ->all();

            if (count($pages) > 0) {
                return $pages;
            }
        }

        return [
            [
                'id' => 'legacy-page-1',
                'content' => $raw,
            ],
        ];
    }


    private function randomPokeMessage(
        string $emoji,
        string $senderName
    ): string {
        $messages = [
            '👉' => [
                '{name} está te chamando.',
                'Ei! Você aí!',
                'Psiu!',
                'Ô criatura, olha aqui.',
                'Tô falando com você!',
                'Acorda pra vida!',
                'Tem alguém tentando chamar sua atenção.',
                'Acorda Samurai, temos uma cidade pra queimar.',
            ],

            '👀' => [
                '{name} está de olho em você.',
                'Olhos sempre abertos !!',
                'Eu vi isso aí 👀',
                'Tá achando que ninguém viu?',
                'Tem alguém te observando...',
                'Nada passa despercebido.',
                'Hmm... interessante 👀',
                'Eu tô vendo tudo.',
                'Suspeito. Muito suspeito.',
                'Averiguando Resenha.',
            ],

            '💩' => [
                'Cocô.',
                'Fezes.',
                'Merda.',
                'Bosta.',
                'Badalhoca.',
                'Isso fede.',
                'Merdão',
                'Que situação de merda.',
                'Parabéns, virou adubo.',
                'Cheiro de problema.',
                'Ossa que Bosta.',
                'Cagaro.',
                'Que belo monte de 💩.',
                'O ambiente ficou insalubre.',
            ],

            '💀' => [
                'Mortis.',
                'Vai morrer.',
                'Pé na cova e outro no sabonete.',
                'Morre logo aí.',
                'Já dá pra lootear?',
                'Eu fico com a cueca.',
                'F no chat.',
                'Foi de base?',
                'Alguém chama o clérigo.',
                'Seu inventário já tem herdeiro?',
                'Já pode arrastar pra cima ?.',
                'Começa a separar o loot.',
                'Você tá com cara de defunto.',
                'Se cair, eu pego seus itens.',
            ],

            '❤️' => [
                'Você consegue.',
                'Não desista.',
                'Aguente firme.',
                'Determinação, herói!',
                'Eu confio em você.',
                'Você arrasa.',
                'Vai dar certo.',
                'Tô contigo.',
                'Você não cai hoje.',
                'Força, campeão!',
                'Mais um turno. Você consegue.',
                'Herói não desiste agora.',
                'Respira e continua.',
                'Meu favorito da Party. Não conta pros outros.',
            ],

            '🔥' => [
                'HELL YEAHH!!',
                'Irado!',
                'Fireee!',
                'Mandou muito!',
                'Insano!',
                'Arrasou!',
                'Tá pegando fogo!',
                'É DISSO que eu tô falando!',
                'ABSURDO!',
                'Cinema.',
                'Brabo demais.',
                'Isso foi criminosamente bonito.',
                'Comprou o dado viciado ?.',
                'Alguém apaga esse incêndio!',
            ],
        ];

        $pool =
            $messages[$emoji]
            ??
            $messages['👉'];

        $message =
            $pool[
                array_rand(
                    $pool
                )
            ];

        return str_replace(
            '{name}',
            $senderName,
            $message
        );
    }


    private function normalizedSettings(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }
}