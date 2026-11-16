<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\CharacterSpell;
use App\Models\CharacterSpellLoadout;
use App\Models\CharacterSpellcasting;
use App\Models\Spell;
use App\Services\Characters\ConcentrationCheckService;
use App\Services\Characters\CharacterConcentrationManager;
use App\Models\CharacterTransformationState;
use App\Services\Spells\CampaignSpellAccessService;
use App\Services\Spells\SpellCastService;
use App\Services\Spells\SpellClassListService;
use App\Services\Spells\SpellDisplayService;
use App\Services\Spells\SpellDurationResolver;
use App\Services\Spells\SpellcastingProgressionService;
use App\Services\Spells\SpellcastingSheetSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CharacterSpellbookController extends Controller
{
    public function __construct(
        private readonly CampaignSpellAccessService $access,
        private readonly SpellDisplayService $display,
        private readonly SpellcastingProgressionService $progression,
        private readonly SpellcastingSheetSyncService $sheetSync,
        private readonly SpellClassListService $classLists,
        private readonly SpellCastService $caster,
        private readonly ConcentrationCheckService $concentrationChecks
    ) {
    }

    public function state(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $character->load([
            'combat',
        ]);

        if (
            $request->query('scope')
            ===
            'concentration'
        ) {
            return response()->json([
                'success' => true,
                'concentration' =>
                    $this->concentrationPayload(
                        $character
                    ),
                'transformation_timer' => $this->transformationTimerPayload($character),
            ]);
        }

        $campaigns =
            $this->activeCampaigns($character);

        $campaign =
            $this->resolveSelectedCampaign(
                $campaigns,
                $request->integer('campaign_id')
            );

        $character->loadMissing([
            'abilities',
            'classes',
        ]);

        $this->sheetSync
            ->syncLinkedProfiles(
                $character
            );

        $this->progression
            ->sync(
                $character
            );

        $profiles =
            CharacterSpellcasting::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->with([
                    'character.abilities',
                    'characterClass',
                ])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

        $slots =
            DB::table('character_spell_slots')
                ->where(
                    'character_id',
                    $character->id
                )
                ->orderBy('slot_type')
                ->orderBy('slot_level')
                ->get();

        $myRows =
            DB::table('character_spells as cs')
                ->join(
                    'spells as s',
                    's.id',
                    '=',
                    'cs.spell_id'
                )
                ->leftJoin(
                    'character_spellcasting as sc',
                    'sc.id',
                    '=',
                    'cs.spellcasting_id'
                )
                ->where(
                    'cs.character_id',
                    $character->id
                )
                ->orderBy('s.level')
                ->orderBy('s.name')
                ->orderBy('s.source')
                ->get([
                    'cs.id as character_spell_id',
                    'cs.spell_id',
                    'cs.spellcasting_id',
                    'cs.source_type',
                    'cs.source_class',
                    'cs.known',
                    'cs.prepared',
                    'cs.always_prepared',
                    'cs.free_casts_current',
                    'cs.free_casts_max',
                    'cs.free_casts_recovery',
                    'cs.cast_without_slot',
                    'cs.overrides',
                    'cs.personal_notes',
                    'cs.personal_media_path',

                    'sc.class as profile_name',

                    's.id',
                    's.name',
                    's.level',
                    's.school',
                    's.casting_time',
                    's.range',
                    's.components',
                    's.duration',
                    's.ritual',
                    's.concentration',
                    's.description',
                    's.higher_level_data',
                    's.higher_level_text',
                    's.scaling_level_dice',
                    's.damage_types',
                    's.saving_throw',
                    's.attack_type',
                    's.area_tags',
                    's.source',
                    's.page',
                    's.provider',
                    's.rules_version',
                    's.raw_data',
                ]);

        $mySpellIds =
            $myRows
                ->pluck('spell_id')
                ->map(
                    fn ($id) => (int) $id
                );

        $allowedIds =
            $mySpellIds->isEmpty()
                ? collect()
                : $this->allowedSpecificIds(
                    $campaign?->id,
                    $mySpellIds
                );

        $allowedLookup =
            $allowedIds->flip();

        // Relações reais entre magias e perfis, sem duplicar o catálogo visual.
        $castingProfilesBySpell = DB::table('character_spell_profiles as csp')
            ->join('character_spellcasting as cp', 'cp.id', '=', 'csp.spellcasting_id')
            ->whereIn('csp.character_spell_id', $myRows->pluck('character_spell_id')->all())
            ->where('cp.character_id', $character->id)
            ->get([
                'csp.character_spell_id', 'csp.spellcasting_id', 'csp.known',
                'csp.prepared', 'csp.always_prepared', 'cp.class', 'cp.is_active',
            ])->groupBy('character_spell_id');

        $spells =
            $myRows
                ->map(
                    function (
                        object $row
                    ) use (
                        $allowedLookup,
                        $castingProfilesBySpell,
                        $character
                    ): array {
                        $payload =
                            $this->display
                                ->payload($row);

                        return [
                            ...$payload,
                            // Tipos definidos pela origem: usados apenas na prévia de dano.
                            'damage_types' => is_array($row->damage_types)
                                ? $row->damage_types
                                : (json_decode((string) ($row->damage_types ?? '[]'), true) ?: []),

                            'character_spell_id' =>
                                (int)
                                $row->character_spell_id,

                            'spellcasting_id' =>
                                $row->spellcasting_id
                                !== null
                                    ? (int)
                                    $row->spellcasting_id
                                    : null,

                            'profile_name' =>
                                $row->profile_name,

                            'personal_notes' => (string) ($row->personal_notes ?? ''),
                            'personal_media_url' => $row->personal_media_path
                                ? route('characters.spellbook.spells.media.show', [
                                    'character' => $character->id,
                                    'spell' => $row->spell_id,
                                ], false)
                                : null,
                            'casting_profiles' => ($castingProfilesBySpell->get(
                                (int) $row->character_spell_id, collect()
                            ))->map(fn ($profile): array => [
                                'id' => (int) $profile->spellcasting_id,
                                'class' => (string) $profile->class,
                                'known' => (bool) $profile->known,
                                'prepared' => (bool) $profile->prepared,
                                'always_prepared' => (bool) $profile->always_prepared,
                                'is_active' => (bool) $profile->is_active,
                            ])->values()->all(),

                            'source_type' =>
                                $row->source_type,

                            'source_class' =>
                                $row->source_class,

                            'known' =>
                                (bool) $row->known,

                            'prepared' =>
                                (int) $row->level > 0
                                    ? (bool) (
                                        $row->prepared
                                        ||
                                        $row->always_prepared
                                    )
                                    : false,

                            'prepared_manual' =>
                                (int) $row->level > 0
                                    ? (bool) $row->prepared
                                    : false,

                            'always_prepared' =>
                                (bool)
                                $row->always_prepared,

                            'free_casts_current' =>
                                $row->free_casts_current
                                !== null
                                    ? (int)
                                    $row->free_casts_current
                                    : null,

                            'free_casts_max' =>
                                $row->free_casts_max
                                !== null
                                    ? (int)
                                    $row->free_casts_max
                                    : null,

                            'free_casts_recovery' =>
                                $row->free_casts_recovery,

                            'cast_without_slot' =>
                                (bool) $row->cast_without_slot,

                            'allowed_in_campaign' =>
                                $allowedLookup->has(
                                    (int) $row->spell_id
                                ),
                        ];
                    }
                )
                ->values();

        $myClassLists = $this->classLists
            ->listsForSpellIds($mySpellIds);

        $spells = $spells
            ->map(function (array $spell) use ($myClassLists): array {
                $spell['class_lists'] = $myClassLists[(int) $spell['id']] ?? [];
                return $spell;
            })
            ->values();

        $loadouts =
            CharacterSpellLoadout::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->with([
                    'spellcasting:id,class',
                    'spells:id,name,level',
                ])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->orderBy('id')
                ->get();

        $catalogQuery =
            $this->access
                ->allowedSpellQuery(
                    $campaign?->id
                );

        $sources =
            (clone $catalogQuery)
                ->select([
                    'spells.source',
                    'spells.provider',
                ])
                ->whereNotNull('spells.source')
                ->where('spells.source', '<>', '')
                ->distinct()
                ->orderBy('spells.source')
                ->get();

        $schools =
            (clone $catalogQuery)
                ->whereNotNull('spells.school')
                ->where('spells.school', '<>', '')
                ->distinct()
                ->orderBy('spells.school')
                ->pluck('spells.school')
                ->values();

        $characterContext =
            $this->sheetSync
                ->context(
                    $character,
                    $profiles
                );

        return response()->json([
            'success' => true,

            'character_context' =>
                $characterContext,

            'campaigns' =>
                $campaigns
                    ->map(
                        fn (Campaign $row) => [
                            'id' =>
                                (int) $row->id,

                            'name' =>
                                $row->name,
                        ]
                    )
                    ->values()
                    ->all(),

            'campaign' =>
                $campaign
                    ? [
                        'id' =>
                            (int) $campaign->id,

                        'name' =>
                            $campaign->name,
                    ]
                    : null,

            'catalog' => [
                'count' =>
                    (clone $catalogQuery)
                        ->count(),

                'canonical_2024' =>
                    true,

                'sources' =>
                    $sources
                        ->map(
                            fn (
                                object $row
                            ) => [
                                'code' =>
                                    (string)
                                    $row->source,

                                'provider' =>
                                    (string)
                                    $row->provider,

                                'kind' =>
                                    $row->provider
                                    ===
                                    '5etools-homebrew'
                                        ? 'homebrew'
                                        : 'standard',
                            ]
                        )
                        ->values()
                        ->all(),

                'schools' =>
                    $schools->all(),

                'lists' =>
                    $this->classLists->availableLists(),
            ],

            'profiles' =>
                $profiles
                    ->map(
                        fn (
                            CharacterSpellcasting
                            $profile
                        ) =>
                            $this
                                ->profilePayload(
                                    $profile
                                )
                    )
                    ->values()
                    ->all(),

            'slots' =>
                $slots
                    ->map(
                        fn (
                            object $slot
                        ) => [
                            'id' =>
                                (int) $slot->id,

                            'slot_type' =>
                                (string)
                                $slot->slot_type,

                            'slot_level' =>
                                (int)
                                $slot->slot_level,

                            'current' =>
                                (int)
                                $slot->current,

                            'maximum' =>
                                (int)
                                $slot->maximum,

                            'calculated_maximum' =>
                                $slot->calculated_maximum
                                !== null
                                    ? (int)
                                    $slot->calculated_maximum
                                    : null,

                            'manual_maximum' =>
                                $slot->manual_maximum
                                !== null
                                    ? (int)
                                    $slot->manual_maximum
                                    : null,

                            'recovery' =>
                                (string)
                                $slot->recovery,
                        ]
                    )
                    ->values()
                    ->all(),

            'shared_effective_caster_level' =>
                $this->progression
                    ->sharedEffectiveCasterLevel(
                        $profiles
                            ->where(
                                'is_active',
                                true
                            )
                            ->values()
                    ),

            'loadouts' =>
                $loadouts
                    ->map(
                        fn (
                            CharacterSpellLoadout $loadout
                        ) => [
                            'id' =>
                                (int) $loadout->id,

                            'spellcasting_id' =>
                                $loadout->spellcasting_id
                                !== null
                                    ? (int) $loadout->spellcasting_id
                                    : null,

                            'profile_name' =>
                                $loadout->spellcasting?->class,

                            'name' =>
                                (string) $loadout->name,

                            'description' =>
                                $loadout->description,

                            'sort_order' =>
                                (int) $loadout->sort_order,

                            'spell_count' =>
                                $loadout->spells
                                    ->where('level', '>', 0)
                                    ->count(),

                            'spell_ids' =>
                                $loadout->spells
                                    ->where('level', '>', 0)
                                    ->pluck('id')
                                    ->map(fn ($id) => (int) $id)
                                    ->values()
                                    ->all(),
                        ]
                    )
                    ->values()
                    ->all(),

            'concentration' =>
                $this->concentrationPayload(
                    $character
                ),

            'spells' =>
                $spells->all(),
        ]);
    }

    public function catalog(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $campaigns =
            $this->activeCampaigns($character);

        $campaign =
            $this->resolveSelectedCampaign(
                $campaigns,
                $request->integer('campaign_id')
            );

        $query =
            $this->access
                ->allowedSpellQuery(
                    $campaign?->id
                );

        $search =
            trim(
                (string)
                $request->query(
                    'q',
                    ''
                )
            );

        if ($search !== '') {
            $query->where(
                function (
                    $builder
                ) use (
                    $search
                ) {
                    $builder
                        ->where(
                            'spells.name',
                            'like',
                            '%'
                            . $search
                            . '%'
                        )
                        ->orWhere(
                            'spells.source',
                            'like',
                            '%'
                            . $search
                            . '%'
                        )
                        ->orWhere(
                            'spells.school',
                            'like',
                            '%'
                            . $search
                            . '%'
                        )
                        ->orWhere(
                            'spells.description',
                            'like',
                            '%'
                            . $search
                            . '%'
                        );
                }
            );
        }

        $level =
            $request->query('level');

        if (
            $level !== null
            &&
            $level !== ''
            &&
            is_numeric($level)
        ) {
            $query->where(
                'spells.level',
                (int) $level
            );
        }

        foreach (
            [
                'school',
                'source',
                'provider',
            ]
            as $filter
        ) {
            $value =
                trim(
                    (string)
                    $request->query(
                        $filter,
                        ''
                    )
                );

            if ($value === '') {
                continue;
            }

            $query->where(
                'spells.'
                . $filter,
                $value
            );
        }

        $listCodes = collect(
            $request->query('lists', [])
        )
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values();

        if ($listCodes->isNotEmpty()) {
            $query->whereExists(
                function ($subquery) use ($listCodes) {
                    $subquery
                        ->selectRaw('1')
                        ->from('spell_class_lists as scl')
                        ->whereColumn('scl.spell_id', 'spells.id')
                        ->where('scl.list_type', 'class')
                        ->whereIn('scl.class_code', $listCodes->all());

                    $this->classLists->constrainToPrimary2024Lists(
                        $subquery,
                        'spells',
                        'scl'
                    );
                }
            );
        }

        $page =
            max(
                1,
                $request->integer(
                    'page',
                    1
                )
            );

        $perPage =
            min(
                60,
                max(
                    10,
                    $request->integer(
                        'per_page',
                        40
                    )
                )
            );

        $total =
            (clone $query)->count();

        $rows =
            $query
                ->orderBy('spells.level')
                ->orderBy('spells.name')
                ->orderBy('spells.source')
                ->forPage(
                    $page,
                    $perPage
                )
                ->get([
                    'spells.id',
                    'spells.name',
                    'spells.level',
                    'spells.school',
                    'spells.casting_time',
                    'spells.range',
                    'spells.components',
                    'spells.duration',
                    'spells.ritual',
                    'spells.concentration',
                    'spells.description',
                    'spells.higher_level_data',
                    'spells.higher_level_text',
                    'spells.scaling_level_dice',
                    'spells.damage_types',
                    'spells.saving_throw',
                    'spells.attack_type',
                    'spells.area_tags',
                    'spells.source',
                    'spells.page',
                    'spells.provider',
                    'spells.rules_version',
                    'spells.raw_data',
                ]);

        $existingIds =
            DB::table('character_spells')
                ->where(
                    'character_id',
                    $character->id
                )
                ->whereIn(
                    'spell_id',
                    $rows
                        ->pluck('id')
                        ->all()
                )
                ->pluck('spell_id')
                ->map(
                    fn ($id) => (int) $id
                )
                ->flip();

        $catalogClassLists = $this->classLists
            ->listsForSpellIds(
                $rows->pluck('id')
            );

        $lastPage =
            max(
                1,
                (int)
                ceil(
                    $total
                    /
                    $perPage
                )
            );

        return response()->json([
            'success' => true,

            'data' =>
                $rows
                    ->map(
                        fn (
                            object $spell
                        ) => [
                            ...$this
                                ->display
                                ->payload(
                                    $spell
                                ),
                            'damage_types' => is_array($spell->damage_types)
                                ? $spell->damage_types
                                : (json_decode((string) ($spell->damage_types ?? '[]'), true) ?: []),

                            'class_lists' =>
                                $catalogClassLists[(int) $spell->id]
                                ?? [],

                            'in_spellbook' =>
                                $existingIds
                                    ->has(
                                        (int)
                                        $spell->id
                                    ),
                        ]
                    )
                    ->values()
                    ->all(),

            'pagination' => [
                'page' =>
                    $page,

                'per_page' =>
                    $perPage,

                'total' =>
                    $total,

                'last_page' =>
                    $lastPage,

                'has_previous' =>
                    $page > 1,

                'has_next' =>
                    $page
                    <
                    $lastPage,
            ],
        ]);
    }

    public function storeSpell(
        Request $request,
        Character $character,
        Spell $spell
    ): JsonResponse {
        Gate::authorize('update', $character);

        $campaign =
            $this
                ->resolveCampaignForMutation(
                    $request,
                    $character
                );

        if (
            ! $this->access
                ->isSpellAllowed(
                    $campaign?->id,
                    $spell->id
                )
        ) {
            throw ValidationException::withMessages([
                'spell' =>
                    'Este feitiço não está autorizado no contexto atual da campanha.',
            ]);
        }

        $validated =
            $request->validate([
                'spellcasting_id' => [
                    'nullable',
                    'integer',
                ],
            ]);

        $profile = null;

        if (
            ! empty(
                $validated[
                    'spellcasting_id'
                ]
            )
        ) {
            $profile =
                CharacterSpellcasting::query()
                    ->where(
                        'character_id',
                        $character->id
                    )
                    ->findOrFail(
                        (int)
                        $validated[
                            'spellcasting_id'
                        ]
                    );
        }

        $characterSpell =
            CharacterSpell::query()
                ->firstOrCreate(
                    [
                        'character_id' =>
                            $character->id,

                        'spell_id' =>
                            $spell->id,
                    ],
                    [
                        'spellcasting_id' =>
                            $profile?->id,

                        'source_type' =>
                            $profile
                                ? 'class'
                                : 'catalog',

                        'source_class' =>
                            $profile?->class,

                        'known' =>
                            true,

                        'prepared' =>
                            false,

                        'always_prepared' =>
                            false,
                    ]
                );

        if ($profile) {
            $characterSpell
                ->spellcasting_id =
                $profile->id;

            $characterSpell
                ->source_type =
                'class';

            $characterSpell
                ->source_class =
                $profile->class;
        }

        $characterSpell->known = true;
        $characterSpell->save();

        if ($profile) {
            DB::table('character_spell_profiles')->updateOrInsert(
                [
                    'character_spell_id' => $characterSpell->id,
                    'spellcasting_id' => $profile->id,
                ],
                [
                    'known' => true,
                    'prepared' => (bool) $characterSpell->prepared,
                    'always_prepared' => (bool) $characterSpell->always_prepared,
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json([
            'success' => true,

            'message' =>
                "{$spell->name} foi adicionado ao Grimório.",
        ]);
    }

    public function updateSpell(
        Request $request,
        Character $character,
        Spell $spell
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated =
            $request->validate([
                'personal_notes' => [
                    'sometimes', 'nullable', 'string', 'max:8000',
                ],
                'casting_profile_ids' => [
                    'sometimes', 'array', 'max:10',
                ],
                'casting_profile_ids.*' => [
                    'integer', 'distinct',
                ],
                'origin_status' => ['sometimes', 'array'],
                'origin_status.spellcasting_id' => ['required_with:origin_status', 'integer'],
                'origin_status.prepared' => ['sometimes', 'boolean'],
                'origin_status.always_prepared' => ['sometimes', 'boolean'],
                'origin_status.known' => ['sometimes', 'boolean'],

                'prepared' => [
                    'sometimes',
                    'boolean',
                ],

                'always_prepared' => [
                    'sometimes',
                    'boolean',
                ],

                'known' => [
                    'sometimes',
                    'boolean',
                ],

                'spellcasting_id' => [
                    'sometimes',
                    'nullable',
                    'integer',
                ],

                'free_casts_max' => [
                    'sometimes',
                    'nullable',
                    'integer',
                    'min:0',
                    'max:999',
                ],

                'free_casts_current' => [
                    'sometimes',
                    'nullable',
                    'integer',
                    'min:0',
                    'max:999',
                ],

                'free_casts_recovery' => [
                    'sometimes',
                    'nullable',
                    Rule::in([
                        'short_rest',
                        'long_rest',
                        'day',
                        'manual',
                    ]),
                ],

                'cast_without_slot' => [
                    'sometimes',
                    'boolean',
                ],
            ]);

        $characterSpell =
            CharacterSpell::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->where(
                    'spell_id',
                    $spell->id
                )
                ->firstOrFail();

        foreach (
            [
                'personal_notes',
                'prepared',
                'always_prepared',
                'known',
                'free_casts_max',
                'free_casts_current',
                'free_casts_recovery',
                'cast_without_slot',
            ]
            as $field
        ) {
            if (
                array_key_exists(
                    $field,
                    $validated
                )
            ) {
                $characterSpell->{$field} =
                    $validated[$field];
            }
        }

        if (
            array_key_exists(
                'spellcasting_id',
                $validated
            )
        ) {
            $profileId =
                $validated[
                    'spellcasting_id'
                ];

            if ($profileId) {
                $profile =
                    CharacterSpellcasting::query()
                        ->where(
                            'character_id',
                            $character->id
                        )
                        ->findOrFail(
                            (int)
                            $profileId
                        );

                $characterSpell
                    ->spellcasting_id =
                    $profile->id;

                $characterSpell
                    ->source_type =
                    'class';

                $characterSpell
                    ->source_class =
                    $profile->class;

                DB::table('character_spell_profiles')->insertOrIgnore([
                    'character_spell_id' => $characterSpell->id,
                    'spellcasting_id' => $profile->id,
                    'known' => (bool) $characterSpell->known,
                    'prepared' => (bool) $characterSpell->prepared,
                    'always_prepared' => (bool) $characterSpell->always_prepared,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            } else {
                $characterSpell
                    ->spellcasting_id =
                    null;

                $characterSpell
                    ->source_class =
                    null;
            }
        }

        // Adiciona ou remove origens independentes, preservando a magia única.
        if (array_key_exists('casting_profile_ids', $validated)) {
            $requestedIds = collect($validated['casting_profile_ids'])
                ->map(fn ($id) => (int) $id)->unique()->values();
            $profiles = CharacterSpellcasting::query()
                ->where('character_id', $character->id)
                ->where('is_active', true)
                ->whereIn('id', $requestedIds->all())
                ->get()->keyBy('id');

            if ($profiles->count() !== $requestedIds->count()) {
                throw ValidationException::withMessages([
                    'casting_profile_ids' => 'Um dos perfis é inválido ou está desativado.',
                ]);
            }

            foreach ($requestedIds as $id) {
                DB::table('character_spell_profiles')->insertOrIgnore([
                    'character_spell_id' => $characterSpell->id,
                    'spellcasting_id' => $id,
                    'known' => true,
                    'prepared' => false,
                    'always_prepared' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $keep = DB::table('character_spell_profiles')
                ->where('character_spell_id', $characterSpell->id);
            if ($requestedIds->isEmpty()) {
                $keep->delete();
            } else {
                $keep->whereNotIn('spellcasting_id', $requestedIds->all())->delete();
            }

            $primaryId = (int) ($characterSpell->spellcasting_id ?? 0);
            if (!$requestedIds->contains($primaryId)) {
                $primaryId = (int) ($requestedIds->first() ?? 0);
                $primary = $profiles->get($primaryId);
                $characterSpell->spellcasting_id = $primary?->id;
                $characterSpell->source_class = $primary?->class;
                $characterSpell->source_type = $primary ? 'class' : 'catalog';
            }
        }

        if (isset($validated['origin_status'])) {
            $change = $validated['origin_status'];
            $profile = CharacterSpellcasting::query()
                ->where('character_id', $character->id)
                ->where('is_active', true)
                ->findOrFail((int) $change['spellcasting_id']);
            $originQuery = DB::table('character_spell_profiles')
                ->where('character_spell_id', $characterSpell->id)
                ->where('spellcasting_id', $profile->id);
            if (!$originQuery->exists()) {
                throw ValidationException::withMessages([
                    'origin_status' => 'Vincule primeiro a magia a este perfil.',
                ]);
            }
            $values = [];
            foreach (['prepared', 'always_prepared', 'known'] as $key) {
                if (array_key_exists($key, $change)) {
                    $values[$key] = (bool) $change[$key];
                }
            }
            if ($values !== []) {
                $originQuery->update([...$values, 'updated_at' => now()]);
                if ((int) $characterSpell->spellcasting_id === (int) $profile->id) {
                    foreach ($values as $key => $value) {
                        $characterSpell->{$key} = $value;
                    }
                }
            }
        }

        // Legado: as ações Preparar e Sempre preparado continuam atualizando
        // o perfil principal, mantendo comportamento anterior da interface.
        $primaryStatus = [];
        foreach (['known', 'prepared', 'always_prepared'] as $key) {
            if (array_key_exists($key, $validated)) {
                $primaryStatus[$key] = (bool) $validated[$key];
            }
        }
        if ($primaryStatus !== [] && $characterSpell->spellcasting_id) {
            DB::table('character_spell_profiles')->updateOrInsert(
                ['character_spell_id' => $characterSpell->id,
                 'spellcasting_id' => $characterSpell->spellcasting_id],
                [...$primaryStatus, 'updated_at' => now()]
            );
        }

        if (
            $characterSpell
                ->free_casts_max
            !== null
            &&
            $characterSpell
                ->free_casts_current
            !== null
        ) {
            $characterSpell
                ->free_casts_current =
                min(
                    (int)
                    $characterSpell
                        ->free_casts_current,
                    (int)
                    $characterSpell
                        ->free_casts_max
                );
        }

        $characterSpell->save();

        return response()->json([
            'success' => true,
            'message' =>
                'Feitiço atualizado.',
        ]);
    }

    public function uploadSpellMedia(Request $request, Character $character, Spell $spell): JsonResponse
    {
        Gate::authorize('update', $character);
        $request->validate([
            'media' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ]);
        $personal = CharacterSpell::query()
            ->where('character_id', $character->id)->where('spell_id', $spell->id)
            ->firstOrFail();
        $newPath = $request->file('media')->store("character-spells/{$character->id}", 'public');
        $oldPath = $personal->personal_media_path;
        try {
            $personal->personal_media_path = $newPath;
            $personal->save();
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newPath);
            throw $e;
        }
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
        return response()->json([
            'success' => true,
            'personal_media_url' => route('characters.spellbook.spells.media.show', [
                'character' => $character->id,
                'spell' => $spell->id,
            ], false),
            'message' => 'Imagem do feitiço atualizada.',
        ]);
    }

    /**
     * V15: mídia privada do jogador. Evita URLs absolutas de APP_URL e
     * permite exibição sem depender do symlink public/storage.
     */
    public function showSpellMedia(Character $character, Spell $spell): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        Gate::authorize('update', $character);

        $personal = CharacterSpell::query()
            ->where('character_id', $character->id)
            ->where('spell_id', $spell->id)
            ->firstOrFail();

        $path = trim((string) ($personal->personal_media_path ?? ''));
        abort_unless(
            $path !== ''
                && str_starts_with($path, "character-spells/{$character->id}/")
                && !str_contains($path, '..')
                && !str_contains($path, '\\'),
            404
        );

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        $fullPath = $disk->path($path);
        abort_unless(is_file($fullPath), 404);

        return response()->file($fullPath, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function deleteSpellMedia(Character $character, Spell $spell): JsonResponse
    {
        Gate::authorize('update', $character);
        $personal = CharacterSpell::query()
            ->where('character_id', $character->id)->where('spell_id', $spell->id)
            ->firstOrFail();
        $oldPath = $personal->personal_media_path;
        $personal->personal_media_path = null;
        $personal->save();
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }
        return response()->json(['success' => true, 'personal_media_url' => null]);
    }

    public function destroySpell(
        Character $character,
        Spell $spell
    ): JsonResponse {
        Gate::authorize('update', $character);

        CharacterSpell::query()
            ->where(
                'character_id',
                $character->id
            )
            ->where(
                'spell_id',
                $spell->id
            )
            ->delete();

        return response()->json([
            'success' => true,

            'message' =>
                "{$spell->name} foi removido do Grimório.",
        ]);
    }

    public function storeProfile(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated =
            $this->validateProfile(
                $request,
                $character
            );

        $validated =
            $this->sheetSync
                ->applyLinkedClass(
                    $character,
                    $validated
                );

        // V4 trabalha com bônus adicionais, não com valores absolutos.
        $validated['spell_save_dc_override'] = null;
        $validated['spell_attack_bonus_override'] = null;

        $profile =
            CharacterSpellcasting::query()
                ->create([
                    'character_id' =>
                        $character->id,

                    ...$validated,
                ]);

        $this->progression->sync($character);

        return response()->json([
            'success' => true,

            'message' =>
                'Conjurador adicionado.',

            'profile_id' =>
                (int) $profile->id,
        ]);
    }

    public function updateProfile(
        Request $request,
        Character $character,
        CharacterSpellcasting $profile
    ): JsonResponse {
        Gate::authorize('update', $character);

        abort_unless(
            (int) $profile->character_id
            ===
            (int) $character->id,
            404
        );

        $validated =
            $this->validateProfile(
                $request,
                $character,
                $profile
            );

        $validated =
            $this->sheetSync
                ->applyLinkedClass(
                    $character,
                    $validated
                );

        $validated['spell_save_dc_override'] = null;
        $validated['spell_attack_bonus_override'] = null;

        $profile->fill($validated);
        $profile->save();

        CharacterSpell::query()
            ->where(
                'character_id',
                $character->id
            )
            ->where(
                'spellcasting_id',
                $profile->id
            )
            ->update([
                'source_class' =>
                    $profile->class,
            ]);

        $this->progression->sync($character);

        return response()->json([
            'success' => true,
            'message' =>
                'Conjurador atualizado.',
        ]);
    }

    public function destroyProfile(
        Character $character,
        CharacterSpellcasting $profile
    ): JsonResponse {
        Gate::authorize('update', $character);

        abort_unless(
            (int) $profile->character_id
            ===
            (int) $character->id,
            404
        );

        DB::transaction(
            function () use (
                $character,
                $profile
            ): void {
                CharacterSpell::query()
                    ->where(
                        'character_id',
                        $character->id
                    )
                    ->where(
                        'spellcasting_id',
                        $profile->id
                    )
                    ->update([
                        'spellcasting_id' =>
                            null,
                    ]);

                DB::table('character_spell_slots')
                    ->where(
                        'character_id',
                        $character->id
                    )
                    ->where(
                        'slot_type',
                        'pact:'
                        . $profile->id
                    )
                    ->delete();

                $profile->delete();
            }
        );

        $this->progression->sync($character);

        return response()->json([
            'success' => true,

            'message' =>
                'Conjurador removido. Os feitiços permaneceram no Grimório.',
        ]);
    }

    public function importProfilesFromSheet(
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $character->load('classes');

        $created = $this->sheetSync
            ->importDetectedProfiles(
                $character
            );

        $this->sheetSync
            ->syncLinkedProfiles(
                $character
            );

        $this->progression
            ->sync(
                $character
            );

        return response()->json([
            'success' => true,

            'created' =>
                $created,

            'message' =>
                $created > 0
                    ? sprintf(
                        '%d perfil(is) criado(s) a partir da ficha.',
                        $created
                    )
                    : 'Nenhum novo conjurador reconhecido para importar.',
        ]);
    }

    public function updateSlot(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated =
            $request->validate([
                'slot_type' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'slot_level' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:9',
                ],

                'manual_maximum' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:99',
                ],

                'current' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:99',
                ],
            ]);

        $slot =
            $this->progression
                ->updateSlot(
                    $character,
                    $validated['slot_type'],
                    (int)
                    $validated['slot_level'],
                    $validated['manual_maximum']
                    !== null
                        ? (int)
                        $validated['manual_maximum']
                        : null,
                    (int)
                    $validated['current']
                );

        return response()->json([
            'success' => true,

            'message' =>
                'Slots atualizados.',

            'slot' => [
                'id' =>
                    (int) $slot->id,

                'slot_type' =>
                    $slot->slot_type,

                'slot_level' =>
                    (int) $slot->slot_level,

                'current' =>
                    (int) $slot->current,

                'maximum' =>
                    (int) $slot->maximum,

                'calculated_maximum' =>
                    $slot->calculated_maximum,

                'manual_maximum' =>
                    $slot->manual_maximum,

                'recovery' =>
                    $slot->recovery,
            ],
        ]);
    }

    public function cast(
        Request $request,
        Character $character,
        Spell $spell
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated =
            $request->validate([
                'resource_type' => [
                    'required',
                    Rule::in([
                        'slot',
                        'free',
                        'none',
                    ]),
                ],

                'spellcasting_id' => [
                    'nullable', 'integer',
                ],

                'slot_type' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'cast_level' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:9',
                ],
                'confirm_replace_concentration' => [
                    'sometimes',
                    'boolean',
                ],
            ]);

        $characterSpell =
            CharacterSpell::query()
                ->where(
                    'character_id',
                    $character->id
                )
                ->where(
                    'spell_id',
                    $spell->id
                )
                ->firstOrFail();

        $result =
            $this->caster
                ->cast(
                    $character,
                    $characterSpell,
                    $spell,
                    $validated
                );

        return response()->json([
            'success' => true,

            'message' =>
                "{$spell->name} conjurado.",

            'cast' =>
                $result,
        ]);
    }

    public function stopConcentration(
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $result = app(CharacterConcentrationManager::class)->stop($character);

        return response()->json([
            'success' => true,
            'message' => 'Concentração encerrada.',
            'concentration' => $result,
        ]);
    }

    public function concentrationCheck(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated = $request->validate([
            'damage' => [
                'required',
                'integer',
                'min:1',
                'max:1000000',
            ],
            'roll_total' => ['nullable', 'integer', 'min:-1000', 'max:1000'],
            'spell_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $activeForm = CharacterTransformationState::query()
            ->with('transformation')
            ->where('character_id', $character->id)
            ->where('is_active', true)->first();
        if ($activeForm?->transformation?->requires_concentration) {
            throw ValidationException::withMessages([
                'concentration' => 'A concentração atual pertence à transformação. Use o teste de Transformações.',
            ]);
        }

        $combat = $character->combat()->first();

        if (
            !$combat
            || !(bool) $combat->concentration_active
            || !$combat->concentration_spell_id
        ) {
            throw ValidationException::withMessages([
                'concentration' =>
                    'O personagem não está concentrando em um feitiço.',
            ]);
        }

        $spell = Spell::query()
            ->find($combat->concentration_spell_id);

        if (!$spell || !(bool) $spell->concentration) {
            throw ValidationException::withMessages([
                'concentration' =>
                    'A concentração ativa não pertence a um feitiço válido.',
            ]);
        }

        $exhaustionRuleActive =
            (bool) data_get(
                is_array($character->sheet_settings ?? null)
                    ? $character->sheet_settings
                    : [],
                'optional_rules.exhaustion',
                false
            );

        $exhaustionLevel =
            $exhaustionRuleActive
                ? max(
                    0,
                    min(
                        6,
                        (int) ($combat->exhaustion_level ?? 0)
                    )
                )
                : 0;

        $check = $this->concentrationChecks->forDamage(
            damage: (int) $validated['damage'],
            constitutionSaveModifier:
                $this->characterConstitutionSaveModifier($character),
            exhaustionPenalty:
                $exhaustionLevel * 2,
            context: [
                'source' => 'spell',
                'character_id' => (int) $character->id,
                'spell_id' => (int) $spell->id,
                'spell_name' => (string) $spell->name,
            ]
        );

        if (array_key_exists('roll_total', $validated) && $validated['roll_total'] !== null) {
            if ((int) ($validated['spell_id'] ?? 0) !== (int) $spell->id) {
                throw ValidationException::withMessages([
                    'spell_id' => 'O feitiço concentrado mudou antes da resolução do teste.',
                ]);
            }
            $result = app(CharacterConcentrationManager::class)->resolveSpell(
                $character,
                (int) $spell->id,
                (int) $validated['damage'],
                (int) $validated['roll_total']
            );
            return response()->json([
                'success' => true,
                'resolved' => true,
                'result' => $result,
            ]);
        }

        return response()->json([
            'success' => true,
            'concentration_check' => $check,
        ]);
    }

    public function storeLoadout(
        Request $request,
        Character $character
    ): JsonResponse {
        Gate::authorize('update', $character);

        $validated = $this->validateLoadout($request, $character);

        $loadout = DB::transaction(function () use (
            $character,
            $validated
        ): CharacterSpellLoadout {
            $loadout = CharacterSpellLoadout::query()->create([
                'character_id' => $character->id,
                'spellcasting_id' => $validated['spellcasting_id'] ?? null,
                'name' => trim((string) $validated['name']),
                'description' => $this->nullableTrimmedString(
                    $validated['description'] ?? null
                ),
                'sort_order' => CharacterSpellLoadout::query()
                    ->where('character_id', $character->id)
                    ->max('sort_order') + 1,
            ]);

            $loadout->spells()->sync(
                array_key_exists('spell_ids', $validated)
                    ? $this->loadoutSpellIds(
                        $character,
                        $loadout->spellcasting_id,
                        $validated['spell_ids'] ?? []
                    )
                    : $this->currentPreparedSpellIds(
                        $character,
                        $loadout->spellcasting_id
                    )
            );

            return $loadout;
        });

        return response()->json([
            'success' => true,
            'message' => "Loadout {$loadout->name} criado.",
            'loadout_id' => (int) $loadout->id,
        ]);
    }

    public function updateLoadout(
        Request $request,
        Character $character,
        CharacterSpellLoadout $loadout
    ): JsonResponse {
        Gate::authorize('update', $character);
        $this->assertLoadoutOwner($character, $loadout);

        $validated = $this->validateLoadout($request, $character, $loadout);

        $loadout->fill([
            'spellcasting_id' => $validated['spellcasting_id'] ?? null,
            'name' => trim((string) $validated['name']),
            'description' => $this->nullableTrimmedString(
                $validated['description'] ?? null
            ),
        ]);
        $loadout->save();

        if (array_key_exists('spell_ids', $validated)) {
            $loadout->spells()->sync(
                $this->loadoutSpellIds(
                    $character,
                    $loadout->spellcasting_id,
                    $validated['spell_ids'] ?? []
                )
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Loadout atualizado.',
        ]);
    }

    public function overwriteLoadout(
        Character $character,
        CharacterSpellLoadout $loadout
    ): JsonResponse {
        Gate::authorize('update', $character);
        $this->assertLoadoutOwner($character, $loadout);

        $loadout->spells()->sync(
            $this->currentPreparedSpellIds(
                $character,
                $loadout->spellcasting_id
            )
        );

        return response()->json([
            'success' => true,
            'message' => 'Loadout sobrescrito com a preparação atual.',
        ]);
    }

    public function applyLoadout(
        Character $character,
        CharacterSpellLoadout $loadout
    ): JsonResponse {
        Gate::authorize('update', $character);
        $this->assertLoadoutOwner($character, $loadout);

        DB::transaction(function () use (
            $character,
            $loadout
        ): void {
            $scope = CharacterSpell::query()
                ->where('character_id', $character->id)
                ->where('always_prepared', false)
                ->whereHas(
                    'spell',
                    fn ($query) => $query->where('level', '>', 0)
                );

            if ($loadout->spellcasting_id !== null) {
                $scope->where(
                    'spellcasting_id',
                    $loadout->spellcasting_id
                );
            }

            $scope->update([
                'prepared' => false,
            ]);

            $spellIds = $loadout->spells()
                ->where('spells.level', '>', 0)
                ->pluck('spells.id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if ($spellIds !== []) {
                $prepare = CharacterSpell::query()
                    ->where('character_id', $character->id)
                    ->where('always_prepared', false)
                    ->whereIn('spell_id', $spellIds);

                if ($loadout->spellcasting_id !== null) {
                    $prepare->where(
                        'spellcasting_id',
                        $loadout->spellcasting_id
                    );
                }

                $prepare->update([
                    'prepared' => true,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Loadout {$loadout->name} aplicado.",
        ]);
    }

    public function destroyLoadout(
        Character $character,
        CharacterSpellLoadout $loadout
    ): JsonResponse {
        Gate::authorize('update', $character);
        $this->assertLoadoutOwner($character, $loadout);

        $name = $loadout->name;
        $loadout->delete();

        return response()->json([
            'success' => true,
            'message' => "Loadout {$name} removido.",
        ]);
    }

    private function validateProfile(
        Request $request,
        Character $character,
        ?CharacterSpellcasting $profile = null
    ): array {
        $uniqueClass =
            Rule::unique(
                'character_spellcasting',
                'class'
            )
                ->where(
                    fn ($query) =>
                        $query->where(
                            'character_id',
                            $character->id
                        )
                );

        if ($profile) {
            $uniqueClass =
                $uniqueClass->ignore(
                    $profile->id
                );
        }

        return $request->validate([
            'character_class_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'character_classes',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'character_id',
                            $character->id
                        )
                ),
            ],

            'sync_with_character_class' => [
                'required',
                'boolean',
            ],

            'class' => [
                'required',
                'string',
                'max:80',
                $uniqueClass,
            ],

            'ability' => [
                'required',
                Rule::in([
                    'intelligence',
                    'wisdom',
                    'charisma',
                    'strength',
                    'dexterity',
                    'constitution',
                ]),
            ],

            'caster_level' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],

            'caster_type' => [
                'required',
                Rule::in([
                    'full',
                    'half',
                    'half_up',
                    'third',
                    'pact',
                    'custom',
                ]),
            ],

            'slot_mode' => [
                'required',
                Rule::in([
                    'automatic',
                    'manual',
                ]),
            ],

            'effective_caster_level_override' => [
                'nullable',
                'integer',
                'min:0',
                'max:20',
            ],

            'spell_save_dc_override' => [
                'nullable',
                'integer',
                'min:0',
                'max:99',
            ],

            'spell_attack_bonus_override' => [
                'nullable',
                'integer',
                'min:-99',
                'max:99',
            ],

            'spell_save_dc_bonus' => [
                'nullable',
                'integer',
                'min:-20',
                'max:20',
            ],

            'spell_attack_bonus_extra' => [
                'nullable',
                'integer',
                'min:-20',
                'max:20',
            ],

            'spell_list_codes' => [
                'nullable',
                'array',
            ],

            'spell_list_codes.*' => [
                'string',
                Rule::in(collect($this->classLists->availableLists())->pluck('code')->all()),
            ],

            'spell_limit' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
            ],

            'prepared_limit' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);
    }

    private function profilePayload(
        CharacterSpellcasting $profile
    ): array {
        return [
            'id' =>
                (int) $profile->id,

            'character_class_id' =>
                $profile->character_class_id
                !== null
                    ? (int) $profile->character_class_id
                    : null,

            'sync_with_character_class' =>
                (bool) $profile->sync_with_character_class,

            'linked_class' =>
                $profile->characterClass
                    ? [
                        'id' =>
                            (int) $profile->characterClass->id,

                        'class' =>
                            (string) $profile->characterClass->class,

                        'subclass' =>
                            $profile->characterClass->subclass
                            ?: null,

                        'level' =>
                            (int) $profile->characterClass->level,
                    ]
                    : null,

            'class' =>
                $profile->class,

            'ability' =>
                $profile->ability,

            'caster_level' =>
                (int)
                $profile->caster_level,

            'caster_type' =>
                $profile->caster_type,

            'slot_mode' =>
                $profile->slot_mode,

            'effective_caster_level_override' =>
                $profile
                    ->effective_caster_level_override,

            'spell_save_dc_override' =>
                $profile
                    ->spell_save_dc_override,

            'spell_attack_bonus_override' =>
                $profile
                    ->spell_attack_bonus_override,

            'spell_save_dc_bonus' =>
                (int) ($profile->spell_save_dc_bonus ?? 0),

            'spell_attack_bonus_extra' =>
                (int) ($profile->spell_attack_bonus_extra ?? 0),

            'spell_list_codes' =>
                array_values($profile->spell_list_codes ?? []),

            'spell_limit' =>
                $profile->spell_limit !== null
                    ? (int) $profile->spell_limit
                    : null,

            'prepared_limit' =>
                $profile->prepared_limit !== null
                    ? (int) $profile->prepared_limit
                    : null,

            'base_spell_save_dc' =>
                8
                + (int) ($profile->character?->proficiency_bonus ?? 0)
                + $profile->ability_modifier,

            'base_spell_attack_bonus' =>
                (int) ($profile->character?->proficiency_bonus ?? 0)
                + $profile->ability_modifier,

            'ability_modifier' =>
                $profile->ability_modifier,

            'spell_save_dc' =>
                $profile->spell_save_dc,

            'spell_attack_bonus' =>
                $profile->spell_attack_bonus,

            'is_active' =>
                (bool)
                $profile->is_active,

            'sort_order' =>
                (int)
                $profile->sort_order,

            'notes' =>
                $profile->notes,
        ];
    }

    private function validateLoadout(
        Request $request,
        Character $character,
        ?CharacterSpellLoadout $loadout = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'spellcasting_id' => [
                'nullable',
                'integer',
                Rule::exists('character_spellcasting', 'id')
                    ->where(
                        fn ($query) => $query->where(
                            'character_id',
                            $character->id
                        )
                    ),
            ],

            'spell_ids' => [
                'sometimes',
                'array',
                'max:500',
            ],

            'spell_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('spells', 'id'),
            ],
        ]);
    }

    private function loadoutSpellIds(
        Character $character,
        ?int $spellcastingId,
        array $requestedIds
    ): array {
        $requestedIds = collect($requestedIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($requestedIds->isEmpty()) {
            return [];
        }

        $query = CharacterSpell::query()
            ->where('character_id', $character->id)
            ->where('always_prepared', false)
            ->whereIn('spell_id', $requestedIds->all())
            ->whereHas(
                'spell',
                fn ($builder) => $builder->where('level', '>', 0)
            );

        if ($spellcastingId !== null) {
            $query->where('spellcasting_id', $spellcastingId);
        }

        return $query
            ->pluck('spell_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function currentPreparedSpellIds(
        Character $character,
        ?int $spellcastingId
    ): array {
        $query = CharacterSpell::query()
            ->where('character_id', $character->id)
            ->where('prepared', true)
            ->where('always_prepared', false)
            ->whereHas(
                'spell',
                fn ($builder) => $builder->where('level', '>', 0)
            );

        if ($spellcastingId !== null) {
            $query->where('spellcasting_id', $spellcastingId);
        }

        return $query
            ->pluck('spell_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function assertLoadoutOwner(
        Character $character,
        CharacterSpellLoadout $loadout
    ): void {
        abort_unless(
            (int) $loadout->character_id
            ===
            (int) $character->id,
            404
        );
    }

    private function nullableTrimmedString(
        mixed $value
    ): ?string {
        $value = trim((string) $value);
        return $value !== '' ? $value : null;
    }

    private function characterConstitutionSaveModifier(
        Character $character
    ): int {
        $abilities = $character
            ->abilities()
            ->first();

        $temporaryBonuses = is_array(
            $abilities?->temporary_bonuses
        )
            ? $abilities->temporary_bonuses
            : [];

        $overrides = is_array(
            $abilities?->overrides
        )
            ? $abilities->overrides
            : [];

        $baseScore = (int) (
            $abilities?->constitution
            ?? 10
        );

        $effectiveScore = array_key_exists(
            'constitution',
            $overrides
        )
            ? (int) $overrides['constitution']
            : (
                $baseScore
                + (int) (
                    $temporaryBonuses['constitution']
                    ?? $temporaryBonuses['con']
                    ?? 0
                )
            );

        $abilityModifier = (int) floor(
            ($effectiveScore - 10) / 2
        );

        $savingThrow = $character
            ->savingThrows()
            ->where('ability', 'constitution')
            ->first();

        $temporarySaveBonus = (int) (
            $savingThrow?->temporary_bonus
            ?? 0
        );

        if (
            $savingThrow
            && $savingThrow->bonus_override !== null
        ) {
            return (int) $savingThrow->bonus_override
                + $temporarySaveBonus;
        }

        $level = max(
            1,
            (int) ($character->level ?? 1)
        );

        $calculatedProficiency = match (true) {
            $level >= 17 => 6,
            $level >= 13 => 5,
            $level >= 9 => 4,
            $level >= 5 => 3,
            default => 2,
        };

        $proficiencyBonus = (int) (
            $character->proficiency_bonus
            ?? $calculatedProficiency
        );

        return $abilityModifier
            + (
                (bool) ($savingThrow?->proficient ?? false)
                    ? $proficiencyBonus
                    : 0
            )
            + $temporarySaveBonus;
    }

    private function allowedSpecificIds(
        ?int $campaignId,
        Collection $spellIds
    ): Collection {
        $query =
            DB::table('spells')
                ->whereIn(
                    'spells.id',
                    $spellIds->all()
                )
                ->where(
                    'spells.active',
                    true
                )
                ->where(
                    'spells.upstream_missing',
                    false
                );

        if (!$campaignId) {
            return $query
                ->where(
                    'spells.provider',
                    '<>',
                    '5etools-homebrew'
                )
                ->pluck('spells.id')
                ->map(
                    fn ($id) => (int) $id
                );
        }

        $campaign =
            Campaign::query()
                ->find($campaignId);

        if (
            $campaign
            &&
            $campaign
                ->allow_all_homebrew_spells
        ) {
            return $query
                ->pluck('spells.id')
                ->map(
                    fn ($id) => (int) $id
                );
        }

        return $query
            ->where(
                function (
                    $builder
                ) use (
                    $campaignId
                ) {
                    $builder
                        ->where(
                            'spells.provider',
                            '<>',
                            '5etools-homebrew'
                        )
                        ->orWhereExists(
                            function (
                                $subquery
                            ) use (
                                $campaignId
                            ) {
                                $subquery
                                    ->selectRaw('1')
                                    ->from(
                                        'campaign_homebrew_spell_sources as chss'
                                    )
                                    ->whereColumn(
                                        'chss.homebrew_spell_source_id',
                                        'spells.homebrew_spell_source_id'
                                    )
                                    ->where(
                                        'chss.campaign_id',
                                        $campaignId
                                    );
                            }
                        );
                }
            )
            ->pluck('spells.id')
            ->map(
                fn ($id) => (int) $id
            );
    }

    private function transformationTimerPayload(Character $character): ?array
    {
        $state = CharacterTransformationState::query()
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->first();
        if (!$state?->hasActiveTransformation()) return null;

        $encounter = $state->combat_id !== null
            ? DB::table('combats')->where('id', $state->combat_id)
                ->first(['id','name','is_active','current_round','current_turn'])
            : null;
        $participant = $encounter
            ? DB::table('combat_players')
                ->where('combat_id', $encounter->id)
                ->where('character_id', $character->id)
                ->first(['initiative'])
            : null;
        $started = $state->activated_round !== null ? (int) $state->activated_round : null;
        $round = ($encounter && $encounter->is_active) ? (int) $encounter->current_round : null;
        $runtime = is_array($state->runtime_state) ? $state->runtime_state : [];
        $manual = max(0, (int) ($runtime['manual_elapsed_rounds'] ?? 0));
        $total = isset($runtime['duration_total_rounds'])
            ? (int) $runtime['duration_total_rounds'] : null;
        $elapsed = $total !== null && $state->duration_remaining !== null
            ? max(0, $total - (int) $state->duration_remaining)
            : ($manual + ($round !== null && $started !== null ? max(0, $round - $started) : 0));
        return [
            'transformation_id' => (int) $state->character_transformation_id,
            'duration_remaining' => $state->duration_remaining !== null
                ? (int) $state->duration_remaining : null,
            'combat_id' => $state->combat_id !== null ? (int) $state->combat_id : null,
            'combat_name' => $encounter?->name,
            'combat_active' => (bool) ($encounter?->is_active ?? false),
            'started_round' => $started,
            'started_turn' => $state->activated_turn !== null ? (int) $state->activated_turn : null,
            'current_round' => $round,
            'current_turn' => $encounter ? (int) $encounter->current_turn : null,
            'elapsed_rounds' => $elapsed,
            'initiative' => $participant?->initiative !== null ? (int) $participant->initiative : null,
        ];
    }

    private function concentrationPayload(
        Character $character
    ): array {
        $combatState =
            $character->combat;

        // Compatibilidade com concentrações antigas de feitiço e novas formas.
        // A identidade original do Character é a única fonte do estado.
        $transformationState = CharacterTransformationState::query()
            ->with('transformation')
            ->where('character_id', $character->id)
            ->where('is_active', true)
            ->first();
        $concentratingTransformation = $transformationState?->transformation;
        if (!$concentratingTransformation?->requires_concentration) {
            $concentratingTransformation = null;
        }

        $shared = app(CharacterConcentrationManager::class)->currentConcentration($character);
        $active = $shared !== null;
        $source = $shared['source'] ?? null;
        $spellId = $source === 'spell' ? ($shared['id'] ?? null) : null;

        $spell = $spellId ? Spell::query()->find((int) $spellId) : null;
        $spellName = $spell?->name;

        $overrides =
            $this->normalizeArray(
                $combatState?->overrides
            );

        $meta =
            data_get(
                $overrides,
                'concentration',
                []
            );

        $startedCombatId =
            is_array($meta)
            && isset($meta['combat_id'])
            && $meta['combat_id'] !== null
                ? (int) $meta['combat_id']
                : null;

        $startedRound =
            is_array($meta)
            && isset($meta['started_round'])
            && $meta['started_round'] !== null
                ? max(
                    1,
                    (int) $meta['started_round']
                )
                : null;

        $encounter = null;

        if (
            $active
            &&
            $startedCombatId
        ) {
            $encounter =
                DB::table('combats')
                    ->where(
                        'id',
                        $startedCombatId
                    )
                    ->first([
                        'id',
                        'name',
                        'current_round',
                        'current_turn',
                        'is_active',
                    ]);
        }

        // Preserva o tempo decorrido mesmo depois que o combate termina.
        $currentRound = $encounter
            ? max(0, (int) $encounter->current_round)
            : null;

        $offlineRounds = is_array($meta)
            ? max(0, (int) ($meta['manual_elapsed_rounds'] ?? 0)) : 0;
        if ($source === 'feature' && !empty($shared['id'])) {
            $featureState = DB::table('character_feature_effect_states')
                ->where('character_id', $character->id)
                ->where('character_feature_id', (int) $shared['id'])
                ->where('is_active', true)
                ->first(['duration_total_rounds', 'duration_remaining']);
            if ($featureState) {
                $offlineRounds = max(0,
                    (int) $featureState->duration_total_rounds - (int) $featureState->duration_remaining);
            }
        } elseif ($source === 'transformation' && $transformationState) {
            $runtime = is_array($transformationState->runtime_state)
                ? $transformationState->runtime_state : [];
            $offlineRounds = max(0, (int) ($runtime['manual_elapsed_rounds'] ?? 0));
        }

        $elapsedRounds =
            $currentRound !== null && $startedRound !== null
                ? max(0, $currentRound - $startedRound) + $offlineRounds
                : ($active ? $offlineRounds : null);

        // Limite real da magia, não o tempo transcorrido. Magias sem limite
        // numérico permanecem acompanháveis sem expiração automática.
        $spellDuration = $spell ? app(SpellDurationResolver::class)->resolve($spell) : null;
        $durationTotal = $source === 'spell'
            ? (isset($meta['duration_total_rounds']) && is_numeric($meta['duration_total_rounds'])
                ? (int) $meta['duration_total_rounds']
                : ($spellDuration['total_rounds'] ?? null))
            : null;
        $durationRemaining = $durationTotal !== null && $elapsedRounds !== null
            ? max(0, $durationTotal - $elapsedRounds)
            : null;

        return [
            'active' => $active,
            'source' => $active ? $source : null,
            'effect_name' => $shared['name'] ?? null,
            'feature_id' => $source === 'feature' ? (int) $shared['id'] : null,
            'transformation_id' => $concentratingTransformation
                ? (int) $concentratingTransformation->id
                : null,
            'transformation_type' => $concentratingTransformation?->type,

            'spell_id' =>
                $spellId
                    ? (int) $spellId
                    : null,

            'spell_name' =>
                $spellName,

            'duration_label' => $source === 'spell' ? ($spellDuration['label'] ?? null) : null,
            'duration_total_rounds' => $durationTotal,
            'duration_remaining' => $durationRemaining,

            'combat_id' =>
                $encounter
                    ? (int) $encounter->id
                    : null,

            'combat_name' =>
                $encounter?->name,

            'combat_active' =>
                $encounter
                    ? (bool) $encounter->is_active
                    : false,

            'started_round' =>
                $startedRound,

            'current_round' =>
                $currentRound,

            'elapsed_rounds' =>
                $elapsedRounds,

            'elapsed_seconds' =>
                $elapsedRounds !== null
                    ? $elapsedRounds * 6
                    : null,

            'tracking_rounds' =>
                $encounter !== null
                && (bool) $encounter->is_active
                &&
                $startedRound !== null,
        ];
    }

    private function normalizeArray(
        mixed $value
    ): array {
        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
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

    private function activeCampaigns(
        Character $character
    ): Collection {
        return Campaign::query()
            ->whereHas(
                'characters',
                function (
                    $query
                ) use (
                    $character
                ) {
                    $query
                        ->where(
                            'characters.id',
                            $character->id
                        )
                        ->where(
                            'campaign_characters.is_active',
                            true
                        );
                }
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'allow_all_homebrew_spells',
            ]);
    }

    private function resolveSelectedCampaign(
        Collection $campaigns,
        ?int $campaignId
    ): ?Campaign {
        if ($campaigns->isEmpty()) {
            return null;
        }

        if ($campaignId) {
            $selected =
                $campaigns->firstWhere(
                    'id',
                    $campaignId
                );

            if ($selected) {
                return $selected;
            }
        }

        return $campaigns->first();
    }

    private function resolveCampaignForMutation(
        Request $request,
        Character $character
    ): ?Campaign {
        $campaigns =
            $this->activeCampaigns(
                $character
            );

        $requestedId =
            $request->integer(
                'campaign_id'
            );

        if (
            $requestedId
            &&
            !$campaigns->contains(
                'id',
                $requestedId
            )
        ) {
            abort(404);
        }

        return $this
            ->resolveSelectedCampaign(
                $campaigns,
                $requestedId
            );
    }
}