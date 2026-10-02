<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Services\Campaigns\CampaignLiveStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CampaignMasterController extends Controller
{
    public function __construct(
        private CampaignLiveStateService $liveState
    ) {
    }

    public function live(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        abort_unless(
            $campaign->isOwner($request->user()),
            403
        );

        $state =
            $this->liveState
                ->masterShield(
                    $campaign
                );

        $state['players'] =
            $this->attachDeathHistory(
                is_array(
                    $state['players']
                    ?? null
                )
                    ? $state['players']
                    : []
            );

        $state['players'] =
            $this->attachMemberVisibility(
                $campaign,
                $state['players']
            );

        return response()->json([
            'success' => true,
            ...$state,
        ]);
    }

    public function characterImage(
        Request $request,
        Campaign $campaign,
        Character $character
    ): BinaryFileResponse {
        abort_unless(
            $campaign->isOwner(
                $request->user()
            ),
            403
        );

        $isShared =
            $campaign
                ->characters()
                ->whereKey(
                    $character->id
                )
                ->exists();

        abort_unless(
            $isShared,
            404
        );

        abort_unless(
            is_string(
                $character->image_path
            )
            &&
            trim(
                $character->image_path
            )
            !==
            '',
            404
        );

        $relativePath =
            ltrim(
                str_replace(
                    '\\',
                    '/',
                    trim(
                        $character->image_path
                    )
                ),
                '/'
            );

        foreach (
            ['storage/', 'public/']
            as $prefix
        ) {
            if (
                str_starts_with(
                    $relativePath,
                    $prefix
                )
            ) {
                $relativePath =
                    substr(
                        $relativePath,
                        strlen($prefix)
                    );
            }
        }

        abort_if(
            $relativePath === ''
            ||
            str_contains(
                $relativePath,
                '../'
            ),
            404
        );

        abort_unless(
            Storage::disk('public')
                ->exists(
                    $relativePath
                ),
            404
        );

        return response()->file(
            Storage::disk('public')
                ->path(
                    $relativePath
                ),
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


    public function removeMember(
        Request $request,
        Campaign $campaign,
        int $user
    ): JsonResponse {
        abort_unless(
            $campaign->isOwner(
                $request->user()
            ),
            403
        );

        abort_if(
            (int) $campaign->owner_user_id
            ===
            $user,
            422,
            'O Mestre não pode remover a si mesmo da própria campanha.'
        );

        $member =
            $campaign
                ->members()
                ->with('user')
                ->where(
                    'user_id',
                    $user
                )
                ->first();

        abort_unless(
            $member,
            404,
            'Este Player não pertence mais à campanha.'
        );

        $playerName =
            $member->user?->name
            ?? 'Player';

        $characterIds =
            Character::query()
                ->where(
                    'user_id',
                    $user
                )
                ->pluck('id');

        DB::transaction(
            function () use (
                $campaign,
                $member,
                $characterIds
            ): void {
                if ($characterIds->isNotEmpty()) {
                    DB::table(
                        'campaign_characters'
                    )
                        ->where(
                            'campaign_id',
                            $campaign->id
                        )
                        ->whereIn(
                            'character_id',
                            $characterIds
                        )
                        ->delete();
                }

                $member->delete();
            }
        );

        return response()->json([
            'success' =>
                true,

            'message' =>
                "{$playerName} foi removido da Party.",

            'user_id' =>
                $user,
        ]);
    }


    public function updateMemberVisibility(
        Request $request,
        Campaign $campaign,
        int $user
    ): JsonResponse {
        abort_unless(
            $campaign->isOwner(
                $request->user()
            ),
            403
        );

        $validated =
            $request->validate([
                'hidden' => [
                    'required',
                    'boolean',
                ],
            ]);

        $member =
            $campaign
                ->members()
                ->with('user')
                ->where(
                    'user_id',
                    $user
                )
                ->first();

        abort_unless(
            $member,
            404,
            'Este Player não pertence mais à campanha.'
        );

        $hidden =
            (bool) $validated['hidden'];

        $member->forceFill([
            'hidden_from_party' =>
                $hidden,
        ]);

        $member->save();

        return response()->json([
            'success' =>
                true,

            'user_id' =>
                $user,

            'hidden_from_party' =>
                $hidden,

            'message' =>
                $hidden
                    ? "{$member->user?->name} foi escondido da Party."
                    : "{$member->user?->name} voltou a ficar visível para a Party.",
        ]);
    }


    public function updateSettings(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        abort_unless(
            $campaign->isOwner(
                $request->user()
            ),
            403
        );

        $validated =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

        $campaign->update([
            'name' =>
                trim(
                    $validated['name']
                ),

            'description' =>
                isset(
                    $validated['description']
                )
                    ? trim(
                        $validated['description']
                    )
                    : null,
        ]);

        return redirect()
            ->route(
                'campaigns.show',
                $campaign
            )
            ->with(
                'success',
                'Configurações da campanha atualizadas.'
            );
    }


    public function updateNotes(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        abort_unless(
            $campaign->isOwner(
                $request->user()
            ),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Autosave do Caderno
        |--------------------------------------------------------------------------
        |
        | O navegador envia JSON por PATCH. O token CSRF vai no header.
        | Mantemos o formato simples para que cada autosave seja previsível.
        |
        */

        $validated =
            $request->validate([
                'pages' => [
                    'required',
                    'array',
                    'min:1',
                    'max:40',
                ],

                'pages.*.id' => [
                    'required',
                    'string',
                    'max:100',
                    'distinct',
                ],

                'pages.*.title' => [
                    'nullable',
                    'string',
                    'max:120',
                ],

                'pages.*.content' => [
                    'nullable',
                    'string',
                    'max:100000',
                ],

                'active_page_id' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
            ]);

        $pages =
            collect(
                $validated['pages']
            )
                ->map(
                    fn (array $page) => [
                        'id' =>
                            trim(
                                (string) $page['id']
                            ),

                        'title' =>
                            mb_substr(
                                (string) (
                                    $page['title']
                                    ?? ''
                                ),
                                0,
                                120
                            ),

                        'content' =>
                            (string) (
                                $page['content']
                                ?? ''
                            ),
                    ]
                )
                ->filter(
                    fn (array $page) =>
                        $page['id'] !== ''
                )
                ->values();

        if ($pages->isEmpty()) {
            $pages =
                collect([
                    [
                        'id' =>
                            'page-1',

                        'title' =>
                            '',

                        'content' =>
                            '',
                    ],
                ]);
        }

        $activePageId =
            (string) (
                $validated['active_page_id']
                ?? ''
            );

        if (
            !$pages->contains(
                fn (array $page) =>
                    $page['id']
                    ===
                    $activePageId
            )
        ) {
            $activePageId =
                (string) $pages
                    ->first()['id'];
        }

        $payload = [
            'version' =>
                2,

            'active_page_id' =>
                $activePageId,

            'pages' =>
                $pages->all(),
        ];

        $encoded =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE
                |
                JSON_UNESCAPED_SLASHES
                |
                JSON_THROW_ON_ERROR
            );

        $campaign->master_notes =
            $encoded;

        $campaign->save();

        /*
        | Confirma no banco antes de dizer ao frontend que foi salvo.
        */
        $campaign->refresh();

        abort_unless(
            (string) $campaign->master_notes
            ===
            $encoded,
            500,
            'Não foi possível confirmar o salvamento do caderno.'
        );

        return response()->json([
            'success' =>
                true,

            'message' =>
                'Caderno salvo.',

            'updated_at' =>
                $campaign->updated_at
                    ?->toISOString(),

            'active_page_id' =>
                $activePageId,
        ]);
    }


    private function attachMemberVisibility(
        Campaign $campaign,
        array $players
    ): array {
        if ($players === []) {
            return $players;
        }

        $visibilityByUser =
            $campaign
                ->members()
                ->get([
                    'user_id',
                    'hidden_from_party',
                ])
                ->keyBy(
                    fn ($member) =>
                        (int) $member->user_id
                );

        return collect(
            $players
        )
            ->map(
                function (array $player) use (
                    $visibilityByUser
                ): array {
                    $userId =
                        (int) (
                            $player['user_id']
                            ?? 0
                        );

                    $member =
                        $visibilityByUser
                            ->get(
                                $userId
                            );

                    $player['hidden_from_party'] =
                        (bool) (
                            $member?->hidden_from_party
                            ?? false
                        );

                    return $player;
                }
            )
            ->values()
            ->all();
    }


    private function attachDeathHistory(
        array $players
    ): array {
        $characterIds =
            collect(
                $players
            )
                ->map(
                    fn (array $player) =>
                        (int) data_get(
                            $player,
                            'character.id',
                            0
                        )
                )
                ->filter()
                ->unique()
                ->values();

        if ($characterIds->isEmpty()) {
            return $players;
        }

        $combatByCharacter =
            DB::table(
                'character_combat'
            )
                ->whereIn(
                    'character_id',
                    $characterIds
                )
                ->get([
                    'character_id',
                    'overrides',
                ])
                ->keyBy(
                    fn ($row) =>
                        (int) $row->character_id
                );

        return collect(
            $players
        )
            ->map(
                function (array $player) use (
                    $combatByCharacter
                ): array {
                    $character =
                        $player['character']
                        ?? null;

                    if (!is_array($character)) {
                        return $player;
                    }

                    $characterId =
                        (int) (
                            $character['id']
                            ?? 0
                        );

                    $row =
                        $combatByCharacter
                            ->get(
                                $characterId
                            );

                    $overrides =
                        $this->normalizedOverrides(
                            $row?->overrides
                            ?? []
                        );

                    $history =
                        data_get(
                            $overrides,
                            'death_history',
                            []
                        );

                    if (!is_array($history)) {
                        $history = [];
                    }

                    $events =
                        $history['events']
                        ?? [];

                    if (!is_array($events)) {
                        $events = [];
                    }

                    $events =
                        collect(
                            $events
                        )
                            ->filter(
                                fn ($event) =>
                                    is_array($event)
                            )
                            ->values()
                            ->all();

                    $count =
                        max(
                            count($events),
                            max(
                                0,
                                (int) (
                                    $history['count']
                                    ?? 0
                                )
                            )
                        );

                    $currentlyDead =
                        (bool) (
                            $character['death_save_dead']
                            ?? false
                        );

                    $character['has_died'] =
                        $count > 0
                        ||
                        $currentlyDead;

                    $character['death_count'] =
                        max(
                            $count,
                            $currentlyDead
                                ? 1
                                : 0
                        );

                    $character['last_death_at'] =
                        isset(
                            $history['last_at']
                        )
                        &&
                        is_string(
                            $history['last_at']
                        )
                            ? $history['last_at']
                            : null;

                    $character['death_history'] =
                        $events;

                    $player['character'] =
                        $character;

                    return $player;
                }
            )
            ->values()
            ->all();
    }


    private function normalizedOverrides(
        mixed $value
    ): array {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded =
                json_decode(
                    $value,
                    true
                );

            return is_array($decoded)
                ? $decoded
                : [];
        }

        return [];
    }

}