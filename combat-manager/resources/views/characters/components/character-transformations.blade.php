@props([
    'character',
])

@php
    /*
    |--------------------------------------------------------------------------
    | TRANSFORMAÇÕES — V14
    |--------------------------------------------------------------------------
    |
    | Este componente é apenas a camada de presença/seleção da transformação
    | dentro da ficha. O editor e as ações de ativar/encerrar serão conectados
    | aos endpoints próprios na próxima etapa.
    |
    | A regra opcional controla completamente a presença do módulo.
    |
    */

    $sheetSettings =
        is_array(
            $character->sheet_settings
            ?? null
        )
            ? $character->sheet_settings
            : [];


    /*
    |--------------------------------------------------------------------------
    | TEMA VISUAL
    |--------------------------------------------------------------------------
    |
    | Os modais deste componente usam x-teleport="body". Como isso os tira
    | de dentro do wrapper da ficha, carregamos o tema aqui e reaplicamos o
    | contexto em cada portal para que os tokens --theme-* continuem válidos.
    |
    */
    $characterTheme =
        trim(
            (string) data_get(
                $sheetSettings,
                'appearance.theme',
                'spellbound'
            )
        );

    if ($characterTheme === '') {
        $characterTheme = 'spellbound';
    }

    $characterTheme =
        preg_replace(
            '/[^a-z0-9_-]/i',
            '',
            $characterTheme
        )
        ?: 'spellbound';

    $transformationsEnabled =
        (bool) data_get(
            $sheetSettings,
            'optional_rules.transformations',
            false
        );

    $transformations =
        collect();

    $activeState =
        null;

    $activeTransformation =
        null;

    if ($transformationsEnabled) {
        if (
            method_exists(
                $character,
                'transformations'
            )
        ) {
            $transformations =
                $character
                    ->transformations()
                    ->with([
                        'formCharacter',
                        'sourceNpc',
                    ])
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
        }

        if (
            method_exists(
                $character,
                'transformationState'
            )
        ) {
            $activeState =
                $character
                    ->transformationState()
                    ->with(
                        'transformation.sourceNpc'
                    )
                    ->first();

            if (
                $activeState
                && $activeState->is_active
            ) {
                $activeTransformation =
                    $activeState->transformation;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS DE IMAGEM / FORMATO
    |--------------------------------------------------------------------------
    */
    $publicImageUrl =
        function (?string $path): ?string {
            if (!$path) {
                return null;
            }

            $path =
                str_replace(
                    '\\',
                    '/',
                    trim(
                        $path
                    )
                );

            if ($path === '') {
                return null;
            }

            if (
                str_starts_with(
                    $path,
                    'http://'
                )
                || str_starts_with(
                    $path,
                    'https://'
                )
            ) {
                return $path;
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALIZAÇÃO DE CAMINHOS ANTIGOS
            |--------------------------------------------------------------------------
            |
            | A imagem da transformação é salva no disk "public" como:
            |
            | character-transformations/arquivo.webp
            |
            | Alguns registros antigos podem carregar prefixos de filesystem
            | ou de URL. Removemos tudo isso antes de procurar o arquivo.
            |--------------------------------------------------------------------------
            */
            foreach (
                [
                    'storage/app/public/',
                    'public/storage/',
                ]
                as $prefix
            ) {
                $position =
                    stripos(
                        $path,
                        $prefix
                    );

                if ($position !== false) {
                    $path =
                        substr(
                            $path,
                            $position
                            + strlen(
                                $prefix
                            )
                        );

                    break;
                }
            }

            $path =
                ltrim(
                    $path,
                    '/'
                );

            foreach (
                [
                    'storage/',
                    'public/',
                ]
                as $prefix
            ) {
                if (
                    str_starts_with(
                        $path,
                        $prefix
                    )
                ) {
                    $path =
                        substr(
                            $path,
                            strlen(
                                $prefix
                            )
                        );
                }
            }

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk =
                \Illuminate\Support\Facades\Storage::disk(
                    'public'
                );

            $candidates = [
                $path,
            ];

            /*
             * Recupera registros que tenham salvo apenas um caminho antigo
             * contendo o mesmo nome do arquivo.
             */
            $basename =
                basename(
                    $path
                );

            if ($basename !== '') {
                $candidates[] =
                    'character-transformations/'
                    . $basename;
            }

            $resolvedPath =
                collect(
                    $candidates
                )
                    ->filter()
                    ->unique()
                    ->first(
                        fn ($candidate) =>
                            $disk->exists(
                                $candidate
                            )
                    );

            if (!$resolvedPath) {
                return null;
            }

            /*
             * URL relativa deliberadamente.
             *
             * Assim a imagem usa o mesmo host/porta em que a ficha foi aberta
             * e não depende de APP_URL antigo, Hamachi ou outro endereço.
             */
            return
                '/storage/'
                . ltrim(
                    $resolvedPath,
                    '/'
                );
        };


    $isNpcBuilder =
        function ($npc): bool {
            if (!$npc) {
                return false;
            }

            $json =
                $npc->json_data
                ?? [];

            if (is_string($json)) {
                $decoded =
                    json_decode(
                        $json,
                        true
                    );

                $json =
                    is_array(
                        $decoded
                    )
                        ? $decoded
                        : [];
            }

            return (
                is_array(
                    $json
                )
                && (
                    $json['format']
                    ?? null
                )
                ===
                'npc-builder'
            );
        };


    /* Imagem padrão: retrato próprio do NPC, incluindo fontes importadas. */
    $npcPortraitUrl = function ($npc) use ($publicImageUrl): ?string {
        if (!$npc) return null;
        $data = $npc->json_data ?? [];
        if (is_string($data)) $data = json_decode($data, true) ?: [];
        if (!is_array($data)) $data = [];
        $paths = [
            $npc->image_path ?? null,
            data_get($data, 'image_path'),
            data_get($data, 'image'),
            data_get($data, 'portrait'),
        ];
        foreach ($paths as $path) {
            if (!is_string($path) || trim($path) === '') continue;
            $url = $publicImageUrl($path);
            if ($url) return $url;
        }
        return null;
    };

    /*
    |--------------------------------------------------------------------------
    | FICHA DE MONSTRO ATIVA — NPC BUILDER
    |--------------------------------------------------------------------------
    |
    | O Cofre trabalha com dois contratos diferentes:
    |
    | Native:
    |   json_data.format === "npc-builder"
    |
    | Normal:
    |   importação / 5emm, interpretada pelo NpcViewModelFactory.
    |
    | A Blade não tenta mais descobrir qual contrato recebeu.
    | MonsterTransformationSheetResolver converte ambos para o mesmo payload.
    |--------------------------------------------------------------------------
    */
    $activeMonsterNpc =
        null;

    $activeMonsterSheetData =
        null;

    $activeMonsterUnsupported =
        false;

    if (
        $activeTransformation
        && $activeTransformation->type === 'monster_sheet'
    ) {
        $activeMonsterNpc =
            $activeTransformation->sourceNpc;

        if ($activeMonsterNpc) {
            try {
                $activeMonsterSheetData =
                    app(
                        \App\Services\Transformations\MonsterTransformationSheetResolver::class
                    )->resolve(
                        $activeMonsterNpc
                    );

            } catch (\Throwable $exception) {
                report(
                    $exception
                );

                $activeMonsterSheetData =
                    null;

                $activeMonsterUnsupported =
                    !$isNpcBuilder(
                        $activeMonsterNpc
                    );
            }
        }
    }

    /*
     * A miniatura pertence exclusivamente à transformação.
     *
     * A foto do Character principal NÃO é usada como fallback aqui:
     * ela já existe no cabeçalho da ficha e repeti-la seria redundante.
     */
    $imageUrl = $activeTransformation
        ? ($publicImageUrl($activeTransformation->image_path)
            ?: ($activeTransformation->isMonsterSheet()
                ? $npcPortraitUrl($activeTransformation->sourceNpc)
                : null))
        : null;

    $typeLabels = [
        'character_sheet' =>
            'Nova Ficha',

        'monster_sheet' =>
            'Ficha de Monstro',
    ];

    $castingLabels = [
        'none' =>
            null,

        'spell' =>
            null,

        'bonus_action' =>
            'Ação Bônus',

        'action' =>
            'Ação Padrão',
    ];

    $durationLabels = [
        'indefinite' =>
            'Até ser encerrada',

        'rounds' =>
            'rodada',

        'minutes' =>
            'minuto',

        'hours' =>
            'hora',
    ];



    $transformationPayload =
        $transformations
            ->map(
                function ($transformation) use (
                    $typeLabels,
                    $castingLabels,
                    $durationLabels,
                    $character,
                    $isNpcBuilder,
                    $publicImageUrl,
                    $npcPortraitUrl
                ) {
                    $durationFormula = (string) data_get($transformation->settings, 'duration_formula', '');
                    $durationMode = $durationFormula !== '' ? 'dice' : 'fixed';
                    $durationType =
                        (string) (
                            $transformation->duration_type
                            ?? 'indefinite'
                        );

                    $durationValue =
                        $transformation->duration_value !== null
                            ? max(
                                0,
                                (int) $transformation->duration_value
                            )
                            : null;

                    $durationLabel = $durationFormula !== ''
                        ? $durationFormula . ' rodadas'
                        : ($durationLabels[$durationType] ?? null);

                    if (
                        $durationFormula === ''
                        && $durationType !== 'indefinite'
                        && $durationValue !== null
                        && $durationLabel
                    ) {
                        $durationLabel =
                            $durationValue
                            . ' '
                            . $durationLabel
                            . (
                                $durationValue === 1
                                    ? ''
                                    : 's'
                            );
                    }

                    $usesMax =
                        $transformation->uses_max !== null
                            ? max(
                                0,
                                (int) $transformation->uses_max
                            )
                            : null;

                    $usesCurrent =
                        $transformation->uses_current !== null
                            ? max(
                                0,
                                (int) $transformation->uses_current
                            )
                            : $usesMax;

                    /*
                     * Foto própria da transformação.
                     * Sem foto própria, a interface usa apenas o emblema
                     * neutro do módulo — nunca a foto do personagem base.
                     */
                    $photo = $publicImageUrl($transformation->image_path)
                        ?: ($transformation->isMonsterSheet()
                            ? $npcPortraitUrl($transformation->sourceNpc)
                            : null);

                    $monsterSupported =
                        $transformation->type !== 'monster_sheet'
                        || $isNpcBuilder(
                            $transformation->sourceNpc
                        );

                    return [
                        'id' =>
                            (int) $transformation->id,

                        'name' =>
                            (string) $transformation->name,

                        'type' =>
                            (string) $transformation->type,

                        'type_label' =>
                            $typeLabels[
                                $transformation->type
                            ]
                            ?? 'Transformação',

                        'image_url' =>
                            $photo,

                        'source_npc_id' =>
                            $transformation->source_npc_id !== null
                                ? (int) $transformation->source_npc_id
                                : null,

                        'monster_supported' =>
                            $monsterSupported,

                        'form_character_id' =>
                            $transformation->form_character_id !== null
                                ? (int) $transformation->form_character_id
                                : null,

                        'sheet_edit_url' =>
                            (
                                $transformation->type === 'character_sheet'
                                && $transformation->form_character_id !== null
                            )
                                ? (
                                    route(
                                        'characters.show',
                                        $character
                                    )
                                    . '?transformation_sheet='
                                    . (int) $transformation->id
                                )
                                : null,

                        'casting_time' =>
                            (string) (
                                $transformation->casting_time
                                ?? 'none'
                            ),

                        'linked_spell_id' =>
                            (int) data_get($transformation->settings, 'linked_spell_id', 0) ?: null,

                        'casting_label' =>
                            $castingLabels[
                                $transformation->casting_time
                                ?? 'none'
                            ]
                            ?? null,

                        'requires_concentration' =>
                            (bool) (
                                $transformation->requires_concentration
                                ?? false
                            ),

                        'duration_type' => $durationFormula !== '' ? 'dice' : $durationType,
                        'duration_mode' => $durationMode,
                        'duration_formula' => $durationFormula,

                        'duration_value' =>
                            $durationValue,

                        'duration_label' =>
                            $durationLabel,

                        'is_special' =>
                            (bool) (
                                $transformation->is_special
                                ?? false
                            ),

                        'special_trigger' =>
                            $transformation->special_trigger,

                        'uses_max' =>
                            $usesMax,

                        'uses_current' =>
                            $usesCurrent,

                        'recovery' =>
                            $transformation->recovery,

                        'applies_exhaustion' =>
                            (bool) (
                                $transformation->applies_exhaustion
                                ?? false
                            ),

                        'exhaustion_levels' =>
                            max(
                                0,
                                (int) (
                                    $transformation->exhaustion_levels
                                    ?? 0
                                )
                            ),

                        'exhaustion_timing' =>
                            $transformation->exhaustion_timing,
                    ];
                }
            )
            ->values()
            ->all();

    /*
    |--------------------------------------------------------------------------
    | COFRE DE CRIATURAS
    |--------------------------------------------------------------------------
    |
    | O modal de criação precisa apenas dos dados de seleção.
    | A autorização real continua sendo feita no controller.
    |
    */
    $availableNpcs =
        collect();

    if ($transformationsEnabled) {
        try {
            $availableNpcs =
                collect(
                    app(\App\Services\NpcService::class)
                        ->getAllByUser((int) $character->user_id)
                );
        } catch (\Throwable $exception) {
            report($exception);
            $availableNpcs = collect();
        }
    }

    /* Magias de concentração que o personagem já possui no Grimório. */
    $hasSpellMediaRoute = \Illuminate\Support\Facades\Route::has('characters.spellbook.spells.media.show');
    $linkedSpellOptions = \App\Models\CharacterSpell::query()
        ->where('character_id', $character->id)
        ->with('spell')
        ->get()
        ->filter(fn ($entry) => $entry->spell && (bool) $entry->spell->concentration)
        ->map(function ($entry) use ($character, $hasSpellMediaRoute) {
            $mediaPath = trim((string) ($entry->personal_media_path ?? ''));
            $mediaUrl = null;
            if ($mediaPath !== '') {
                // Quando presente, a rota autentica o dono da magia.
                $mediaUrl = $hasSpellMediaRoute
                    ? route('characters.spellbook.spells.media.show', [
                        'character' => $character->id,
                        'spell' => $entry->spell_id,
                    ], false)
                    : '/storage/' . ltrim($mediaPath, '/');
            }
            return [
                'id' => (int) $entry->spell->id,
                'name' => (string) $entry->spell->name,
                'level' => (int) $entry->spell->level,
                'image_url' => $mediaUrl,
            ];
        })
        ->unique('id')
        ->sortBy([['level','asc'],['name','asc']])
        ->values()
        ->all();
    $encodedLinkedSpells = e(json_encode($linkedSpellOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    $npcPayload =
        $availableNpcs
            ->map(function ($npc) use (
                $isNpcBuilder,
                $publicImageUrl,
                $npcPortraitUrl
            ) {
                $supported =
                    $isNpcBuilder(
                        $npc
                    );

                return [
                    'id' =>
                        (int) $npc->id,

                    'name' =>
                        (string) (
                            $npc->name
                            ?? 'Criatura'
                        ),

                    'challenge_rating' =>
                        $npc->challenge_rating
                        ?? null,

                    'image_url' =>
                        $npcPortraitUrl($npc),

                    'supported' =>
                        $supported,

                    'format_label' =>
                        $supported
                            ? 'Criada no Spellbound'
                            : 'Formato externo',
                ];
            })
            ->sortBy([
                [
                    'supported',
                    'desc',
                ],
                [
                    'name',
                    'asc',
                ],
            ])
            ->values()
            ->all();


    $activePayload =
        $activeTransformation
            ? collect(
                $transformationPayload
            )->firstWhere(
                'id',
                (int) $activeTransformation->id
            )
            : null;

    $encodedTransformations =
        e(
            json_encode(
                $transformationPayload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
        );

    $encodedActive =
        e(
            json_encode(
                $activePayload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
        );


    $encodedNpcs =
        e(
            json_encode(
                $npcPayload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            )
        );
@endphp


@if ($transformationsEnabled)

    {{-- CSS global dos dados: carregado fora dos templates x-if do Alpine. --}}
    @include('components.ui.poly-die-styles')

    @once
        <style>
            /*
            |--------------------------------------------------------------------------
            | TRANSFORMAÇÕES — V4
            |--------------------------------------------------------------------------
            */

            .character-transformations-v14 {
                --tr-wine: var(--theme-text-strong);
                --tr-wine-soft: var(--theme-accent-strong);
                --tr-paper: var(--theme-surface-card);
                --tr-paper-soft: var(--theme-surface-card-alt);
                --tr-band: var(--theme-band);
                --tr-line: var(--theme-border);
                --tr-muted: var(--theme-text-secondary);
                --tr-gold: var(--theme-label);
            }

            .character-transformations-v14-card {
                overflow: hidden;
                border: 1px solid var(--tr-line);
                border-radius: 12px;
                background:
                    linear-gradient(
                        180deg,
                        rgba(255,253,248,.98),
                        rgba(247,240,230,.90)
                    );
                box-shadow:
                    inset 0 1px 0 rgba(255,255,255,.82),
                    0 4px 12px rgba(83,21,15,.045);
            }

            .character-transformations-v14-trigger {
                display: flex;
                width: 100%;
                min-height: 58px;
                align-items: center;
                gap: 10px;
                padding: 9px 10px;
                text-align: left;
                transition:
                    background .14s ease,
                    border-color .14s ease;
            }

            .character-transformations-v14-trigger:hover,
            .character-transformations-v14-trigger:focus-visible {
                background: rgba(234,219,200,.36);
                outline: none;
            }

            .character-transformations-v14-portrait {
                position: relative;
                display: flex;
                width: 38px;
                height: 42px;
                flex: 0 0 38px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px solid rgba(154,111,69,.34);
                border-radius: 9px;
                background: #eadbc8;
                color: #6b1d14;
            }

            .character-transformations-v14-portrait img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top center;
            }

            .character-transformations-v14-copy {
                min-width: 0;
                flex: 1;
            }

            .character-transformations-v14-kicker {
                display: block;
                font-size: 6.5px;
                font-weight: 900;
                line-height: 1;
                letter-spacing: .13em;
                text-transform: uppercase;
                color: var(--tr-gold);
            }

            .character-transformations-v14-name {
                display: block;
                margin-top: 3px;
                overflow: hidden;
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.1;
                color: var(--tr-wine);
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-transformations-v14-meta {
                display: flex;
                min-width: 0;
                align-items: center;
                gap: 5px;
                margin-top: 3px;
                color: var(--tr-muted);
                font-size: 7.5px;
                font-weight: 800;
            }

            .character-transformations-v14-meta span {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .character-transformations-v14-chevron {
                display: flex;
                width: 24px;
                height: 24px;
                flex: 0 0 24px;
                align-items: center;
                justify-content: center;
                border-radius: 7px;
                color: var(--tr-gold);
                transition:
                    background .14s ease,
                    color .14s ease,
                    transform .14s ease;
            }

            .character-transformations-v14-trigger:hover
            .character-transformations-v14-chevron {
                background: rgba(234,219,200,.62);
                color: var(--tr-wine);
                transform: translateX(1px);
            }

            .character-transformations-v14-active-line {
                height: 3px;
                background:
                    linear-gradient(
                        90deg,
                        #6b1d14,
                        #9a4a38,
                        #c0905f
                    );
            }

            .character-transformations-v14-modal {
                width: min(94vw, 780px);
                max-height: min(84vh, 760px);
                overflow: hidden;
                border: 1px solid rgba(154,111,69,.52);
                border-radius: 18px;
                background: #fbf8f1;
                box-shadow:
                    0 28px 80px rgba(42,23,18,.30);
            }

            .character-transformations-v14-list {
                max-height: 520px;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color:
                    rgba(140,98,57,.30)
                    transparent;
            }

            .character-transformations-v14-option {
                display: grid;

                grid-template-columns:
                    82px
                    minmax(0,1fr)
                    auto;

                gap: 14px;
                align-items: center;

                width: 100%;

                border-bottom:
                    1px solid
                    rgba(154,111,69,.20);

                padding:
                    13px 14px;

                text-align: left;

                transition:
                    background .14s ease;
            }

            .character-transformations-v14-option:hover {
                background: #f3e7d8;
            }

            .character-transformations-v14-option:hover
            .character-transformations-v14-option-image {
                border-color:
                    rgba(83,21,15,.28);
            }

            .character-transformations-v14-option:last-child {
                border-bottom: 0;
            }

            .character-transformations-v14-option-image {
                position: relative;

                display: flex;

                width: 82px;
                height: 94px;

                flex: 0 0 82px;

                align-items: center;
                justify-content: center;

                overflow: hidden;

                border:
                    1px solid
                    rgba(154,111,69,.36);

                border-radius:
                    11px;

                background:
                    linear-gradient(
                        180deg,
                        #f0e1ce,
                        #eadbc8
                    );

                color:
                    #6b1d14;

                box-shadow:
                    inset 0 1px 0
                    rgba(255,255,255,.72);
            }

            .character-transformations-v14-option-image img {
                position: absolute;
                inset: 0;
                z-index: 2;

                width: 100%;
                height: 100%;

                object-fit: cover;
                object-position: top center;
            }

            .character-transformations-v14-option-image-fallback {
                position: relative;
                z-index: 1;

                display: flex;

                align-items: center;
                justify-content: center;

                width: 100%;
                height: 100%;

                color:
                    #7d5336;
            }

            .character-transformations-v14-tag {
                display: inline-flex;
                align-items: center;
                border: 1px solid rgba(140,98,57,.22);
                border-radius: 999px;
                background: #f7f0e6;
                padding: 3px 6px;
                color: #704326;
                font-size: 7px;
                font-weight: 900;
                line-height: 1;
            }

            .character-transformations-v14-create-button {
                display: inline-flex;
                min-height: 34px;
                align-items: center;
                justify-content: center;
                gap: 6px;
                border: 1px solid rgba(83,21,15,.20);
                border-radius: 9px;
                background: #6b1d14;
                padding: 0 11px;
                color: #fffaf2;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                transition:
                    background .14s ease,
                    transform .14s ease;
            }

            .character-transformations-v14-create-button:hover {
                background: #53150f;
                transform: translateY(-1px);
            }

            .character-transformations-v14-create-modal {
                display: flex;
                width: min(96vw, 860px);
                max-height: min(92vh, 860px);
                max-height: min(92dvh, 860px);
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(154,111,69,.52);
                border-radius: 18px;
                background: #fbf8f1;
                box-shadow: 0 30px 90px rgba(42,23,18,.34);
            }

            /*
             * Cabeçalho e rodapé ficam sempre dentro da área visível.
             * Somente o conteúdo central do formulário rola.
             */
            .character-transformations-v14-create-header,
            .character-transformations-v14-create-footer {
                position: relative;
                z-index: 3;
                flex: 0 0 auto;
            }

            .character-transformations-v14-create-body {
                flex: 1 1 auto;
                min-height: 0;
                max-height: none;
                overflow-x: hidden;
                overflow-y: auto;
                overscroll-behavior: contain;
                padding: 18px;
                scrollbar-width: thin;
                scrollbar-color:
                    rgba(140,98,57,.30)
                    transparent;
            }

            .character-transformations-v14-create-footer {
                box-shadow:
                    0 -10px 24px rgba(42,23,18,.055);
            }

            .character-transformations-v14-section {
                border-top: 1px solid rgba(154,111,69,.22);
                padding-top: 16px;
                margin-top: 16px;
            }

            .character-transformations-v14-section:first-child {
                border-top: 0;
                padding-top: 0;
                margin-top: 0;
            }

            .character-transformations-v14-section-title {
                font-family: Georgia, 'Times New Roman', serif;
                font-size: 15px;
                font-weight: 900;
                color: #53150f;
            }

            .character-transformations-v14-section-copy {
                margin-top: 3px;
                font-size: 9px;
                line-height: 1.5;
                color: #755847;
            }

            .character-transformations-v14-type-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0,1fr));
                gap: 10px;
                margin-top: 11px;
            }

            .character-transformations-v14-type {
                min-height: 92px;
                border: 1px solid rgba(154,111,69,.32);
                border-radius: 12px;
                background: #fffdf8;
                padding: 12px;
                text-align: left;
                transition:
                    border-color .14s ease,
                    background .14s ease,
                    transform .14s ease;
            }

            .character-transformations-v14-type:hover {
                border-color: rgba(83,21,15,.38);
                background: #f8f0e6;
            }

            .character-transformations-v14-type.is-active {
                border-color: rgba(83,21,15,.72);
                background: #f3e7d8;
                box-shadow: inset 0 0 0 1px rgba(83,21,15,.08);
            }

            .character-transformations-v14-label {
                display: block;
                margin-bottom: 5px;
                color: #704326;
                font-size: 8px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            .character-transformations-v14-input,
            .character-transformations-v14-select {
                width: 100%;
                min-height: 38px;
                border: 1px solid rgba(154,111,69,.34);
                border-radius: 9px;
                background: #fffdf8;
                padding: 0 10px;
                color: #34231b;
                font-size: 11px;
                outline: none;
                transition:
                    border-color .14s ease,
                    box-shadow .14s ease;
            }

            .character-transformations-v14-input:focus,
            .character-transformations-v14-select:focus {
                border-color: rgba(83,21,15,.58);
                box-shadow: 0 0 0 3px rgba(83,21,15,.07);
            }

            .character-transformations-v14-segmented {
                display: grid;
                overflow: hidden;
                border: 1px solid rgba(154,111,69,.30);
                border-radius: 9px;
                background: #f7f0e6;
            }

            .character-transformations-v14-segmented.cols-2 {
                grid-template-columns: repeat(2, minmax(0,1fr));
            }

            .character-transformations-v14-segmented.cols-4 {
                grid-template-columns: repeat(4, minmax(0,1fr));
            }

            .character-transformations-v14-segmented button {
                min-height: 36px;
                border-right: 1px solid rgba(154,111,69,.20);
                padding: 6px 8px;
                color: #704326;
                font-size: 8px;
                font-weight: 900;
                text-transform: uppercase;
                transition:
                    background .14s ease,
                    color .14s ease;
            }

            .character-transformations-v14-segmented button:last-child {
                border-right: 0;
            }

            .character-transformations-v14-segmented button.is-active {
                background: #6b1d14;
                color: #fffaf2;
            }

            .character-transformations-v14-grid-2 {
                display: grid;
                grid-template-columns: repeat(2, minmax(0,1fr));
                gap: 10px;
            }

            .character-transformations-v14-toggle-row {
                display: flex;
                min-height: 46px;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                border: 1px solid rgba(154,111,69,.26);
                border-radius: 10px;
                background: #fffdf8;
                padding: 9px 11px;
            }

            .character-transformations-v14-switch {
                position: relative;
                width: 38px;
                height: 21px;
                flex: 0 0 38px;
                border-radius: 999px;
                background: #d8c7ab;
                transition: background .14s ease;
            }

            .character-transformations-v14-switch.is-active {
                background: #6b1d14;
            }

            .character-transformations-v14-switch::after {
                content: '';
                position: absolute;
                top: 2px;
                left: 2px;
                width: 17px;
                height: 17px;
                border-radius: 999px;
                background: #fffdf8;
                box-shadow: 0 1px 3px rgba(42,23,18,.18);
                transition: transform .14s ease;
            }

            .character-transformations-v14-switch.is-active::after {
                transform: translateX(17px);
            }

            .character-transformations-v14-photo {
                display: grid;
                grid-template-columns: 72px minmax(0,1fr);
                gap: 12px;
                align-items: center;
                margin-top: 10px;
            }

            .character-transformations-v14-photo-preview {
                display: flex;
                width: 72px;
                height: 82px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border: 1px dashed rgba(154,111,69,.45);
                border-radius: 11px;
                background: #eadbc8;
                color: #6b1d14;
            }

            .character-transformations-v14-photo-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top center;
            }

            .character-transformations-v14-npc-list {
                display: grid;
                grid-template-columns: repeat(2, minmax(0,1fr));
                gap: 8px;
                max-height: 260px;
                margin-top: 10px;
                overflow-y: auto;
            }

            .character-transformations-v14-npc {
                display: grid;
                grid-template-columns: 42px minmax(0,1fr);
                gap: 9px;
                align-items: center;
                border: 1px solid rgba(154,111,69,.25);
                border-radius: 10px;
                background: #fffdf8;
                padding: 7px;
                text-align: left;
                transition:
                    border-color .14s ease,
                    background .14s ease;
            }

            .character-transformations-v14-npc:hover {
                background: #f8f0e6;
            }

            .character-transformations-v14-npc.is-active {
                border-color: rgba(83,21,15,.65);
                background: #f3e7d8;
            }

            .character-transformations-v14-npc.is-unsupported {
                border-color: rgba(160,82,61,.28);
                background: #fffaf2;
            }

            .character-transformations-v14-npc.is-unsupported:hover {
                border-color: rgba(160,82,61,.42);
                background: #f8f0e6;
            }

            .character-transformations-v14-format-badge {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                margin-top: 3px;
                border: 1px solid rgba(147,98,61,.25);
                border-radius: 999px;
                padding: 2px 6px;
                background: #f3e7d8;
                color: #704326;
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .character-transformations-v14-format-badge.unsupported {
                border-color: rgba(127,38,29,.28);
                background: #fff2ee;
                color: #7f261d;
            }

            .character-transformations-v14-format-warning {
                margin-top: 10px;
                border: 1px solid rgba(127,38,29,.30);
                border-radius: 10px;
                background: #fff5f1;
                padding: 10px 11px;
                color: #70402f;
                font-size: 9px;
                line-height: 1.5;
            }

            .character-transformations-v14-format-warning strong {
                display: block;
                margin-bottom: 2px;
                color: #6b1d14;
                font-family: Georgia, Cambria, "Times New Roman", serif;
                font-size: 11px;
            }

            .character-transformations-v14-npc-image {
                display: flex;
                width: 42px;
                height: 48px;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border-radius: 8px;
                background: #eadbc8;
                color: #6b1d14;
            }

            .character-transformations-v14-npc-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top center;
            }

            .character-transformations-v14-option-actions {
                display: flex;

                max-width: 230px;

                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;

                gap: 6px;
            }

            .character-transformations-v14-action {
                display: inline-flex;
                min-height: 31px;
                align-items: center;
                justify-content: center;
                gap: 5px;
                border: 1px solid rgba(154,111,69,.30);
                border-radius: 8px;
                padding: 0 9px;
                font-size: 7px;
                font-weight: 900;
                letter-spacing: .06em;
                text-transform: uppercase;
                transition:
                    background .14s ease,
                    color .14s ease,
                    border-color .14s ease,
                    opacity .14s ease;
            }

            .character-transformations-v14-action.edit {
                background: #fffdf8;
                color: #704326;
            }

            .character-transformations-v14-action.edit:hover {
                background: #f3e7d8;
                border-color: rgba(83,21,15,.28);
                color: #53150f;
            }

            .character-transformations-v14-action.delete {
                border-color:
                    rgba(127,38,29,.24);

                background:
                    #fff8f5;

                color:
                    #8b2a20;
            }

            .character-transformations-v14-action.delete:hover {
                border-color: rgba(127,38,29,.38);
                background: #fff0eb;
                color: #6b1d14;
            }

            .character-transformations-v14-action.transform {
                border-color: rgba(83,21,15,.28);
                background: #6b1d14;
                color: #fffaf2;
            }

            .character-transformations-v14-action.transform:hover {
                background: #53150f;
            }

            .character-transformations-v14-action.normal {
                border-color: rgba(83,21,15,.24);
                background: #eadbc8;
                color: #53150f;
            }

            .character-transformations-v14-action.normal:hover {
                background: #e2cdb3;
            }

            .character-transformations-v14-action:disabled {
                cursor: not-allowed;
                opacity: .45;
            }

            .character-transformations-v14-delete-modal {
                width: min(92vw, 420px);
                overflow: hidden;
                border: 1px solid rgba(127,38,29,.30);
                border-radius: 16px;
                background: #fffdf8;
                box-shadow: 0 26px 80px rgba(42,23,18,.34);
            }

            .character-transformations-v14-delete-icon {
                display: flex;
                width: 38px;
                height: 38px;
                flex: 0 0 auto;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(127,38,29,.22);
                border-radius: 10px;
                background: #fff0eb;
                color: #7f261d;
            }

            .character-transformations-v14-error {
                margin-top: 12px;
                border: 1px solid rgba(153,27,27,.18);
                border-radius: 9px;
                background: #fff1f1;
                padding: 9px 11px;
                color: #8b1e16;
                font-size: 9px;
                font-weight: 800;
                line-height: 1.45;
            }

            @media (max-width: 640px) {
                .character-transformations-v14-option {
                    grid-template-columns:
                        66px
                        minmax(0,1fr);
                }

                .character-transformations-v14-option-image {
                    width: 66px;
                    height: 76px;
                }

                .character-transformations-v14-option > :last-child {
                    grid-column: 1 / -1;
                }

                .character-transformations-v14-option-actions {
                    max-width: none;
                    justify-content: flex-start;
                }

                .character-transformations-v14-type-grid,
                .character-transformations-v14-grid-2,
                .character-transformations-v14-npc-list {
                    grid-template-columns: 1fr;
                }

                .character-transformations-v14-segmented.cols-4 {
                    grid-template-columns: repeat(2, minmax(0,1fr));
                }

                .character-transformations-v14-segmented.cols-4 button:nth-child(2) {
                    border-right: 0;
                }

                .character-transformations-v14-segmented.cols-4 button:nth-child(-n+2) {
                    border-bottom: 1px solid rgba(154,111,69,.20);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | V11 — FUNDO ESCURO REAL DOS MODAIS
            |--------------------------------------------------------------------------
            |
            | A opacidade fica em CSS próprio para não depender de uma variante
            | Tailwind como bg-black/72 que pode não existir na build.
            |--------------------------------------------------------------------------
            */
            .character-transformations-v14-backdrop {
                background:
                    rgba(0,0,0,.82);

                -webkit-backdrop-filter:
                    blur(8px);

                backdrop-filter:
                    blur(8px);
            }

            /*
            |--------------------------------------------------------------------------
            | V11 — LEGIBILIDADE
            |--------------------------------------------------------------------------
            */
            .character-transformations-v14-modal {
                color:
                    #34231b;
            }

            .character-transformations-v14-tag {
                padding:
                    4px 7px;

                font-size:
                    8px;
            }

            .character-transformations-v14-create-button {
                min-height:
                    36px;

                padding:
                    0 13px;

                font-size:
                    9px;
            }

            .character-transformations-v14-section-title {
                font-size:
                    16px;
            }

            .character-transformations-v14-section-copy {
                font-size:
                    10px;
            }

            .character-transformations-v14-label {
                font-size:
                    9px;
            }

            .character-transformations-v14-input,
            .character-transformations-v14-select {
                min-height:
                    40px;

                font-size:
                    12px;
            }

            .character-transformations-v14-segmented button {
                min-height:
                    38px;

                font-size:
                    9px;
            }

            .character-transformations-v14-format-badge {
                font-size:
                    8px;
            }

            .character-transformations-v14-format-warning {
                font-size:
                    10px;
            }

            .character-transformations-v14-format-warning strong {
                font-size:
                    12px;
            }

            .character-transformations-v14-action {
                min-height:
                    33px;

                padding:
                    0 10px;

                font-size:
                    8px;
            }

            .character-transformations-v14-error {
                font-size:
                    10px;
            }

            @media (max-width: 640px) {
                .character-transformations-v14-backdrop {
                    background:
                        rgba(0,0,0,.86);

                    -webkit-backdrop-filter:
                        blur(7px);

                    backdrop-filter:
                        blur(7px);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | V12 — TRANSFORMAÇÃO ESPECIAL
            |--------------------------------------------------------------------------
            */
            .character-transformations-v14-special-modal {
                width:
                    min(
                        92vw,
                        540px
                    );

                overflow: hidden;

                border:
                    1px solid
                    rgba(154,111,69,.52);

                border-radius:
                    18px;

                background:
                    #fbf8f1;

                color:
                    #34231b;

                box-shadow:
                    0 30px 90px
                    rgba(0,0,0,.46);
            }

            .character-transformations-v14-special-header {
                display: flex;

                align-items: flex-start;
                justify-content: space-between;

                gap: 14px;

                border-bottom:
                    1px solid
                    rgba(154,111,69,.28);

                background:
                    linear-gradient(
                        180deg,
                        #eadbc8,
                        #e6d2ba
                    );

                padding:
                    15px 16px 13px;
            }

            .character-transformations-v14-special-eyebrow {
                display: block;

                color:
                    #93623d;

                font-size:
                    9px;
                font-weight: 900;
                letter-spacing: .13em;
                text-transform: uppercase;
            }

            .character-transformations-v14-special-title {
                margin-top: 4px;

                color:
                    #53150f;

                font-family:
                    Georgia,
                    'Times New Roman',
                    serif;

                font-size:
                    22px;
                font-weight: 900;
                line-height: 1.05;
            }

            .character-transformations-v14-special-close {
                display: flex;

                width: 32px;
                height: 32px;

                flex: 0 0 auto;

                align-items: center;
                justify-content: center;

                border: 0;

                border-radius:
                    8px;

                background:
                    transparent;

                color:
                    #704326;

                font-size:
                    20px;

                transition:
                    background .14s ease,
                    color .14s ease;
            }

            .character-transformations-v14-special-close:hover {
                background:
                    rgba(83,21,15,.08);

                color:
                    #53150f;
            }

            .character-transformations-v14-special-body {
                padding:
                    15px 16px 16px;
            }

            .character-transformations-v14-special-copy {
                margin: 0;

                color:
                    #674936;

                font-size:
                    11px;
                line-height: 1.5;
            }

            .character-transformations-v14-special-life {
                display: inline-flex;

                min-height: 24px;

                align-items: center;

                margin-top: 9px;

                border:
                    1px solid
                    rgba(147,98,61,.25);

                border-radius:
                    999px;

                background:
                    #f3e7d8;

                padding:
                    0 9px;

                color:
                    #704326;

                font-size:
                    9px;
                font-weight: 900;
            }

            .character-transformations-v14-special-list {
                display: grid;

                gap: 8px;

                margin-top: 13px;
            }

            .character-transformations-v14-special-option {
                display: grid;

                grid-template-columns:
                    68px
                    minmax(0,1fr)
                    auto;

                gap: 11px;
                align-items: center;

                border:
                    1px solid
                    rgba(154,111,69,.26);

                border-radius:
                    12px;

                background:
                    #fffdf8;

                padding:
                    9px;

                transition:
                    border-color .14s ease,
                    background .14s ease;
            }

            .character-transformations-v14-special-option:hover {
                border-color:
                    rgba(83,21,15,.34);

                background:
                    #f8f0e6;
            }

            .character-transformations-v14-special-image {
                position: relative;

                display: flex;

                width: 68px;
                height: 76px;

                align-items: center;
                justify-content: center;

                overflow: hidden;

                border:
                    1px solid
                    rgba(154,111,69,.34);

                border-radius:
                    9px;

                background:
                    #eadbc8;

                color:
                    #6b1d14;
            }

            .character-transformations-v14-special-image img {
                position: absolute;
                inset: 0;

                width: 100%;
                height: 100%;

                object-fit: cover;
                object-position: top center;
            }

            .character-transformations-v14-special-name {
                color:
                    #53150f;

                font-family:
                    Georgia,
                    'Times New Roman',
                    serif;

                font-size:
                    16px;
                font-weight: 900;
                line-height: 1.08;
            }

            .character-transformations-v14-special-trigger {
                display: inline-flex;

                min-height: 20px;

                align-items: center;

                margin-top: 5px;

                border:
                    1px solid
                    rgba(147,98,61,.24);

                border-radius:
                    999px;

                background:
                    #f3e7d8;

                padding:
                    0 7px;

                color:
                    #704326;

                font-size:
                    8px;
                font-weight: 900;
            }

            .character-transformations-v14-special-action {
                display: inline-flex;

                min-height: 34px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid
                    rgba(83,21,15,.28);

                border-radius:
                    8px;

                background:
                    #6b1d14;

                padding:
                    0 11px;

                color:
                    #fffaf2;

                font-size:
                    8px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;

                white-space: nowrap;

                transition:
                    background .14s ease,
                    transform .14s ease;
            }

            .character-transformations-v14-special-action:hover {
                background:
                    #53150f;

                transform:
                    translateY(-1px);
            }

            .character-transformations-v14-special-action:disabled {
                cursor: wait;
                opacity: .48;
                transform: none;
            }

            .character-transformations-v14-special-footer {
                display: flex;

                align-items: center;
                justify-content: flex-end;

                gap: 8px;

                border-top:
                    1px solid
                    rgba(154,111,69,.22);

                background:
                    #fffdf8;

                padding:
                    11px 16px;
            }

            .character-transformations-v14-special-decline {
                min-height: 32px;

                border: 0;

                border-radius:
                    8px;

                background:
                    transparent;

                padding:
                    0 10px;

                color:
                    #755847;

                font-size:
                    8px;
                font-weight: 900;
                letter-spacing: .04em;
                text-transform: uppercase;

                transition:
                    background .14s ease,
                    color .14s ease;
            }

            .character-transformations-v14-special-decline:hover {
                background:
                    #f3e7d8;

                color:
                    #53150f;
            }

            @media (max-width: 640px) {
                .character-transformations-v14-special-option {
                    grid-template-columns:
                        58px
                        minmax(0,1fr);
                }

                .character-transformations-v14-special-image {
                    width: 58px;
                    height: 66px;
                }

                .character-transformations-v14-special-action {
                    grid-column:
                        1 / -1;

                    width: 100%;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | V15 — INTEGRAÇÃO TOTAL COM O THEME CORE
            |--------------------------------------------------------------------------
            |
            | Transformações nasceu antes do sistema --theme-* e por isso ainda
            | possuía vários HEX fixos. Este bloco fica por último de propósito:
            | transforma card, seletor, editor, prompts e controles em superfícies
            | semânticas do tema atual, preservando vermelho somente para ações
            | destrutivas / erros.
            |
            */

            .character-sheet-theme[data-theme]
            .character-transformations-v14-card {
                border-color: var(--theme-border) !important;
                background: var(--theme-card-background) !important;
                color: var(--theme-text-primary) !important;
                box-shadow: var(--theme-module-shadow) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-trigger:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-trigger:focus-visible {
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-portrait,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-option-image,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-photo-preview,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-npc-image,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-image {
                border-color: var(--theme-border) !important;
                background: var(--theme-inset-background) !important;
                color: var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-kicker,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-label,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-eyebrow {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-name,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-section-title,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-title,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-name {
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-meta,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-section-copy,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-copy {
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-chevron {
                color: var(--theme-label) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-trigger:hover
            .character-transformations-v14-chevron {
                background: var(--theme-accent-softer) !important;
                color: var(--theme-accent-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-active-line {
                background:
                    linear-gradient(
                        90deg,
                        var(--theme-accent-strong),
                        var(--theme-accent),
                        color-mix(
                            in srgb,
                            var(--theme-accent) 42%,
                            var(--theme-paper-strong) 58%
                        )
                    ) !important;
            }

            /* ---------------- MODAIS / PORTAIS ---------------- */

            .character-sheet-theme[data-theme]
            .character-transformations-v14-backdrop {
                background: var(--theme-modal-backdrop, rgba(0,0,0,.82)) !important;
                -webkit-backdrop-filter: blur(8px) !important;
                backdrop-filter: blur(8px) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-modal,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-create-modal,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-modal {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 28px 80px rgba(0,0,0,.32)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-create-header {
                border-color: var(--theme-border) !important;
                background: var(--theme-modal-header, var(--theme-band-background)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-create-footer {
                border-color: var(--theme-border) !important;
                background: var(--theme-modal-footer, var(--theme-paper-strong)) !important;
                box-shadow:
                    0 -10px 24px
                    color-mix(
                        in srgb,
                        var(--theme-shadow-color, #000) 8%,
                        transparent
                    ) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-concentration {
                border-color: var(--theme-modal-border, var(--theme-border)) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-modal-text, var(--theme-text-primary)) !important;
                box-shadow: var(--theme-modal-shadow, 0 24px 70px rgba(0,0,0,.30)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-header,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-footer {
                border-color: var(--theme-border) !important;
                background: var(--theme-band-background) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-create-body,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-body,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-list {
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-text-primary) !important;
            }

            /* ---------------- LISTAS / CARDS INTERNOS ---------------- */

            .character-sheet-theme[data-theme]
            .character-transformations-v14-option,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-type,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-toggle-row,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-npc,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-option {
                border-color: var(--theme-border-soft) !important;
                background: var(--theme-card-background) !important;
                color: var(--theme-text-primary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-option:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-type:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-npc:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-option:hover {
                border-color: var(--theme-border) !important;
                background: var(--theme-card-hover) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-type.is-active,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-npc.is-active {
                border-color: var(--theme-accent) !important;
                background: var(--theme-accent-softer) !important;
                box-shadow:
                    inset 3px 0 0 var(--theme-accent) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-tag,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-format-badge,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-trigger,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-life {
                border-color: var(--theme-border) !important;
                background: var(--theme-accent-softer) !important;
                color: var(--theme-label) !important;
            }

            /* ---------------- FORMULÁRIO ---------------- */

            .character-sheet-theme[data-theme]
            .character-transformations-v14-input,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-select {
                border-color: var(--theme-input-border) !important;
                background: var(--theme-input-bg) !important;
                color: var(--theme-input-text) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-input:focus,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-select:focus {
                border-color: var(--theme-input-focus) !important;
                box-shadow: var(--theme-input-focus-ring) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-segmented {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-inset) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-segmented button {
                border-color: var(--theme-border-soft) !important;
                background: transparent !important;
                color: var(--theme-text-secondary) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-segmented button:hover {
                background: var(--theme-card-hover) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-segmented button.is-active {
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-switch {
                background: var(--theme-border-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-switch.is-active {
                background: var(--theme-accent) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-switch::after {
                background: var(--theme-paper-strong) !important;
                box-shadow: 0 1px 4px rgba(0,0,0,.18) !important;
            }

            /* ---------------- AÇÕES ---------------- */

            .character-sheet-theme[data-theme]
            .character-transformations-v14-create-button,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.transform,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-action {
                border-color: var(--theme-button-border) !important;
                background: var(--theme-button-background) !important;
                color: var(--theme-button-text) !important;
                box-shadow: var(--theme-button-shadow) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-create-button:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.transform:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-action:hover {
                background: var(--theme-button-hover-background) !important;
                box-shadow: var(--theme-button-hover-shadow) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.edit,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.normal,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-decline,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-close {
                border-color: var(--theme-border) !important;
                background: var(--theme-surface-raised) !important;
                color: var(--theme-text-strong) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.edit:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.normal:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-decline:hover,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-special-close:hover {
                background: var(--theme-card-hover) !important;
            }

            /* ---------------- DESTRUTIVO / ERRO ---------------- */

            .character-sheet-theme[data-theme]
            .character-transformations-v14-delete-modal {
                border-color: color-mix(in srgb, var(--theme-state-damage) 32%, transparent) !important;
                background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
                color: var(--theme-text-primary) !important;
                box-shadow: var(--theme-modal-shadow, 0 26px 80px rgba(0,0,0,.34)) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-delete-icon,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-error,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-format-warning,
            .character-sheet-theme[data-theme]
            .character-transformations-v14-format-badge.unsupported {
                border-color: color-mix(in srgb, var(--theme-state-damage) 25%, transparent) !important;
                background: color-mix(in srgb, var(--theme-state-damage) 8%, var(--theme-paper-strong)) !important;
                color: var(--theme-state-damage) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-format-warning strong {
                color: var(--theme-state-damage) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.delete {
                border-color: color-mix(in srgb, var(--theme-state-damage) 28%, transparent) !important;
                background: color-mix(in srgb, var(--theme-state-damage) 7%, var(--theme-paper-strong)) !important;
                color: var(--theme-state-damage) !important;
            }

            .character-sheet-theme[data-theme]
            .character-transformations-v14-action.delete:hover {
                background: color-mix(in srgb, var(--theme-state-damage) 12%, var(--theme-paper-strong)) !important;
            }


            /* V54: help compact and universal dice UI. */
            .character-sheet-theme[data-theme] .ct-v54-type-row {padding:0;}
            .character-sheet-theme[data-theme] .ct-v54-type-option {position:relative;min-width:0;}
            .character-sheet-theme[data-theme] .ct-v54-type-option > button:first-child {width:100%;padding-right:36px;}
            .character-sheet-theme[data-theme] .ct-v51-switch .ct-v54-help {position:absolute; right:10px;top:50%;transform:translateY(-50%);display:grid;place-items:center;
                width:20px;height:20px;min-height:20px;padding:0;border-radius:50%;border:1px solid var(--theme-border-strong);background:var(--theme-surface-raised);
                color:var(--theme-text-secondary);font-size:11px;font-weight:800;z-index:5;}
            .character-sheet-theme[data-theme] .ct-v54-help:hover,
            .character-sheet-theme[data-theme] .ct-v54-help:focus-visible {color:var(--theme-text-strong);border-color:var(--theme-accent);background:var(--theme-accent-softer);}
            .character-sheet-theme[data-theme] .ct-v54-help-copy {margin:9px 0 0;padding:11px 13px;border:1px solid var(--theme-border);
                border-radius:10px;background:var(--theme-surface-card-alt);color:var(--theme-text-secondary);font-size:11px;line-height:1.55;}
            .character-sheet-theme[data-theme] .ct-v54-help-copy strong {display:block;color:var(--theme-text-strong);margin-bottom:3px;}
            .character-sheet-theme[data-theme] .ct-v54-duration {display:flex;align-items:end;gap:10px;flex-wrap:wrap;margin-top:12px;}
            .character-sheet-theme[data-theme] .ct-v54-duration-hint {flex-basis:100%;font-size:10px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme].ct-v54-overlay {z-index:360!important;}
            .character-sheet-theme[data-theme] .ct-v54-roll-modal {z-index:2;position:relative;display:flex;flex-direction:column;width:min(94vw,500px);
                max-height:min(90dvh,690px);overflow:hidden;border:1px solid var(--theme-border);border-radius:16px;
                background:var(--theme-modal-surface);color:var(--theme-text-primary);box-shadow:var(--theme-modal-shadow);}
            .character-sheet-theme[data-theme] .ct-v54-roll-head {display:flex;justify-content:space-between;align-items:start;gap:12px;padding:18px 20px;
                border-bottom:1px solid var(--theme-border);background:var(--theme-modal-header-background);}
            .character-sheet-theme[data-theme] .ct-v54-roll-head small {text-transform:uppercase;color:var(--theme-label);font-size:9px;letter-spacing:.14em;font-weight:800;}
            .character-sheet-theme[data-theme] .ct-v54-roll-head h2 {font-family:Georgia,serif;color:var(--theme-text-strong);font-size:23px;font-weight:900;}
            .character-sheet-theme[data-theme] .ct-v54-roll-head p {font-size:11px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v54-roll-head button {min-width:30px;height:30px;border:1px solid var(--theme-border);border-radius:9px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v54-roll-body {padding:18px 20px;min-height:0;overflow:auto;}
            .character-sheet-theme[data-theme] .ct-v54-expression {text-align:center;font-size:12px;color:var(--theme-text-secondary);font-weight:750;}
            .character-sheet-theme[data-theme] .ct-v54-dice-row {margin:15px 0;display:flex;gap:8px;align-items:center;justify-content:center;flex-wrap:wrap;}
            .character-sheet-theme[data-theme] .ct-v54-die-cell {display:flex;}
            .character-sheet-theme[data-theme] .ct-v54-modifier {font-family:Georgia,serif;font-size:22px;font-weight:900;color:var(--theme-accent-strong);}
            .character-sheet-theme[data-theme] .ct-v54-result {display:flex;justify-content:center;align-items:baseline;gap:7px;}
            .character-sheet-theme[data-theme] .ct-v54-result small {font-size:10px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v54-result strong {font-family:Georgia,serif;font-size:32px;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v54-result span {font-size:11px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v54-manual {display:grid;gap:7px;font-size:11px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v54-roll-error {font-size:11px;color:var(--theme-state-damage,#a33);margin-top:12px;}
            .character-sheet-theme[data-theme] .ct-v54-description {font-size:10px;color:var(--theme-text-secondary);margin-top:16px;text-align:center;line-height:1.5;}
            .character-sheet-theme[data-theme] .ct-v54-roll-actions {display:flex;flex-wrap:wrap;justify-content:end;gap:8px;padding:13px 18px;border-top:1px solid var(--theme-border);}
            .character-sheet-theme[data-theme] .ct-v54-roll-actions button:disabled {opacity:.5;cursor:wait;}
            @media(max-width:520px) {.character-sheet-theme[data-theme] .ct-v54-roll-actions button {flex:1 1 42%;}}

            /* V55 — acabamento inspirado nas referências, com temas sem HEX fixo. */
            .character-sheet-theme[data-theme] .ct-v54-type-option {isolation:isolate;}
            .character-sheet-theme[data-theme] .ct-v54-type-option:has(.ct-v55-help-wrap:hover),
            .character-sheet-theme[data-theme] .ct-v54-type-option:has(.ct-v55-help-wrap:focus-within){z-index:20;}
            .character-sheet-theme[data-theme] .ct-v55-help-wrap {position:absolute;right:12px;top:50%;transform:translateY(-50%);z-index:25;}
            .character-sheet-theme[data-theme] .ct-v51-switch .ct-v55-help {
                display:grid;place-items:center;min-height:0;width:17px;height:17px;padding:0;
                border:1px solid var(--theme-border-strong);border-radius:50%;
                background:var(--theme-surface-card);font:700 10px Georgia,serif;color:var(--theme-text-secondary);
                opacity:.7;transition:opacity .16s,border-color .16s,color .16s;
            }
            .character-sheet-theme[data-theme] .ct-v55-help:hover,.character-sheet-theme[data-theme] .ct-v55-help:focus-visible {
                opacity:1;border-color:var(--theme-accent);color:var(--theme-accent-strong);outline-offset:2px;
            }
            .character-sheet-theme[data-theme] .ct-v55-help-popup {
                position:absolute;top:calc(100% + 13px);right:-9px;width:min(285px,75vw);
                padding:12px 13px;border:1px solid var(--theme-border-strong);border-radius:11px;
                background:var(--theme-modal-surface,var(--theme-surface-raised));box-shadow:var(--theme-modal-shadow,0 12px 30px #0002);
                color:var(--theme-text-secondary);font-size:11px;line-height:1.5;letter-spacing:0;text-transform:none;
                opacity:0;visibility:hidden;pointer-events:none;transform:translateY(-5px);
                transition:opacity .15s,transform .15s,visibility .15s;
            }
            .character-sheet-theme[data-theme] .ct-v55-help-wrap:hover .ct-v55-help-popup,
            .character-sheet-theme[data-theme] .ct-v55-help-wrap:focus-within .ct-v55-help-popup {
                opacity:1;visibility:visible;transform:translateY(0);
            }
            .character-sheet-theme[data-theme] .ct-v55-help-popup strong {display:block;color:var(--theme-text-strong);font:900 14px Georgia,serif;margin-bottom:4px;}
            .character-sheet-theme[data-theme] .ct-v55-linked-art {overflow:hidden;border:1px solid var(--theme-border);width:43px;height:43px;flex:0 0 43px;font:900 19px Georgia,serif;}
            .character-sheet-theme[data-theme] .ct-v55-linked-art img,
            .character-sheet-theme[data-theme] .ct-v55-picker-art img {display:block;width:100%;height:100%;object-fit:cover;}
            .character-sheet-theme[data-theme] .ct-v55-picker-art {overflow:hidden;width:44px;height:44px;flex:0 0 44px;}
            .character-sheet-theme[data-theme] .ct-v54-roll-modal {width:min(92vw,530px);border-radius:18px;}
            .character-sheet-theme[data-theme] .ct-v54-roll-head {padding:17px 22px 16px;}
            .character-sheet-theme[data-theme] .ct-v54-roll-head h2 {font-size:24px;line-height:1.2;margin:3px 0;}
            .character-sheet-theme[data-theme] .ct-v54-roll-body {padding:20px 18px 17px;text-align:center;}
            .character-sheet-theme[data-theme] .ct-v54-expression {font:900 22px Georgia,'Times New Roman',serif;color:var(--theme-text-strong);letter-spacing:.01em;}
            .character-sheet-theme[data-theme] .ct-v54-dice-row {margin:13px 0 18px;gap:12px;min-height:83px;}
            .character-sheet-theme[data-theme] .ct-v54-die-cell {padding:1px;filter:drop-shadow(0 2px 3px rgba(0,0,0,.08));}
            .character-sheet-theme[data-theme] .ct-v54-result {margin-top:4px;align-items:baseline;gap:9px;}
            .character-sheet-theme[data-theme] .ct-v54-result small {text-transform:uppercase;letter-spacing:.11em;font-size:9px;font-weight:800;}
            .character-sheet-theme[data-theme] .ct-v54-result strong {font-size:47px;line-height:1.15;}
            .character-sheet-theme[data-theme] .ct-v54-result span {font-size:13px;}
            .character-sheet-theme[data-theme] .ct-v54-roll-actions {justify-content:center;gap:8px;padding:13px 18px;}
            .character-sheet-theme[data-theme] .ct-v54-roll-actions .character-transformations-v14-create-button {min-height:38px;}
            .character-sheet-theme[data-theme] .ct-v54-manual {max-width:240px;text-align:left;margin:16px auto;}
            @media(max-width:520px) {
                .character-sheet-theme[data-theme] .ct-v54-dice-row {gap:6px;}
                .character-sheet-theme[data-theme] .ct-v55-help-popup {right:-5px;max-width:78vw;}
            }
        </style>
    @endonce



    {{-- V15: Cards compactos e contador de iniciativa. --}}
    <style>
      .character-transformations-v14-list { padding: 10px; display: flex; flex-direction: column; gap: 10px; }
      .character-transformations-v14-option {
        grid-template-columns: 76px minmax(0,1fr) auto;
        align-items: center; gap: 12px; padding: 11px;
        background: var(--theme-paper-strong,#fffdf8); border: 1px solid var(--theme-border,#d7c1a8);
        border-radius: 14px; box-shadow: var(--theme-module-shadow,0 2px 6px #452a1808);
      }
      .character-transformations-v14-option:hover { background: var(--theme-hover,#f7efe4); }
      .character-transformations-v14-option-image { width: 76px; height: 84px; flex-basis: 76px; }
      .character-transformations-v14-option:last-child { border-bottom: 1px solid var(--theme-border,#d7c1a8); }
      .character-transformations-v14-option-actions.ct-form-actions { max-width:none; flex-wrap:nowrap; gap:7px; }
      .ct-form-primary { min-height:36px; padding:0 15px; font-size:9px; }
      .ct-form-current { color:var(--theme-state-heal,#276749);font-size:9px;font-weight:900;text-transform:uppercase; }
      .ct-form-more { position:relative; display:flex; flex-direction:column; align-items:flex-end; }
      .ct-form-more-toggle { width:34px;height:36px;border:1px solid var(--theme-border,#cdb89b);
        border-radius:9px;background:var(--theme-paper-strong,#fffdf8);color:var(--theme-label,#76543c);
        font-weight:900;font-size:20px;line-height:1; }
      .ct-form-more-options { position:static;margin-top:5px;width:185px;max-width:60vw;z-index:150;
        background:var(--theme-paper-strong,#fffdf8);border:1px solid var(--theme-border,#cdb89b);
        border-radius:10px;box-shadow:0 12px 35px #29140e30;padding:5px;display:flex;flex-direction:column; }
      .ct-form-more-options button { width:100%;padding:9px 10px;text-align:left;border-radius:7px;
        color:var(--theme-text,#34231b);font-size:10px;font-weight:700; }
      .ct-form-more-options button:hover { background:var(--theme-hover,#f3e7d8); }
      .ct-form-more-options button:disabled {opacity:.45;cursor:not-allowed}
      .ct-form-more-options .ct-form-danger { color:var(--theme-state-damage,#991b1b); }
      .ct-form-active-strip { display:flex;align-items:center;justify-content:space-between;gap:12px;
        padding:10px 12px;background:var(--theme-panel-soft,#f8f0e6);
        border:1px solid var(--theme-border-soft,#ddc8b0);border-radius:11px; }
      .ct-form-active-strip strong { display:block;font:700 15px Georgia,serif;color:var(--theme-title,#53150f); }
      .ct-form-active-strip small { display:block;margin-top:3px;color:var(--theme-text-secondary,#76543c);font-size:10px; }
      .ct-form-active-actions { display:flex;gap:7px;flex-wrap:wrap;justify-content:flex-end; }
      .ct-form-active-actions button {border:1px solid var(--theme-border,#c5ab8f);border-radius:8px;padding:8px 10px;
        font-size:9px;font-weight:850;background:var(--theme-paper-strong,#fffdf8);color:var(--theme-title,#53150f); }
      .ct-form-active-actions .ct-form-return-btn {background:var(--theme-button-background,#6b1d14);
        color:var(--theme-button-text,#fffaf2); }
      .ct-form-floating-timer {position:fixed;right:20px;bottom:20px;z-index:2147483100;
        border:1px solid var(--theme-border,#b69879);border-radius:999px;background:var(--theme-button-background,#6b1d14);
        color:var(--theme-button-text,#fffaf2);padding:10px 15px;box-shadow:0 10px 26px #200e0a4a;font-size:11px;font-weight:850; }
      .ct-form-floating-timer span {font-size:17px;vertical-align:-1px;}
      .ct-form-round-backdrop {position:fixed;inset:0;z-index:2147483200;display:flex;align-items:center;
        justify-content:center;padding:16px;background:#1f100cb8;backdrop-filter:blur(5px);}
      .ct-form-round-modal {width:min(480px,100%);border-radius:18px;overflow:hidden;border:1px solid var(--theme-border,#cdb394);
        background:var(--theme-paper-strong,#fffdf8);color:var(--theme-text,#34231b);box-shadow:0 24px 70px #140b0955;}
      .ct-form-round-header {display:flex;justify-content:space-between;gap:12px;align-items:center;padding:18px 20px;
        background:var(--theme-panel,#eadbc8);border-bottom:1px solid var(--theme-border,#cdb394);}
      .ct-form-round-header small {font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--theme-label,#76543c);}
      .ct-form-round-header h2 {font:800 23px Georgia,serif;color:var(--theme-title,#53150f);}
      .ct-form-round-header button {width:32px;height:32px;border-radius:8px;background:var(--theme-paper-strong,#fffdf8);font-size:22px;}
      .ct-form-round-body {padding:18px 20px;}
      .ct-form-round-combat {font-size:11px;font-weight:700;color:var(--theme-text-secondary,#76543c);margin-bottom:12px;}
      .ct-form-round-grid {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:9px;}
      .ct-form-round-grid>div {background:var(--theme-panel-soft,#f8f0e6);border:1px solid var(--theme-border-soft,#ddc8b0);
        border-radius:11px;text-align:center;padding:12px 8px;}
      .ct-form-round-grid small {display:block;font-size:9px;font-weight:850;letter-spacing:.04em;text-transform:uppercase;color:var(--theme-label,#76543c);}
      .ct-form-round-grid strong {display:block;margin-top:5px;font:800 28px Georgia,serif;color:var(--theme-title,#53150f);}
      .ct-form-round-detail {margin-top:10px;font-size:11px;color:var(--theme-text-secondary,#76543c);}
      .ct-form-round-concentration {display:flex;align-items:center;gap:8px;margin-top:13px;padding:10px 12px;
        border:1px solid var(--theme-border-soft);border-radius:10px;
        background:var(--theme-paper-alt, var(--theme-panel-soft));
        color:var(--theme-text-secondary);font-size:11px;line-height:1.4;}
      .ct-form-round-concentration.is-active {border-color:color-mix(in srgb, var(--theme-state-concentration,#75558a) 38%, var(--theme-border));
        color:var(--theme-text-primary);} 
      .ct-form-round-concentration-dot {width:8px;height:8px;flex:0 0 8px;border-radius:50%;background:var(--theme-text-secondary);}
      .ct-form-round-concentration.is-active .ct-form-round-concentration-dot {background:var(--theme-state-concentration,#75558a);}
      .ct-form-round-error {margin-top:10px;color:var(--theme-state-damage,#991b1b);font-size:10px;}
      .ct-form-round-footer {display:flex;justify-content:flex-end;gap:9px;padding:13px 20px;
        border-top:1px solid var(--theme-border-soft,#ddc8b0);}
      .ct-form-round-footer button {border:1px solid var(--theme-border,#cdb394);border-radius:9px;padding:8px 13px;font-size:10px;font-weight:850;}
      .ct-form-round-footer button:last-child {background:var(--theme-button-background,#6b1d14);color:var(--theme-button-text,#fffaf2);}
      @media(max-width:690px) {
        .character-transformations-v14-option {grid-template-columns:64px minmax(0,1fr);gap:10px;}
        .character-transformations-v14-option-image {width:64px;height:74px;flex-basis:64px;}
        .character-transformations-v14-option-actions.ct-form-actions {grid-column:2;justify-content:flex-start;}
        .ct-form-floating-timer {bottom:14px;right:14px;}
      }

      /* ================================================================
         V16 — TEMA INTEGRAL DAS TRANSFORMAÇÕES
         Todos os portais Alpine carregam data-theme, portanto as cores são
         resolvidas pelo mesmo Theme Core da ficha (incluindo midnight).
         O bloco está DEPOIS das regras V15 para não ser sobrescrito.
         ================================================================ */
      .character-sheet-theme[data-theme] .character-transformations-v14-modal,
      .character-sheet-theme[data-theme] .character-transformations-v14-create-modal,
      .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal,
      .character-sheet-theme[data-theme] .character-transformations-v14-special-modal,
      .character-sheet-theme[data-theme].character-transformations-v14-concentration {
        background: var(--theme-modal-surface, var(--theme-paper-strong, #fffdf8)) !important;
        color: var(--theme-modal-text, var(--theme-text-primary, #34231b)) !important;
        border-color: var(--theme-modal-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-modal > header,
      .character-sheet-theme[data-theme] .character-transformations-v14-create-header,
      .character-sheet-theme[data-theme] .character-transformations-v14-special-header,
      .character-sheet-theme[data-theme].character-transformations-v14-concentration > header {
        background: var(--theme-modal-header-background, var(--theme-band-background, var(--theme-panel))) !important;
        border-color: var(--theme-modal-border, var(--theme-border)) !important;
        color: var(--theme-modal-text, var(--theme-text-primary)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-modal > footer,
      .character-sheet-theme[data-theme] .character-transformations-v14-create-footer,
      .character-sheet-theme[data-theme] .character-transformations-v14-special-footer {
        background: var(--theme-modal-surface-soft, var(--theme-paper-alt, var(--theme-paper-strong))) !important;
        color: var(--theme-modal-muted, var(--theme-text-secondary)) !important;
        border-color: var(--theme-modal-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-list,
      .character-sheet-theme[data-theme] .character-transformations-v14-create-body,
      .character-sheet-theme[data-theme] .character-transformations-v14-special-body {
        background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
        color: var(--theme-modal-text, var(--theme-text-primary)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-option,
      .character-sheet-theme[data-theme] .character-transformations-v14-type,
      .character-sheet-theme[data-theme] .character-transformations-v14-npc {
        background: var(--theme-card-background, var(--theme-surface-card)) !important;
        color: var(--theme-text-primary) !important;
        border-color: var(--theme-border) !important;
        box-shadow: var(--theme-module-shadow, none) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-option:hover,
      .character-sheet-theme[data-theme] .character-transformations-v14-type:hover,
      .character-sheet-theme[data-theme] .character-transformations-v14-npc:hover {
        background: var(--theme-card-hover, var(--theme-hover)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-option-image,
      .character-sheet-theme[data-theme] .character-transformations-v14-option-image-fallback {
        background: var(--theme-surface-inset, var(--theme-panel-soft)) !important;
        color: var(--theme-accent-strong, var(--theme-text-strong)) !important;
        border-color: var(--theme-border) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-tag,
      .character-sheet-theme[data-theme] .character-transformations-v14-format-badge:not(.unsupported) {
        color: var(--theme-text-secondary, var(--theme-label)) !important;
        background: var(--theme-surface-inset, var(--theme-panel-soft)) !important;
        border-color: var(--theme-border, var(--theme-border-soft)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-option .font-serif,
      .character-sheet-theme[data-theme] .ct-form-active-strip strong,
      .character-sheet-theme[data-theme] .character-transformations-v14-modal > header h2,
      .character-sheet-theme[data-theme] .character-transformations-v14-create-header h2,
      .character-sheet-theme[data-theme] .ct-form-round-header h2 {
        color: var(--theme-text-strong, var(--theme-title)) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-active-strip,
      .character-sheet-theme[data-theme] .ct-form-round-grid > div {
        border-color: var(--theme-border-soft, var(--theme-border)) !important;
        background: var(--theme-surface-inset, var(--theme-panel-soft)) !important;
        color: var(--theme-text-primary) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-more-toggle,
      .character-sheet-theme[data-theme] .ct-form-more-options,
      .character-sheet-theme[data-theme] .ct-form-active-actions button:not(.ct-form-return-btn),
      .character-sheet-theme[data-theme] .ct-form-round-footer button:not(:last-child),
      .character-sheet-theme[data-theme] .ct-form-round-header button {
        background: var(--theme-surface-raised, var(--theme-paper-strong)) !important;
        color: var(--theme-text-strong) !important;
        border-color: var(--theme-border) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-more-options button {
        color: var(--theme-text-primary) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-more-options button:hover,
      .character-sheet-theme[data-theme] .ct-form-more-toggle:hover,
      .character-sheet-theme[data-theme] .ct-form-active-actions button:not(.ct-form-return-btn):hover {
        background: var(--theme-card-hover, var(--theme-hover)) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-more-options .ct-form-danger {
        color: var(--theme-state-damage, #c56b65) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-current {
        color: var(--theme-state-heal, var(--theme-accent-strong)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-create-button,
      .character-sheet-theme[data-theme] .character-transformations-v14-action.transform,
      .character-sheet-theme[data-theme] .ct-form-return-btn,
      .character-sheet-theme[data-theme] .ct-form-round-footer button:last-child,
      .character-sheet-theme[data-theme].ct-form-floating-timer {
        background: var(--theme-button-background, var(--theme-accent-strong)) !important;
        color: var(--theme-button-text, var(--theme-on-accent, #fff)) !important;
        border-color: var(--theme-button-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-create-button:hover,
      .character-sheet-theme[data-theme] .character-transformations-v14-action.transform:hover,
      .character-sheet-theme[data-theme] .ct-form-return-btn:hover,
      .character-sheet-theme[data-theme].ct-form-floating-timer:hover {
        background: var(--theme-button-hover-background, var(--theme-button-background)) !important;
      }
      .character-sheet-theme[data-theme].ct-form-round-backdrop {
        background: var(--theme-modal-backdrop, rgba(0,0,0,.78)) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-round-modal {
        background: var(--theme-modal-surface, var(--theme-paper-strong)) !important;
        color: var(--theme-modal-text, var(--theme-text-primary)) !important;
        border-color: var(--theme-modal-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-round-header {
        background: var(--theme-modal-header-background, var(--theme-band-background, var(--theme-panel))) !important;
        border-color: var(--theme-modal-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-round-grid strong {
        color: var(--theme-text-strong) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-round-grid small,
      .character-sheet-theme[data-theme] .ct-form-round-combat,
      .character-sheet-theme[data-theme] .ct-form-round-detail,
      .character-sheet-theme[data-theme] .ct-form-active-strip small,
      .character-sheet-theme[data-theme] .character-transformations-v14-modal > footer {
        color: var(--theme-text-secondary) !important;
      }
      .character-sheet-theme[data-theme] .ct-form-round-footer {
        background: var(--theme-modal-surface-soft, var(--theme-paper-alt)) !important;
        border-color: var(--theme-modal-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-input,
      .character-sheet-theme[data-theme] .character-transformations-v14-select {
        background: var(--theme-input-bg, var(--theme-paper-strong)) !important;
        color: var(--theme-input-text, var(--theme-text-primary)) !important;
        border-color: var(--theme-input-border, var(--theme-border)) !important;
      }
      .character-sheet-theme[data-theme] .character-transformations-v14-input::placeholder {
        color: var(--theme-input-placeholder, var(--theme-text-secondary)) !important;
      }

      /* Transforma utilitários Tailwind de cor fixa da UI antiga em tokens
         semânticos, SOMENTE dentro dos modais de Transformações. */
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#53150f]"] { color: var(--theme-text-strong, var(--theme-title)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#6b1d14]"] { color: var(--theme-accent-strong, var(--theme-text-strong)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#34231b]"] { color: var(--theme-text-primary) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#755847]"] { color: var(--theme-text-secondary) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#674936]"] { color: var(--theme-text-secondary) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#6f4c36]"] { color: var(--theme-text-secondary) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#8c6239]"] { color: var(--theme-label, var(--theme-text-secondary)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#93623d]"] { color: var(--theme-label, var(--theme-text-secondary)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#704326]"] { color: var(--theme-label, var(--theme-text-secondary)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#8b2a20]"] { color: var(--theme-state-damage, #c56b65) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#fffaf2]"] { color: var(--theme-button-text, var(--theme-on-accent)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="text-[#315d45]"] { color: var(--theme-state-heal, var(--theme-accent-strong)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#eadbc8]"] { background-color: var(--theme-modal-header-background, var(--theme-band-background, var(--theme-panel))) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#eadbc8]/45"] { background-color: color-mix(in srgb, var(--theme-panel, var(--theme-band-background)) 45%, transparent) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#f3e7d8]"] { background-color: var(--theme-surface-inset, var(--theme-panel-soft)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#f7f0e6]/55"] { background-color: color-mix(in srgb, var(--theme-panel-soft, var(--theme-paper-alt)) 55%, transparent) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#fffdf8]"] { background-color: var(--theme-paper-strong, var(--theme-modal-surface)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#fbf8f1]"] { background-color: var(--theme-modal-surface, var(--theme-paper-strong)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#6b1d14]"] { background-color: var(--theme-button-background, var(--theme-accent-strong)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="bg-[#7f261d]"] { background-color: var(--theme-state-damage, #aa453f) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#93623d]/20"] { border-color: var(--theme-border, var(--theme-modal-border)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#a0774d]/30"] { border-color: var(--theme-border, var(--theme-modal-border)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#a0774d]/24"] { border-color: var(--theme-border, var(--theme-modal-border)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#a0774d]/22"] { border-color: var(--theme-border, var(--theme-modal-border)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#b08c62]/40"] { border-color: var(--theme-border, var(--theme-modal-border)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#b98e63]/60"] { border-color: var(--theme-border, var(--theme-modal-border)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="border-[#7f261d]/25"] { border-color: var(--theme-state-damage) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="hover:bg-[#fffdf8]/55"]:hover,
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="hover:bg-[#eadbc8]/80"]:hover,
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="hover:bg-[#f3e7d8]"]:hover { background-color: var(--theme-card-hover, var(--theme-hover)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="hover:bg-[#6b1d14]"]:hover,
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="hover:bg-[#53150f]"]:hover { background-color: var(--theme-button-hover-background, var(--theme-button-background)) !important; }
      :is(.character-sheet-theme[data-theme] .character-transformations-v14-modal, .character-sheet-theme[data-theme] .character-transformations-v14-create-modal, .character-sheet-theme[data-theme] .character-transformations-v14-delete-modal, .character-sheet-theme[data-theme] .character-transformations-v14-special-modal, .character-sheet-theme[data-theme].character-transformations-v14-concentration) [class~="hover:text-[#53150f]"]:hover { color: var(--theme-text-strong) !important; }
      .character-sheet-theme[data-theme] .character-transformations-v14-option .character-transformations-v14-tag[style*="background"] {
        background: color-mix(in srgb, var(--theme-state-damage) 10%, var(--theme-card-background)) !important;
        color: var(--theme-state-damage) !important;
        border-color: color-mix(in srgb, var(--theme-state-damage) 36%, var(--theme-border)) !important;
      }

            /* V50 — criação sem poluição visual e seleção em modal próprio. */
            .character-sheet-theme[data-theme] .character-transformations-v14-create-modal {
                width: min(94vw, 740px);
                max-height: 90dvh;
                border-color: var(--theme-modal-border,var(--theme-border));
                background: var(--theme-modal-surface,var(--theme-paper-strong));
                color: var(--theme-text-primary);
            }
            .character-sheet-theme[data-theme] .character-transformations-v14-create-header {
                box-shadow: none !important; background-image: none !important;
            }
            .character-sheet-theme[data-theme] .character-transformations-v14-create-body { padding: 16px 19px 19px; }
            .character-sheet-theme[data-theme] .character-transformations-v14-type { min-height: 66px; padding: 10px 12px; }
            .character-sheet-theme[data-theme] .character-transformations-v14-type > span:last-child { display: none; }
            .character-sheet-theme[data-theme] .character-transformations-v14-type-grid { gap: 8px; }
            .character-sheet-theme[data-theme] .character-transformations-v14-section { margin-top: 13px; padding-top: 13px; }
            .character-sheet-theme[data-theme] .character-transformations-v14-section-title { font-size: 15px; }
            .character-sheet-theme[data-theme] .ct-v50-line { display:flex;align-items:center;justify-content:space-between;gap:14px; }
            .character-sheet-theme[data-theme] .ct-v50-light-button {
                flex:0 0 auto;min-height:34px;border:1px solid var(--theme-border);border-radius:9px;
                padding:0 12px;color:var(--theme-text-strong);background:var(--theme-surface-raised);font-size:10px;font-weight:800;
            }
            .character-sheet-theme[data-theme] .ct-v50-light-button:hover { background:var(--theme-card-hover); }
            .character-sheet-theme[data-theme] .ct-v50-linked-spell {margin-top:11px; display:grid;gap:7px;}
            .character-sheet-theme[data-theme] .ct-v50-simple-duration {display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-top:12px;}
            .character-sheet-theme[data-theme] .ct-v50-check { display:flex;align-items:center;gap:10px;min-height:40px;white-space:nowrap;color:var(--theme-text-strong);font-size:10px;font-weight:800; }
            .character-sheet-theme[data-theme] .ct-v50-duration-label { display:grid; gap:5px; min-width:140px;flex:1; }
            .character-sheet-theme[data-theme] .ct-v50-advanced > summary { cursor:pointer;list-style:none;font-size:11px;font-weight:850;color:var(--theme-text-strong);display:flex;justify-content:space-between;gap:10px;align-items:center; }
            .character-sheet-theme[data-theme] .ct-v50-advanced > summary::-webkit-details-marker {display:none;}
            .character-sheet-theme[data-theme] .ct-v50-advanced > summary::before { content:'+'; font-size:17px;line-height:1; color:var(--theme-accent); }
            .character-sheet-theme[data-theme] .ct-v50-advanced[open] > summary::before {content:'−';}
            .character-sheet-theme[data-theme] .ct-v50-advanced > summary span {margin-left:auto;font-size:9px;font-weight:500;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme].ct-v50-overlay {position:fixed !important;inset:0 !important;z-index:320 !important;display:flex;align-items:center;justify-content:center;padding:16px;isolation:isolate;}
            .character-sheet-theme[data-theme] .ct-v50-picker { position:relative; z-index:2; display:flex; flex-direction:column;width:min(94vw,640px);max-height:min(80dvh,700px);}
            .character-sheet-theme[data-theme] .ct-v50-picker-head {display:flex;align-items:center;justify-content:space-between;gap:10px;padding:15px 19px;border-bottom:1px solid var(--theme-border);}
            .character-sheet-theme[data-theme] .ct-v50-picker-head h2,
            .character-sheet-theme[data-theme] .ct-v50-confirm h2 {font-family:Georgia,serif;font-size:22px;font-weight:900;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v50-picker-head button {width:30px;height:30px;border-radius:9px;color:var(--theme-text-secondary);background:var(--theme-surface-raised);}
            .character-sheet-theme[data-theme] .ct-v50-kicker {margin:0 0 3px;text-transform:uppercase;letter-spacing:.1em;font-size:9px;font-weight:850;color:var(--theme-label);}
            .character-sheet-theme[data-theme] .ct-v50-picker-body {min-height:0;overflow-y:auto;padding:14px 18px 19px;}
            .character-sheet-theme[data-theme] .ct-v50-picker-body .character-transformations-v14-npc-list {max-height:none;margin-top:10px;}
            .character-sheet-theme[data-theme] .ct-v50-picker-empty {font-size:11px;color:var(--theme-text-secondary);margin-top:12px;}
            .character-sheet-theme[data-theme] .ct-v50-confirm {z-index:2;position:relative;padding:20px 22px;width:min(92vw,430px);}
            .character-sheet-theme[data-theme] .ct-v50-confirm > p:not(.ct-v50-kicker):not(.character-transformations-v14-error) {font-size:12px;color:var(--theme-text-secondary);line-height:1.55;margin:12px 0 16px;}
            .character-sheet-theme[data-theme] .ct-v50-confirm-actions {display:flex;justify-content:flex-end;gap:8px;}
            .character-sheet-theme[data-theme] .ct-v50-cast-choices {display:grid;gap:7px;margin:10px 0 16px;}
            .character-sheet-theme[data-theme] .ct-v50-cast-choices button {text-align:left;background:var(--theme-surface-raised);border:1px solid var(--theme-border);border-radius:9px;padding:9px 11px;font-size:11px;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v50-cast-choices button.is-selected {background:var(--theme-accent-softer);border-color:var(--theme-accent);}


            /* V51: apresentação enxuta de nome, tipo, origem e ativação. */
            .character-sheet-theme[data-theme] .ct-v51-setup {padding:0 0 15px;border-bottom:1px solid var(--theme-border-soft);}
            .character-sheet-theme[data-theme] .ct-v51-identity {display:grid;grid-template-columns:minmax(0,1fr) 66px;gap:14px;align-items:end;}
            .character-sheet-theme[data-theme] .ct-v51-label {display:block;margin:0 0 7px;font-size:9px;letter-spacing:.09em;text-transform:uppercase;font-weight:900;color:var(--theme-label);}
            .character-sheet-theme[data-theme] .ct-v51-name {height:44px;font-size:14px;font-weight:750;}
            .character-sheet-theme[data-theme] .ct-v51-image-wrap {position:relative;width:66px;height:66px;}
            .character-sheet-theme[data-theme] .ct-v51-photo-remove {position:absolute;z-index:3;top:-6px;right:-6px;width:20px;height:20px;border-radius:50%;border:1px solid var(--theme-border);background:var(--theme-surface-raised);color:var(--theme-text-strong);font-size:15px;line-height:1;}
            .character-sheet-theme[data-theme] .ct-v51-photo-remove:hover {background:var(--theme-accent-softer);}
            .character-sheet-theme[data-theme] .ct-v51-image-select {cursor:pointer;display:flex;width:66px;height:66px;align-items:center;justify-content:center;position:relative;border:1px dashed var(--theme-border-strong);border-radius:11px;background:var(--theme-surface-inset);color:var(--theme-text-secondary);overflow:hidden;}
            .character-sheet-theme[data-theme] .ct-v51-image-select:hover {border-color:var(--theme-accent);background:var(--theme-accent-softer);}
            .character-sheet-theme[data-theme] .ct-v51-image-select img {height:100%;width:100%;object-fit:cover;object-position:top center;}
            .character-sheet-theme[data-theme] .ct-v51-image-select .ct-v51-photo-label {position:absolute;bottom:0;left:0;right:0;padding:4px 2px;font-size:8px;font-weight:900;text-align:center;background:var(--theme-surface-card);color:var(--theme-text-strong);border-top:1px solid var(--theme-border-soft);}
            .character-sheet-theme[data-theme] .ct-v51-type-head {margin:14px 0 8px;}
            .character-sheet-theme[data-theme] .ct-v51-switch {display:grid;grid-template-columns:1fr 1fr;gap:8px;}
            .character-sheet-theme[data-theme] .ct-v51-switch button {min-height:42px;padding:9px 12px;border:1px solid var(--theme-border);border-radius:10px;display:flex;align-items:center;justify-content:center;gap:8px;background:var(--theme-surface-raised);color:var(--theme-text-secondary);font-size:11px;font-weight:850;transition:background .14s,border-color .14s,color .14s;}
            .character-sheet-theme[data-theme] .ct-v51-switch button:hover {background:var(--theme-card-hover);}
            .character-sheet-theme[data-theme] .ct-v51-switch button.is-active {border-color:var(--theme-accent);background:var(--theme-accent-softer);color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v51-npc-selection {margin-top:10px;display:flex;align-items:center;justify-content:space-between;gap:10px;min-width:0;padding:9px 11px;border:1px solid var(--theme-border);border-radius:10px;background:var(--theme-surface-card);}
            .character-sheet-theme[data-theme] .ct-v51-npc-selection strong {display:block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v51-npc-selection small {display:block;margin-top:2px;font-size:9px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v51-npc-selection button {flex:0 0 auto;border:1px solid var(--theme-border-strong);border-radius:8px;padding:8px 11px;background:var(--theme-surface-raised);font-size:10px;font-weight:850;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v51-activation {margin-top:15px;padding-top:0;border:0;}
            .character-sheet-theme[data-theme] .ct-v51-activation .ct-v51-switch {margin-top:10px;}
            .character-sheet-theme[data-theme] .ct-v51-linked-selection {width:100%;min-height:64px;display:flex;align-items:center;gap:12px;margin-top:10px;padding:10px 12px;border:1px solid var(--theme-border);border-radius:11px;text-align:left;background:var(--theme-surface-card);color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v51-linked-selection:hover {border-color:var(--theme-accent);background:var(--theme-card-hover);}
            .character-sheet-theme[data-theme] .ct-v51-linked-icon {display:flex;width:36px;height:36px;flex:0 0 36px;align-items:center;justify-content:center;border-radius:9px;background:var(--theme-accent-softer);color:var(--theme-accent-strong);font-size:18px;}
            .character-sheet-theme[data-theme] .ct-v51-linked-copy {flex:1;min-width:0;}
            .character-sheet-theme[data-theme] .ct-v51-linked-copy strong {display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:Georgia,serif;font-size:15px;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v51-linked-copy small {display:block;margin-top:3px;font-size:10px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v51-linked-arrow {font-size:16px;color:var(--theme-label);}
            .character-sheet-theme[data-theme] .ct-v51-helper {margin:7px 1px 0;font-size:10px;line-height:1.5;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v51-spell-row {width:100%;display:flex;align-items:center;gap:12px;padding:11px;border:1px solid var(--theme-border);border-radius:10px;text-align:left;background:var(--theme-surface-raised);color:var(--theme-text-strong);transition:background .14s,border-color .14s;}
            .character-sheet-theme[data-theme] .ct-v51-spell-row:hover,.character-sheet-theme[data-theme] .ct-v51-spell-row.is-active {background:var(--theme-accent-softer);border-color:var(--theme-accent);}
            .character-sheet-theme[data-theme] .ct-v51-spell-level {display:flex;width:38px;height:38px;flex:0 0 38px;align-items:center;justify-content:center;border:1px solid var(--theme-border);border-radius:9px;background:var(--theme-surface-inset);font-family:Georgia,serif;font-weight:900;font-size:16px;}
            .character-sheet-theme[data-theme] .ct-v51-spell-row-copy {min-width:0;flex:1;}
            .character-sheet-theme[data-theme] .ct-v51-spell-row-copy strong {display:block;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-family:Georgia,serif;font-size:14px;}
            .character-sheet-theme[data-theme] .ct-v51-spell-row-copy small {display:block;margin-top:4px;font-size:10px;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v51-selection-mark {color:var(--theme-accent-strong);font-size:16px;font-weight:900;}
            .character-sheet-theme[data-theme] .ct-v51-picker-search {position:relative;}
            .character-sheet-theme[data-theme] .ct-v51-picker-search input {width:100%;padding-left:35px;}
            .character-sheet-theme[data-theme] .ct-v51-picker-search > span {position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--theme-text-secondary);pointer-events:none;}
            .character-sheet-theme[data-theme] .ct-v51-picker-list {display:grid;gap:8px;margin-top:12px;max-height:53dvh;overflow:auto;}
            .character-sheet-theme[data-theme] .ct-v51-picker-note {margin-top:12px;font-size:10px;line-height:1.55;color:var(--theme-text-secondary);}
            .character-sheet-theme[data-theme] .ct-v50-picker .character-transformations-v14-npc:disabled {cursor:not-allowed;opacity:.58;}
            .character-sheet-theme[data-theme] .ct-v50-picker .character-transformations-v14-npc:disabled:hover {border-color:var(--theme-border);background:var(--theme-surface-raised);}
            .character-sheet-theme[data-theme] .ct-v50-picker-head {background:var(--theme-modal-header-background);}
            .character-sheet-theme[data-theme] .ct-v50-picker {overflow:hidden;}
            @media(max-width:520px) { .character-sheet-theme[data-theme] .ct-v51-identity {grid-template-columns:minmax(0,1fr) 60px;gap:9px;} .character-sheet-theme[data-theme] .ct-v51-image-wrap,.character-sheet-theme[data-theme] .ct-v51-image-select{width:60px;height:60px;} .character-sheet-theme[data-theme] .ct-v51-npc-selection{align-items:flex-start;flex-wrap:wrap;} }

            /* V52 — fluxo visual: escolha da ficha antes da identidade. */
            .character-sheet-theme[data-theme] .ct-v51-setup {padding-bottom:18px;}
            .character-sheet-theme[data-theme] .ct-v52-step-head {display:flex;align-items:center;gap:9px;margin:1px 0 12px;}
            .character-sheet-theme[data-theme] .ct-v52-step-number {width:24px;height:24px;flex:0 0 24px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--theme-border);border-radius:8px;background:var(--theme-surface-inset);color:var(--theme-label);font-size:9px;font-weight:900;letter-spacing:.02em;}
            .character-sheet-theme[data-theme] .ct-v52-step-title {margin:0;font-family:Georgia, 'Times New Roman', serif;font-size:16px;font-weight:900;line-height:1.1;color:var(--theme-text-strong);}
            .character-sheet-theme[data-theme] .ct-v52-type-switch {gap:10px;}
            .character-sheet-theme[data-theme] .ct-v52-type-switch button {position:relative;min-height:56px;justify-content:flex-start;gap:12px;padding:12px 14px;font-size:12px;text-align:left;border-radius:11px;transition:border-color .15s ease,background .15s ease,box-shadow .15s ease,transform .15s ease;}
            .character-sheet-theme[data-theme] .ct-v52-type-switch button.is-active {box-shadow:inset 3px 0 0 var(--theme-accent),0 0 0 1px color-mix(in srgb,var(--theme-accent) 10%,transparent);}
            .character-sheet-theme[data-theme] .ct-v52-type-switch button:focus-visible {outline:2px solid var(--theme-accent);outline-offset:2px;}
            .character-sheet-theme[data-theme] .ct-v52-type-symbol {width:31px;height:31px;flex:0 0 31px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--theme-border-soft);border-radius:9px;background:var(--theme-surface-inset);color:var(--theme-label);font-size:17px;}
            .character-sheet-theme[data-theme] .ct-v52-type-switch button.is-active .ct-v52-type-symbol {border-color:var(--theme-border-strong);background:var(--theme-surface-raised);color:var(--theme-accent-strong);}
            .character-sheet-theme[data-theme] .ct-v52-identity-head {margin:17px 0 11px;padding-top:17px;border-top:1px solid var(--theme-border-soft);}
            .character-sheet-theme[data-theme] .ct-v51-identity {align-items:end;gap:12px;}
            .character-sheet-theme[data-theme] .ct-v51-name {height:45px;}
            .character-sheet-theme[data-theme] .ct-v51-image-select {border-style:solid;background:var(--theme-surface-card);}
            .character-sheet-theme[data-theme] .ct-v51-npc-selection {margin-top:10px;padding:10px 12px;}
            .character-sheet-theme[data-theme] .ct-v52-npc-icon {width:33px;height:33px;flex:0 0 33px;display:flex;align-items:center;justify-content:center;border-radius:9px;background:var(--theme-accent-softer);color:var(--theme-accent-strong);font-size:16px;}
            .character-sheet-theme[data-theme] .ct-v52-npc-copy {flex:1;}
            .character-sheet-theme[data-theme] .ct-v51-npc-selection button:hover {border-color:var(--theme-accent);background:var(--theme-accent-softer);}
            .character-sheet-theme[data-theme] .ct-v51-activation {margin-top:17px;}
            .character-sheet-theme[data-theme] .ct-v51-activation > .ct-v52-step-head {margin-bottom:9px;}
            .character-sheet-theme[data-theme] .ct-v51-activation .ct-v51-switch {margin-top:0;}
            @media (max-width:520px) {
                .character-sheet-theme[data-theme] .ct-v52-type-switch {gap:7px;}
                .character-sheet-theme[data-theme] .ct-v52-type-switch button {padding:9px 10px;gap:7px;min-height:52px;font-size:10px;}
                .character-sheet-theme[data-theme] .ct-v52-type-symbol {width:26px;height:26px;flex-basis:26px;font-size:14px;}
                .character-sheet-theme[data-theme] .ct-v52-step-title {font-size:15px;}
            }
            @media(max-width:550px) { .character-sheet-theme[data-theme] .ct-v50-simple-duration {display:grid;grid-template-columns:1fr 1fr;} .character-sheet-theme[data-theme] .ct-v50-check {grid-column:1/-1;} }

            /* V53 — Transformações: cartões claros e organização sem numeração. */
            .character-sheet-theme[data-theme] .ct-v52-step-head {
                gap: 0; margin-bottom: 12px;
            }
            .character-sheet-theme[data-theme] .ct-v52-step-title {
                font-size: 17px; letter-spacing: -.015em;
            }
            .character-sheet-theme[data-theme] .ct-v51-setup,
            .character-sheet-theme[data-theme] .ct-v51-activation {
                padding-bottom: 0;
            }
            .character-sheet-theme[data-theme] .ct-v53-list {
                display: grid; gap: 12px; padding: 15px;
                max-height: min(65dvh, 630px); overflow-y: auto;
            }
            .character-sheet-theme[data-theme] .ct-v53-card {
                display: grid;
                grid-template-columns: 114px minmax(0, 1fr) auto;
                gap: 17px; align-items: center;
                min-height: 148px; padding: 13px;
                border: 1px solid var(--theme-border);
                border-radius: 16px;
                background: var(--theme-card-background, var(--theme-surface-card));
                color: var(--theme-text-primary);
                box-shadow: none;
                transition: border-color .16s ease, background .16s ease;
            }
            .character-sheet-theme[data-theme] .ct-v53-card:hover,
            .character-sheet-theme[data-theme] .ct-v53-card:focus-within {
                border-color: var(--theme-border-strong);
                background: var(--theme-card-hover, var(--theme-surface-card));
            }
            .character-sheet-theme[data-theme] .ct-v53-card.is-active {
                border-color: var(--theme-accent);
            }
            .character-sheet-theme[data-theme] .ct-v53-portrait {
                position: relative; width: 114px; height: 120px;
                display: flex; align-items: center; justify-content: center;
                overflow: hidden; border-radius: 12px;
                border: 1px solid var(--theme-border-soft);
                background: var(--theme-surface-inset);
                color: var(--theme-label);
            }
            .character-sheet-theme[data-theme] .ct-v53-portrait img {
                width:100%;height:100%;object-fit:cover;object-position:center top;
            }
            .character-sheet-theme[data-theme] .ct-v53-portrait svg { width: 30px; height: 30px; }
            .character-sheet-theme[data-theme] .ct-v53-current-dot {
                position:absolute;inset:auto 6px 6px auto;
                display:inline-flex;align-items:center;justify-content:center;
                width:20px;height:20px;border-radius:50%;
                background:var(--theme-state-heal);color:var(--theme-button-text);
                box-shadow:0 1px 7px rgba(0,0,0,.15);font-size:12px;
            }
            .character-sheet-theme[data-theme] .ct-v53-content { min-width: 0; }
            .character-sheet-theme[data-theme] .ct-v53-name {
                margin:0; font: 800 21px/1.16 Georgia, 'Times New Roman', serif;
                color:var(--theme-text-strong);overflow-wrap:anywhere;
            }
            .character-sheet-theme[data-theme] .ct-v53-kind {
                display:block; margin-top:5px; color:var(--theme-text-secondary);
                font-size:11px;font-weight:600;
            }
            .character-sheet-theme[data-theme] .ct-v53-meta {
                display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-top:11px;
            }
            .character-sheet-theme[data-theme] .ct-v53-tag {
                display:inline-flex;align-items:center;min-height:23px;
                padding:3px 8px;border:1px solid var(--theme-border-soft);
                border-radius:999px;background:var(--theme-surface-card-alt);
                color:var(--theme-text-secondary);font-size:9px;font-weight:750;
            }
            .character-sheet-theme[data-theme] .ct-v53-tag.is-magic {
                background:var(--theme-accent-softer);color:var(--theme-accent-strong);
            }
            .character-sheet-theme[data-theme] .ct-v53-tag.is-active {
                border-color:var(--theme-accent);color:var(--theme-text-strong);
            }
            .character-sheet-theme[data-theme] .ct-v53-actions {
                display:flex;gap:8px;align-items:center;justify-content:flex-end;
                align-self:stretch;max-width:none;
            }
            .character-sheet-theme[data-theme] .ct-v53-actions .ct-form-primary {
                min-height:38px;white-space:nowrap;border-radius:10px;
            }
            .character-sheet-theme[data-theme] .ct-v53-actions .ct-form-more-toggle {
                width:36px;height:38px;border-radius:10px;
            }
            .character-sheet-theme[data-theme] .ct-v53-magic-hint {
                max-width:135px; font-size:10px;line-height:1.4;
                color:var(--theme-text-secondary);text-align:center;
            }
            .character-sheet-theme[data-theme] .ct-v53-card .ct-form-more {
                align-self:center;
            }
            .character-sheet-theme[data-theme] .ct-v53-card .ct-form-more-options {
                position:static;
                width:190px;max-width:min(60vw,220px);
                margin-top:7px;z-index:180;
                background:var(--theme-surface-raised);
                border:1px solid var(--theme-border);
                box-shadow:var(--theme-shadow-strong);
            }
            .character-sheet-theme[data-theme] .ct-v53-list {overflow-x:visible;}
            @media(max-width:700px) {
                .character-sheet-theme[data-theme] .ct-v53-card {
                    grid-template-columns:90px minmax(0,1fr);gap:11px;min-height:122px;
                }
                .character-sheet-theme[data-theme] .ct-v53-portrait {width:90px;height:100px;}
                .character-sheet-theme[data-theme] .ct-v53-name {font-size:17px;}
                .character-sheet-theme[data-theme] .ct-v53-actions {
                    grid-column:2;justify-content:flex-start;align-self:auto;
                }
                .character-sheet-theme[data-theme] .ct-v53-magic-hint {max-width:none;text-align:left;}
            }
            @media(max-width:430px) {
                .character-sheet-theme[data-theme] .ct-v53-card {
                    grid-template-columns:76px minmax(0,1fr);padding:10px;
                }
                .character-sheet-theme[data-theme] .ct-v53-portrait {width:76px;height:90px;}
                .character-sheet-theme[data-theme] .ct-v53-meta {margin-top:7px;}
            }
    </style>

    <section
        x-data="{
            open: false,
            createOpen: false,

            saving: false,
            actingId: null,
            normalizing: false,
            deletingId: null,

            deleteCandidate: null,
            deleteError: null,

            specialPrompt: null,
            specialPromptCandidates: [],

            concentrationCheck: null,
            concentrationRoll: null,
            concentrationRolling: false,
            concentrationLoading: false,
            concentrationError: null,

            saveError: null,
            actionError: null,
            concentrationConflictItem: null,
            concentrationConflictCurrent: null,
            concentrationConfirmLoading: false,
            activeDurationRemaining: @js($activeState?->duration_remaining),

            handleDurationUpdate(timer) {
                if (!this.active) return;
                if (timer && Number(timer.transformation_id) === Number(this.active.id)) {
                    this.activeDurationRemaining = timer.duration_remaining;
                    this.roundInfo = { ...this.roundInfo, ...timer };
                } else if (this.activeDurationRemaining !== null
                    && this.activeDurationRemaining !== undefined) {
                    // A duração pode ter vencido enquanto a ficha estava aberta.
                    window.location.reload();
                }
            },

            cancelConcentrationReplacement() {
                this.concentrationConflictItem = null;
                this.concentrationConflictCurrent = null;
                this.actionError = null;
            },

            async confirmConcentrationReplacement() {
                const item = this.concentrationConflictItem;
                if (!item) return;
                this.concentrationConflictItem = null;
                this.concentrationConflictCurrent = null;
                await this.activateTransformation(item, true);
            },

            editingId: null,

            imageFile: null,
            imagePreview: null,
            imageRemove: false,

            monsterSearch: '',
            optionMenuId: null,
            roundModalOpen: false,
            roundLoading: false,
            roundError: '',
            roundInfo: {
                current_round: @js($activeState?->combat?->current_round),
                current_turn: @js($activeState?->combat?->current_turn),
                combat_name: @js($activeState?->combat?->name),
                combat_active: @js((bool) ($activeState?->combat?->is_active ?? false)),
                started_round: @js($activeState?->activated_round),
                started_turn: @js($activeState?->activated_turn),
                initiative: null,
                duration_remaining: @js($activeState?->duration_remaining),
            },

            currentNpcImage() {
                if (this.form.type !== 'monster_sheet') return null;
                const npc = this.npcs.find(n => Number(n.id) === Number(this.form.source_npc_id));
                return npc?.image_url || null;
            },

            async refreshRoundInfo() {
                if (!this.active || this.roundLoading) return;
                this.roundLoading = true;
                this.roundError = '';
                try {
                    const url = this.concentrationStateUrl
                        + (this.concentrationStateUrl.includes('?') ? '&' : '?')
                        + 'scope=concentration';
                    const response = await this.timedFetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        cache: 'no-store',
                    });
                    const data = await this.jsonResponse(response);
                    const timer = data?.transformation_timer;
                    if (timer && Number(timer.transformation_id) === Number(this.active.id)) {
                        this.roundInfo = { ...this.roundInfo, ...timer };
                        this.activeDurationRemaining = timer.duration_remaining;
                    } else if (this.active) {
                        // A forma pode ter terminado por duração, falha ou conjuração.
                        window.location.reload();
                    }
                } catch (error) {
                    this.roundError = error?.message || 'Não foi possível atualizar a iniciativa.';
                } finally {
                    this.roundLoading = false;
                }
            },

            openRoundTracker() {
                if (!this.active) return;
                this.roundModalOpen = true;
                this.refreshRoundInfo();
            },

            transformations: JSON.parse(
                $el.dataset.transformations
                || '[]'
            ),

            active: JSON.parse(
                $el.dataset.active
                || 'null'
            ),

            npcs: JSON.parse(
                $el.dataset.npcs
                || '[]'
            ),

            linkedSpells: JSON.parse($el.dataset.linkedSpells || '[]'),
            npcPickerOpen: false,
            spellPickerOpen: false,
            spellSearch: '',
            helpType: null,
            castTransformationPrompt: null,
            castTransformationMatches: [],
            castTransformationError: '',
            durationModalOpen: false,
            durationCandidate: null,
            durationConfirmedReplacement: false,
            durationFromSpell: false,
            durationRollMode: 'automatic',
            durationRollResult: null,
            durationRollError: '',
            durationRollBusy: false,
            durationManualValue: '',
            durationDieFaces: [],
            durationAnimInterval: null,
            durationAnimTimeout: null,

            durationFormulaParts(formula) {
                const match = String(formula || '').replace(/\s+/g, '').match(/^(\d{1,2})d(4|6|8|10|12|20|100)([+-]\d{1,3})?$/i);
                if (!match) return null;
                const count = Number(match[1]);
                const sides = Number(match[2]);
                const modifier = Number(match[3] || 0);
                if (count < 1 || count > 12 || modifier < -100 || modifier > 100 || count + modifier < 1 || count * sides + modifier > 10000) return null;
                return { count, sides, modifier };
            },

            get durationParts() { return this.durationFormulaParts(this.durationCandidate?.duration_formula); },

            clearDurationAnimation() {
                if (this.durationAnimInterval !== null) clearInterval(this.durationAnimInterval);
                if (this.durationAnimTimeout !== null) clearTimeout(this.durationAnimTimeout);
                this.durationAnimInterval = null;
                this.durationAnimTimeout = null;
            },

            closeDurationModal() {
                if (this.actingId !== null || this.durationRollBusy) return;
                this.clearDurationAnimation();
                this.durationModalOpen = false;
                this.durationCandidate = null;
                this.durationRollResult = null;
                this.durationRollError = '';
            },

            openDurationModal(item, confirmedReplacement, fromSpell) {
                this.clearDurationAnimation();
                this.durationCandidate = item;
                this.durationConfirmedReplacement = !!confirmedReplacement;
                this.durationFromSpell = !!fromSpell;
                this.durationRollMode = 'automatic';
                this.durationRollResult = null;
                this.durationManualValue = '';
                this.durationRollError = '';
                this.durationDieFaces = Array.from({length: this.durationFormulaParts(item.duration_formula)?.count || 0}, () => 1);
                this.durationModalOpen = true;
                this.$nextTick(() => this.rollDurationDice());
            },

            async rollDurationDice() {
                if (this.durationRollBusy || !this.durationCandidate) return;
                const parts = this.durationParts;
                if (!parts) { this.durationRollError = 'Fórmula inválida nesta transformação.'; return; }
                this.durationRollBusy = true;
                this.durationRollMode = 'automatic';
                this.durationRollResult = null;
                this.durationRollError = '';
                this.clearDurationAnimation();
                const started = Date.now();
                const generatePreview = () => {
                    this.durationDieFaces = this.durationDieFaces.map(() =>
                        1 + Math.floor(Math.random() * parts.sides)
                    );
                };
                generatePreview();
                this.durationAnimInterval = setInterval(generatePreview, 75);
                try {
                    // Uma chamada ao serviço existente POR dado permite exibir as
                    // faces reais retornadas pelo backend, não valores inventados.
                    const rolls = await Promise.all(Array.from({length: parts.count}, async () => {
                        const response = await this.timedFetch(this.diceRollUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({expression: '1d' + parts.sides}),
                        });
                        const data = await this.jsonResponse(response);
                        const face = this.extractRollTotal(data);
                        if (!Number.isInteger(face) || face < 1 || face > parts.sides) {
                            throw new Error('O serviço de dados não retornou uma face válida.');
                        }
                        return face;
                    }));
                    await new Promise(resolve => {
                        this.durationAnimTimeout = setTimeout(resolve,
                            Math.max(0, 900 - (Date.now() - started)));
                    });
                    this.clearDurationAnimation();
                    this.durationDieFaces = rolls;
                    this.durationRollResult = rolls.reduce((sum, value) => sum + value, parts.modifier);
                } catch (error) {
                    this.durationRollError = error?.message || 'Não foi possível rolar os dados.';
                    this.durationRollResult = null;
                    this.durationDieFaces = [];
                } finally {
                    this.clearDurationAnimation();
                    this.durationRollBusy = false;
                }
            },

            async applyDurationRoll() {
                if (!this.durationCandidate || this.durationRollBusy || this.actingId !== null) return;
                const parts = this.durationParts;
                if (!parts) return;
                const value = this.durationRollMode === 'manual' ? Number(this.durationManualValue) : this.durationRollResult;
                if (!Number.isInteger(value) || value < parts.count + parts.modifier || value > parts.count * parts.sides + parts.modifier) {
                    this.durationRollError = 'Informe um resultado possível para a fórmula.'; return;
                }
                this.durationRollError = '';
                await this.activateTransformation(this.durationCandidate, this.durationConfirmedReplacement, this.durationFromSpell, value, this.durationRollMode);
                if (this.actionError) this.durationRollError = this.actionError;
            },

            handleLinkedSpellCast(detail) {
                const spellId = Number(detail?.spell_id || 0);
                if (!spellId || this.active || this.createOpen) return;
                const matches = this.transformations.filter(item =>
                    item.casting_time === 'spell'
                    && Number(item.linked_spell_id) === spellId
                );
                if (!matches.length) return;
                this.castTransformationMatches = matches;
                this.castTransformationError = '';
                this.castTransformationPrompt = matches[0];
            },

            async confirmLinkedTransformation() {
                const form = this.castTransformationPrompt;
                if (!form || this.actingId !== null) return;
                this.castTransformationError = '';
                await this.activateTransformation(form, false, true);
                if (this.actionError) {
                    this.castTransformationError = this.actionError;
                } else {
                    this.castTransformationPrompt = null;
                }
            },

            linkedSpellName(item) {
                const linkedId = Number(item?.linked_spell_id || 0);
                const spell = this.linkedSpells.find(entry => Number(entry.id) === linkedId);
                return spell?.name || 'Feitiço vinculado';
            },

            get selectedLinkedSpell() {
                return this.linkedSpells.find(s => Number(s.id) === Number(this.form.linked_spell_id)) || null;
            },

            get filteredLinkedSpells() {
                const search = String(this.spellSearch || '').trim().toLocaleLowerCase('pt-BR');
                return this.linkedSpells.filter(spell =>
                    !search || String(spell.name || '').toLocaleLowerCase('pt-BR').includes(search)
                    || String(spell.level) === search
                );
            },

            openNpcPicker() {
                this.spellPickerOpen = false;
                this.monsterSearch = '';
                this.npcPickerOpen = true;
            },

            openSpellPicker() {
                this.npcPickerOpen = false;
                this.spellSearch = '';
                this.spellPickerOpen = true;
            },

            selectLinkedSpell(id) {
                const spell = this.linkedSpells.find(spell => Number(spell.id) === Number(id));
                if (!spell) return;
                this.form.linked_spell_id = Number(spell.id);
                this.form.requires_concentration = true;
                this.spellPickerOpen = false;
            },

            storeUrl:
                $el.dataset.storeUrl
                || '',

            updateUrlTemplate:
                $el.dataset.updateUrlTemplate
                || '',

            activateUrlTemplate:
                $el.dataset.activateUrlTemplate
                || '',

            deleteUrlTemplate:
                $el.dataset.deleteUrlTemplate
                || '',

            sheetPrepareUrlTemplate:
                $el.dataset.sheetPrepareUrlTemplate
                || '',

            sheetEditBaseUrl:
                $el.dataset.sheetEditBaseUrl
                || '',

            concentrationUrl:
                $el.dataset.concentrationUrl
                || '',

            concentrationStateUrl:
                $el.dataset.concentrationStateUrl
                || '',

            diceRollUrl:
                $el.dataset.diceRollUrl
                || '',

            preparingSheetId: null,

            deactivateUrl:
                $el.dataset.deactivateUrl
                || '',

            form: {
                name: '',
                type: 'character_sheet',
                source_npc_id: null,
                casting_time: 'none',
                linked_spell_id: null,
                requires_concentration: false,
                duration_type: 'indefinite',
                duration_value: 1,
                duration_formula: '2d6+1',
                is_special: false,
                special_trigger: 'half_hp',
                has_limited_uses: false,
                uses_max: 1,
                recovery: 'daily',
                applies_exhaustion: false,
                exhaustion_levels: 1,
                exhaustion_timing: 'end',
            },

            get isEditing() {
                return this.editingId !== null;
            },

            get modalTitle() {
                return this.isEditing
                    ? 'Editar transformação'
                    : 'Criar transformação';
            },

            get modalEyebrow() {
                return this.isEditing
                    ? 'Editar forma'
                    : 'Nova forma';
            },

            get submitLabel() {
                return this.isEditing
                    ? 'Salvar alterações'
                    : 'Criar transformação';
            },

            get filteredNpcs() {
                const search =
                    String(
                        this.monsterSearch
                        || ''
                    )
                        .trim()
                        .toLocaleLowerCase(
                            'pt-BR'
                        );

                const source =
                    search
                        ? this.npcs.filter(
                            npc =>
                                String(
                                    npc?.name
                                    || ''
                                )
                                    .toLocaleLowerCase(
                                        'pt-BR'
                                    )
                                    .includes(
                                        search
                                    )
                        )
                        : [
                            ...this.npcs,
                        ];

                return source.sort(
                    (a, b) => {
                        const supportDiff =
                            Number(
                                !!b?.supported
                            )
                            -
                            Number(
                                !!a?.supported
                            );

                        if (supportDiff !== 0) {
                            return supportDiff;
                        }

                        return String(
                            a?.name
                            || ''
                        )
                            .localeCompare(
                                String(
                                    b?.name
                                    || ''
                                ),
                                'pt-BR'
                            );
                    }
                );
            },

            get selectedNpc() {
                if (!this.form.source_npc_id) {
                    return null;
                }

                return this.npcs.find(
                    npc =>
                        Number(npc.id)
                        === Number(
                            this.form.source_npc_id
                        )
                ) || null;
            },

            get selectedNpcSupported() {
                return (
                    !this.selectedNpc
                    || !!this.selectedNpc.supported
                );
            },

            /*
            |--------------------------------------------------------------------------
            | TRANSFORMAÇÃO ESPECIAL — GATILHO LOCAL DO HERO
            |--------------------------------------------------------------------------
            |
            | A vida é controlada pelo Hero. Este componente apenas escuta a
            | alteração, verifica suas próprias formas e pergunta ao jogador.
            |--------------------------------------------------------------------------
            */
            handleCharacterHpChanged(detail) {
                if (
                    !detail
                    || this.active
                ) {
                    return;
                }

                if (
                    Number(
                        detail.characterId
                    )
                    !==
                    {{ (int) $character->id }}
                ) {
                    return;
                }

                const previousHp =
                    Math.max(
                        0,
                        parseInt(
                            detail.previousHp
                        ) || 0
                    );

                const currentHp =
                    Math.max(
                        0,
                        parseInt(
                            detail.currentHp
                        ) || 0
                    );

                const maxHp =
                    Math.max(
                        1,
                        parseInt(
                            detail.maxHp
                        ) || 1
                    );

                if (
                    currentHp >= previousHp
                ) {
                    return;
                }

                const halfHp =
                    Math.max(
                        1,
                        Math.floor(
                            maxHp / 2
                        )
                    );

                const crossedHalf =
                    previousHp > halfHp
                    && currentHp <= halfHp;

                const crossedOneOrZero =
                    previousHp > 1
                    && currentHp <= 1;

                if (
                    !crossedHalf
                    && !crossedOneOrZero
                ) {
                    return;
                }

                const candidates =
                    this.transformations
                        .filter(
                            item => {
                                if (
                                    !item
                                    || !item.is_special
                                ) {
                                    return false;
                                }

                                if (
                                    item.uses_max !== null
                                    && item.uses_max !== undefined
                                    && Number(
                                        item.uses_current
                                        ?? 0
                                    ) < 1
                                ) {
                                    return false;
                                }

                                if (
                                    item.type === 'monster_sheet'
                                    && item.monster_supported === false
                                ) {
                                    return false;
                                }

                                if (
                                    item.special_trigger === 'half_hp'
                                ) {
                                    return crossedHalf;
                                }

                                if (
                                    item.special_trigger === 'one_or_zero_hp'
                                ) {
                                    return crossedOneOrZero;
                                }

                                return false;
                            }
                        )
                        .sort(
                            (a, b) => {
                                const aPriority =
                                    a.special_trigger
                                        === 'one_or_zero_hp'
                                            ? 0
                                            : 1;

                                const bPriority =
                                    b.special_trigger
                                        === 'one_or_zero_hp'
                                            ? 0
                                            : 1;

                                return aPriority
                                    - bPriority;
                            }
                        );

                if (
                    candidates.length === 0
                ) {
                    return;
                }

                this.actionError =
                    null;

                this.specialPromptCandidates =
                    candidates;

                this.specialPrompt = {
                    previousHp,
                    currentHp,
                    maxHp,
                    halfHp,
                };
            },

            specialTriggerLabel(item) {
                if (
                    item?.special_trigger
                    === 'one_or_zero_hp'
                ) {
                    return '1 ou 0 PV';
                }

                return 'Metade dos PV';
            },

            dismissSpecialPrompt() {
                if (
                    this.actingId !== null
                ) {
                    return;
                }

                this.specialPrompt =
                    null;

                this.specialPromptCandidates =
                    [];

                this.actionError =
                    null;
            },

            async confirmSpecialTransformation(item) {
                if (
                    !item
                    || this.actingId !== null
                ) {
                    return;
                }

                await this.activateTransformation(
                    item
                );
            },


            openSelector() {
                this.actionError = null;
                this.open = true;
            },

            closeSelector() {
                if (
                    this.createOpen
                    || this.deleteCandidate
                    || this.actingId !== null
                    || this.normalizing
                    || this.deletingId !== null
                ) {
                    return;
                }

                this.open = false;
                this.actionError = null;
            },

            resetEditor() {
                if (
                    this.imagePreview
                    && this.imagePreview.startsWith(
                        'blob:'
                    )
                ) {
                    URL.revokeObjectURL(
                        this.imagePreview
                    );
                }

                this.editingId = null;

                this.form = {
                    name: '',
                    type: 'character_sheet',
                    source_npc_id: null,
                    casting_time: 'none',
                    linked_spell_id: null,
                    requires_concentration: false,
                    duration_type: 'indefinite',
                    duration_value: 1,
                    duration_formula: '2d6+1',
                    is_special: false,
                    special_trigger: 'half_hp',
                    has_limited_uses: false,
                    uses_max: 1,
                    recovery: 'daily',
                    applies_exhaustion: false,
                    exhaustion_levels: 1,
                    exhaustion_timing: 'end',
                };

                this.imageFile = null;
                this.imagePreview = null;
                this.imageRemove = false;
                this.helpType = null;

                this.monsterSearch = '';
                this.saveError = null;
            },

            openCreate() {
                this.resetEditor();
                this.createOpen = true;
            },

            openEdit(item) {
                if (!item) {
                    return;
                }

                this.resetEditor();

                this.editingId =
                    Number(item.id);

                this.form = {
                    name:
                        String(
                            item.name
                            || ''
                        ),

                    type:
                        item.type
                        || 'character_sheet',

                    source_npc_id:
                        item.source_npc_id
                        ?? null,

                    casting_time:
                        ['spell', 'none'].includes(item.casting_time) ? item.casting_time : 'none',
                    linked_spell_id: item.linked_spell_id || null,

                    requires_concentration:
                        !!item.requires_concentration,

                    duration_type:
                        item.duration_type
                        || 'indefinite',
                    duration_formula: item.duration_formula || '2d6+1',

                    duration_value:
                        item.duration_value
                        ?? 1,

                    is_special:
                        !!item.is_special,

                    special_trigger:
                        item.special_trigger
                        || 'half_hp',

                    has_limited_uses:
                        item.uses_max !== null
                        && item.uses_max !== undefined,

                    uses_max:
                        item.uses_max
                        ?? 1,

                    recovery:
                        item.recovery
                        || 'daily',

                    applies_exhaustion:
                        !!item.applies_exhaustion,

                    exhaustion_levels:
                        item.exhaustion_levels
                        || 1,

                    exhaustion_timing:
                        item.exhaustion_timing
                        || 'end',
                };

                this.imagePreview =
                    item.image_url
                    || null;

                this.imageFile = null;
                this.imageRemove = false;
                this.saveError = null;
                this.createOpen = true;
            },

            closeCreate() {
                if (this.saving) {
                    return;
                }

                this.createOpen = false;
                this.npcPickerOpen = false;
                this.spellPickerOpen = false;
                this.resetEditor();
            },

            setType(type) {
                if (
                    ![
                        'character_sheet',
                        'monster_sheet',
                    ].includes(type)
                ) {
                    return;
                }

                this.form.type = type;

                if (
                    type !== 'monster_sheet'
                ) {
                    this.form.source_npc_id =
                        null;
                }
            },

            selectNpc(id) {
                const npc = this.npcs.find(n => Number(n.id) === Number(id));
                if (!npc || !npc.supported) return;
                this.form.source_npc_id = Number(id);
                this.npcPickerOpen = false;
            },

            handleImage(event) {
                const file =
                    event?.target?.files?.[0]
                    || null;

                if (!file) {
                    return;
                }

                this.saveError = null;

                if (
                    ![
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ].includes(file.type)
                ) {
                    this.saveError =
                        'Use uma imagem JPG, PNG ou WEBP.';

                    event.target.value = '';
                    return;
                }

                if (
                    file.size
                    > 5 * 1024 * 1024
                ) {
                    this.saveError =
                        'A imagem deve ter no máximo 5 MB.';

                    event.target.value = '';
                    return;
                }

                if (
                    this.imagePreview
                    && this.imagePreview.startsWith(
                        'blob:'
                    )
                ) {
                    URL.revokeObjectURL(
                        this.imagePreview
                    );
                }

                this.imageFile = file;
                this.imageRemove = false;

                this.imagePreview =
                    URL.createObjectURL(
                        file
                    );
            },

            removeImage() {
                if (
                    this.imagePreview
                    && this.imagePreview.startsWith(
                        'blob:'
                    )
                ) {
                    URL.revokeObjectURL(
                        this.imagePreview
                    );
                }

                this.imageFile = null;
                this.imagePreview = null;

                if (this.isEditing) {
                    this.imageRemove = true;
                }
            },

            typeLabel(item) {
                return item?.type_label
                    || 'Transformação';
            },

            recoveryLabel(item) {
                if (
                    item?.uses_max === null
                    || item?.uses_max === undefined
                ) {
                    return 'Ilimitado';
                }

                if (item.recovery === 'short_rest') {
                    return `${item.uses_current}/${item.uses_max} · Descanso Curto`;
                }

                if (item.recovery === 'daily') {
                    return `${item.uses_current}/${item.uses_max} · Por Dia`;
                }

                return `${item.uses_current}/${item.uses_max} usos`;
            },

            csrfToken() {
                return document
                    .querySelector(
                        'meta[name=&quot;csrf-token&quot;]'
                    )
                    ?.getAttribute(
                        'content'
                    )
                    || '';
            },

            async jsonResponse(response) {
                const data =
                    await response
                        .json()
                        .catch(
                            () => ({})
                        );

                if (!response.ok) {
                    const messages =
                        data?.errors
                            ? Object.values(
                                data.errors
                            )
                                .flat()
                                .filter(Boolean)
                            : [];

                    throw new Error(
                        messages.length
                            ? messages.join(' ')
                            : (
                                data?.message
                                || 'Não foi possível concluir a operação.'
                            )
                    );
                }

                return data;
            },

            async timedFetch(
                url,
                options = {},
                timeoutMs = 20000
            ) {
                const controller =
                    new AbortController();

                const timer =
                    window.setTimeout(
                        () =>
                            controller.abort(),
                        timeoutMs
                    );

                try {
                    return await fetch(
                        url,
                        {
                            ...options,
                            signal:
                                controller.signal,
                        }
                    );

                } catch (error) {
                    if (
                        error?.name
                        === 'AbortError'
                    ) {
                        throw new Error(
                            'O servidor demorou demais para responder. Tente novamente.'
                        );
                    }

                    throw error;

                } finally {
                    window.clearTimeout(
                        timer
                    );
                }
            },

            async handleCharacterDamage(detail) {
                if (
                    !detail
                    || this.concentrationLoading
                    || !this.active
                    || this.active.type !== 'character_sheet'
                    || !this.active.requires_concentration
                ) {
                    return;
                }

                if (
                    Number(
                        detail.characterId
                    )
                    !==
                    {{ (int) $character->id }}
                ) {
                    return;
                }

                if (
                    this.active.form_character_id
                    && Number(
                        detail.mechanicalCharacterId
                    )
                    !==
                    Number(
                        this.active.form_character_id
                    )
                ) {
                    return;
                }

                const damage =
                    Math.max(
                        0,
                        parseInt(
                            detail.damage
                        ) || 0
                    );

                if (damage < 1) {
                    return;
                }

                this.concentrationLoading =
                    true;

                this.concentrationError =
                    null;

                try {
                    const response =
                        await this.timedFetch(
                            this.concentrationUrl,
                            {
                                method:
                                    'POST',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    JSON.stringify({
                                        damage,
                                    }),
                            }
                        );

                    const data =
                        await this.jsonResponse(
                            response
                        );

                    const check =
                        data?.concentration_check
                        ?? null;

                    if (
                        check
                        && check.required
                    ) {
                        this.openConcentrationCheck(
                            check
                        );
                    }

                } catch (error) {
                    console.error(
                        'Erro ao preparar concentração da transformação:',
                        error
                    );

                    this.concentrationError =
                        error?.message
                        ?? 'Não foi possível preparar o teste de concentração.';

                } finally {
                    this.concentrationLoading =
                        false;
                }
            },

            openConcentrationCheck(check) {
                if (
                    !check
                    || !check.required
                ) {
                    return;
                }

                this.concentrationCheck =
                    check;

                this.concentrationRoll =
                    null;

                this.concentrationError =
                    null;

                window.dispatchEvent(
                    new CustomEvent(
                        'character-concentration-check',
                        {
                            detail:
                                check,
                        }
                    )
                );
            },

            closeConcentrationCheck() {
                if (
                    this.concentrationRolling
                ) {
                    return;
                }

                const reloadRequired =
                    !!this.concentrationRoll?.transformationEnded;

                this.concentrationCheck =
                    null;

                this.concentrationRoll =
                    null;

                this.concentrationError =
                    null;

                if (reloadRequired) {
                    window.location.reload();
                }
            },

            extractRollTotal(data) {
                const candidate =
                    data?.data?.total
                    ?? data?.data?.result
                    ?? data?.data?.value
                    ?? data?.data?.sum
                    ?? data?.total
                    ?? data?.result
                    ?? null;

                if (
                    candidate === null
                ) {
                    return null;
                }

                const parsed =
                    parseInt(
                        candidate
                    );

                return Number.isNaN(
                    parsed
                )
                    ? null
                    : parsed;
            },

            async rollConcentrationCheck() {
                if (
                    !this.concentrationCheck
                    || this.concentrationRolling
                ) {
                    return;
                }

                const check =
                    this.concentrationCheck;

                const expression =
                    String(
                        check.expression
                        || '1d20'
                    )
                        .trim();

                this.concentrationRolling =
                    true;

                this.concentrationError =
                    null;

                try {
                    const response =
                        await this.timedFetch(
                            this.diceRollUrl,
                            {
                                method:
                                    'POST',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    JSON.stringify({
                                        expression,
                                    }),
                            }
                        );

                    const data =
                        await this.jsonResponse(
                            response
                        );

                    const total =
                        this.extractRollTotal(
                            data
                        );

                    if (
                        total === null
                    ) {
                        throw new Error(
                            'A rolagem não retornou um total válido.'
                        );
                    }

                    const modifier =
                        parseInt(
                            check.modifier
                        )
                        || 0;

                    const die =
                        total
                        - modifier;

                    const difficultyClass =
                        Math.max(
                            1,
                            parseInt(
                                check.dc
                            )
                            || 10
                        );

                    // O servidor conclui o efeito, aplica exaustão e encerra
                    // a forma em caso de falha. O resultado da rolagem não
                    // pode ficar apenas no estado visual do Alpine.
                    const resolutionResponse = await this.timedFetch(
                        this.concentrationUrl,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                damage: Math.max(1, Number(check.damage) || 1),
                                transformation_id: Number(check.context?.transformation_id),
                                roll_total: total,
                            }),
                        }
                    );
                    const resolution = await this.jsonResponse(resolutionResponse);
                    const passed = !!resolution?.result?.passed;

                    this.concentrationRoll = {
                        die,
                        total,
                        modifier,
                        passed,
                        transformationEnded: !!resolution?.result?.transformation_ended,
                        formatted:
                            data?.formatted
                            ?? expression,
                    };

                    if (resolution?.result?.concentration_ended) {
                        window.dispatchEvent(new CustomEvent('character-concentration-changed'));
                    }

                    window.dispatchEvent(
                        new CustomEvent(
                            'character-dice-rolled',
                            {
                                detail: {
                                    label:
                                        check.label
                                        || 'Salvamento de Constituição',

                                    modifier:
                                        check.base_modifier
                                        ?? modifier,

                                    exhaustionPenalty:
                                        check.exhaustion_penalty
                                        ?? 0,

                                    effectiveModifier:
                                        modifier,

                                    die,
                                    total,

                                    formatted:
                                        data?.formatted
                                        ?? expression,

                                    concentration: {
                                        dc:
                                            difficultyClass,

                                        passed,

                                        damage:
                                            check.damage
                                            ?? null,

                                        context:
                                            check.context
                                            ?? {},
                                    },
                                },
                            }
                        )
                    );

                } catch (error) {
                    console.error(
                        'Erro no teste de concentração:',
                        error
                    );

                    this.concentrationError =
                        error?.message
                        ?? 'Não foi possível rolar o teste de concentração.';

                } finally {
                    this.concentrationRolling =
                        false;
                }
            },

            buildDefinitionBody() {
                const body =
                    new FormData();

                body.append(
                    'name',
                    String(
                        this.form.name
                        || ''
                    ).trim()
                );

                body.append(
                    'type',
                    this.form.type
                );

                if (
                    this.form.type
                        === 'monster_sheet'
                    && this.form.source_npc_id
                ) {
                    body.append(
                        'source_npc_id',
                        this.form.source_npc_id
                    );
                }

                body.append('casting_time', this.form.casting_time);
                if (this.form.casting_time === 'spell') {
                    body.append('linked_spell_id', String(this.form.linked_spell_id || ''));
                }

                body.append('requires_concentration',
                    this.form.casting_time === 'spell' || this.form.requires_concentration ? '1' : '0');

                body.append('duration_type', this.form.duration_type === 'dice' ? 'dice' : this.form.duration_type);
                if (this.form.duration_type === 'dice') {
                    body.append('duration_formula', String(this.form.duration_formula || '').replace(/\s+/g, ''));
                }
                if (
                    this.form.duration_type !== 'dice'
                    && this.form.duration_type !== 'indefinite'
                ) {
                    body.append(
                        'duration_value',
                        Math.max(
                            1,
                            parseInt(
                                this.form.duration_value
                            ) || 1
                        )
                    );
                }

                body.append(
                    'is_special',
                    this.form.is_special
                        ? '1'
                        : '0'
                );

                if (this.form.is_special) {
                    body.append(
                        'special_trigger',
                        this.form.special_trigger
                    );
                }

                body.append(
                    'has_limited_uses',
                    this.form.has_limited_uses
                        ? '1'
                        : '0'
                );

                if (
                    this.form.has_limited_uses
                ) {
                    body.append(
                        'uses_max',
                        Math.max(
                            1,
                            parseInt(
                                this.form.uses_max
                            ) || 1
                        )
                    );

                    body.append(
                        'recovery',
                        this.form.recovery
                    );
                }

                body.append(
                    'applies_exhaustion',
                    this.form.applies_exhaustion
                        ? '1'
                        : '0'
                );

                if (
                    this.form.applies_exhaustion
                ) {
                    body.append(
                        'exhaustion_levels',
                        Math.max(
                            1,
                            Math.min(
                                6,
                                parseInt(
                                    this.form.exhaustion_levels
                                ) || 1
                            )
                        )
                    );

                    body.append(
                        'exhaustion_timing',
                        this.form.exhaustion_timing
                    );
                }

                if (this.imageFile) {
                    body.append(
                        'image',
                        this.imageFile
                    );
                }

                if (
                    this.isEditing
                    && this.imageRemove
                ) {
                    body.append(
                        'remove_image',
                        '1'
                    );
                }

                return body;
            },

            validateEditor() {
                const name =
                    String(
                        this.form.name
                        || ''
                    ).trim();

                if (!name) {
                    return 'Informe o nome da transformação.';
                }

                if (this.form.casting_time === 'spell' && !this.form.linked_spell_id) {
                    return 'Selecione uma magia do seu Grimório.';
                }

                if (
                    this.form.type
                        === 'monster_sheet'
                    && !this.form.source_npc_id
                ) {
                    return 'Escolha uma criatura do Cofre.';
                }

                if (
                    this.form.type
                        === 'monster_sheet'
                    && this.selectedNpc
                    && !this.selectedNpc.supported
                ) {
                    return 'Esta criatura usa um formato externo. Transformações aceitam somente fichas criadas pelo Construtor de NPC do Spellbound.';
                }

                if (this.form.duration_type === 'dice' && !this.durationFormulaParts(this.form.duration_formula)) {
                    return 'Informe uma fórmula válida, como 2d6+1 (até 12 dados).';
                }
                if (
                    this.form.duration_type !== 'dice'
                    && this.form.duration_type
                        !== 'indefinite'
                    && (
                        !parseInt(
                            this.form.duration_value
                        )
                        || parseInt(
                            this.form.duration_value
                        ) < 1
                    )
                ) {
                    return 'Informe uma duração válida.';
                }

                if (
                    this.form.has_limited_uses
                    && (
                        !parseInt(
                            this.form.uses_max
                        )
                        || parseInt(
                            this.form.uses_max
                        ) < 1
                    )
                ) {
                    return 'Informe quantos usos a transformação possui.';
                }

                return null;
            },

            async saveTransformation() {
                if (this.saving) {
                    return;
                }

                const error =
                    this.validateEditor();

                if (error) {
                    this.saveError =
                        error;
                    return;
                }

                this.saving = true;
                this.saveError = null;

                try {
                    const body =
                        this.buildDefinitionBody();

                    let url =
                        this.storeUrl;

                    if (this.isEditing) {
                        url =
                            this.updateUrlTemplate
                                .replace(
                                    '__TRANSFORMATION__',
                                    this.editingId
                                );

                        /*
                         * Multipart + método real PATCH não é interpretado
                         * de forma consistente pelo PHP. Usamos o method
                         * spoofing nativo do Laravel.
                         */
                        body.append(
                            '_method',
                            'PATCH'
                        );
                    }

                    const response =
                        await this.timedFetch(
                            url,
                            {
                                method:
                                    'POST',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body,
                            }
                        );

                    await this.jsonResponse(
                        response
                    );

                    window.location.reload();

                } catch (error) {
                    console.error(
                        this.isEditing
                            ? 'Erro ao editar transformação:'
                            : 'Erro ao criar transformação:',
                        error
                    );

                    this.saveError =
                        error?.message
                        || 'Não foi possível salvar a transformação.';

                } finally {
                    this.saving = false;
                }
            },

            async openCharacterSheet(item) {
                if (
                    !item
                    || item.type !== 'character_sheet'
                    || this.preparingSheetId !== null
                    || this.actingId !== null
                ) {
                    return;
                }

                this.actionError =
                    null;

                if (item.sheet_edit_url) {
                    window.location.href =
                        item.sheet_edit_url;

                    return;
                }

                this.preparingSheetId =
                    Number(
                        item.id
                    );

                try {
                    const response =
                        await this.timedFetch(
                            this.sheetPrepareUrlTemplate
                                .replace(
                                    '__TRANSFORMATION__',
                                    item.id
                                ),
                            {
                                method:
                                    'POST',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    JSON.stringify({}),
                            }
                        );

                    const data =
                        await this.jsonResponse(
                            response
                        );

                    if (data?.transformation) {
                        const index =
                            this.transformations
                                .findIndex(
                                    transformation =>
                                        Number(
                                            transformation.id
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                );

                        if (index >= 0) {
                            this.transformations[index] = {
                                ...this.transformations[index],
                                ...data.transformation,
                            };
                        }
                    }

                    window.location.href =
                        data?.edit_url
                        || (
                            this.sheetEditBaseUrl
                            + '?transformation_sheet='
                            + item.id
                        );

                } catch (error) {
                    this.actionError =
                        error?.message
                        ?? 'Não foi possível preparar a Nova Ficha.';

                } finally {
                    this.preparingSheetId =
                        null;
                }
            },


            async activateTransformation(item, confirmedReplacement = false, fromSpell = false, durationOverride = null, durationRollMode = null) {
                if (
                    !item
                    || this.actingId !== null
                    || this.normalizing
                ) {
                    return;
                }

                if (
                    this.active
                    && Number(this.active.id)
                        !== Number(item.id)
                ) {
                    this.actionError =
                        'Volte à Forma Normal antes de assumir outra transformação.';
                    return;
                }

                if (
                    this.active
                    && Number(this.active.id)
                        === Number(item.id)
                ) {
                    return;
                }

                if (
                    item.type === 'monster_sheet'
                    && item.monster_supported === false
                ) {
                    this.actionError =
                        'Esta transformação usa uma criatura de formato externo. Escolha uma ficha criada pelo Construtor de NPC do Spellbound.';
                    return;
                }

                if (item.casting_time === 'spell' && !fromSpell) {
                    this.actionError = 'Conjure a magia vinculada pelo Grimório para ativar esta forma.';
                    return;
                }

                if (item.requires_concentration && item.casting_time !== 'spell' && !confirmedReplacement) {
                    this.concentrationConfirmLoading = true;
                    try {
                        const url = this.concentrationStateUrl
                            + (this.concentrationStateUrl.includes('?') ? '&' : '?')
                            + 'scope=concentration';
                        const response = await this.timedFetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const data = await this.jsonResponse(response);
                        if (data?.concentration?.active) {
                            this.concentrationConflictItem = item;
                            this.concentrationConflictCurrent = data.concentration;
                            return;
                        }
                    } catch (error) {
                        this.actionError = error?.message
                            || 'Não foi possível conferir a concentração. Tente novamente.';
                        return;
                    } finally {
                        this.concentrationConfirmLoading = false;
                    }
                }

                if (item.duration_mode === 'dice' && durationOverride === null) {
                    this.openDurationModal(item, confirmedReplacement, fromSpell);
                    return;
                }

                this.actingId =
                    Number(item.id);

                this.actionError =
                    null;

                try {
                    const url =
                        this.activateUrlTemplate
                            .replace(
                                '__TRANSFORMATION__',
                                item.id
                            );

                    const response =
                        await this.timedFetch(
                            url,
                            {
                                method:
                                    'POST',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    JSON.stringify({
                                        confirm_replace_concentration: !!confirmedReplacement,
                                        ...(durationOverride !== null ? {
                                            duration_total: durationOverride,
                                            duration_roll_mode: durationRollMode || 'manual',
                                        } : {}),
                                    }),
                            }
                        );

                    await this.jsonResponse(
                        response
                    );

                    this.open =
                        false;

                    window.location.assign(
                        this.sheetEditBaseUrl
                        || window.location.pathname
                    );

                } catch (error) {
                    console.error(
                        'Erro ao transformar:',
                        error
                    );

                    this.actionError =
                        error?.message
                        || 'Não foi possível ativar a transformação.';

                } finally {
                    this.actingId =
                        null;
                }
            },

            requestDelete(item) {
                if (
                    !item
                    || this.deletingId !== null
                    || this.actingId !== null
                    || this.normalizing
                ) {
                    return;
                }

                if (
                    this.active
                    && Number(this.active.id)
                        === Number(item.id)
                ) {
                    this.actionError =
                        'Volte à Forma Normal antes de excluir a transformação ativa.';
                    return;
                }

                this.deleteError =
                    null;

                this.deleteCandidate =
                    item;
            },

            cancelDelete() {
                if (
                    this.deletingId !== null
                ) {
                    return;
                }

                this.deleteCandidate =
                    null;

                this.deleteError =
                    null;
            },

            async confirmDelete() {
                const item =
                    this.deleteCandidate;

                if (
                    !item
                    || this.deletingId !== null
                ) {
                    return;
                }

                this.deletingId =
                    Number(
                        item.id
                    );

                this.deleteError =
                    null;

                try {
                    const url =
                        this.deleteUrlTemplate
                            .replace(
                                '__TRANSFORMATION__',
                                item.id
                            );

                    const response =
                        await fetch(
                            url,
                            {
                                method:
                                    'DELETE',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    JSON.stringify({}),
                            }
                        );

                    await this.jsonResponse(
                        response
                    );

                    window.location.reload();

                } catch (error) {
                    console.error(
                        'Erro ao excluir transformação:',
                        error
                    );

                    this.deleteError =
                        error?.message
                        || 'Não foi possível excluir a transformação.';

                } finally {
                    this.deletingId =
                        null;
                }
            },


            async returnToNormal() {
                if (
                    !this.active
                    || this.normalizing
                    || this.actingId !== null
                ) {
                    return;
                }

                this.normalizing =
                    true;

                this.actionError =
                    null;

                try {
                    const response =
                        await this.timedFetch(
                            this.deactivateUrl,
                            {
                                method:
                                    'POST',

                                headers: {
                                    'Accept':
                                        'application/json',

                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        this.csrfToken(),

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    JSON.stringify({}),
                            }
                        );

                    await this.jsonResponse(
                        response
                    );

                    this.open =
                        false;

                    window.location.assign(
                        this.sheetEditBaseUrl
                        || window.location.pathname
                    );

                } catch (error) {
                    console.error(
                        'Erro ao voltar à Forma Normal:',
                        error
                    );

                    this.actionError =
                        error?.message
                        || 'Não foi possível voltar à Forma Normal.';

                } finally {
                    this.normalizing =
                        false;
                }
            },
        }"

        data-transformations="{!! $encodedTransformations !!}"
        data-active="{!! $encodedActive !!}"
        data-npcs="{!! $encodedNpcs !!}"
        data-linked-spells="{!! $encodedLinkedSpells !!}"
        data-store-url="{{ route(
            'characters.transformations.store',
            $character
        ) }}"

        data-update-url-template="{{ url(
            '/characters/'
            . $character->id
            . '/transformations/__TRANSFORMATION__'
        ) }}"

        data-activate-url-template="{{ url(
            '/characters/'
            . $character->id
            . '/transformations/__TRANSFORMATION__/activate'
        ) }}"

        data-delete-url-template="{{ url(
            '/characters/'
            . $character->id
            . '/transformations/__TRANSFORMATION__'
        ) }}"

        data-sheet-prepare-url-template="{{ url(
            '/characters/'
            . $character->id
            . '/transformations/__TRANSFORMATION__/sheet'
        ) }}"

        data-sheet-edit-base-url="{{ route(
            'characters.show',
            $character
        ) }}"

        data-concentration-url="{{ route(
            'characters.transformations.concentration.check',
            $character
        ) }}"
        data-concentration-state-url="{{ route('characters.spellbook.state', $character) }}"

        data-dice-roll-url="{{ url('/api/roll') }}"

        data-deactivate-url="{{ route(
            'characters.transformations.deactivate',
            $character
        ) }}"

        @character-transformation-duration-changed.window="handleDurationUpdate($event.detail)"
        x-on:spellbound-spell-cast.window="handleLinkedSpellCast($event.detail)"
        @character-open-transformation-rounds.window="openRoundTracker()"

        @character-hp-changed.window="
            handleCharacterHpChanged(
                $event.detail
            )
        "

        @character-damage-applied.window="
            handleCharacterDamage(
                $event.detail
            )
        "

        @keydown.escape.window="
            if (durationModalOpen) {
                closeDurationModal()
            } else if (concentrationConflictItem) {
                cancelConcentrationReplacement()
            } else if (specialPrompt) {
                dismissSpecialPrompt()
            } else if (deleteCandidate) {
                cancelDelete()
            } else if (npcPickerOpen) {
                npcPickerOpen = false
            } else if (spellPickerOpen) {
                spellPickerOpen = false
            } else if (castTransformationPrompt) {
                castTransformationPrompt = null
            } else if (createOpen) {
                closeCreate()
            } else if (open) {
                closeSelector()
            }
        "

        class="
            character-transformations-v14
            mb-3
        "
    >
        <div class="character-transformations-v14-card">

            @if ($activeTransformation)
                <div class="character-transformations-v14-active-line"></div>
            @endif

            <button
                type="button"
                @click="openSelector()"
                class="character-transformations-v14-trigger"
            >
                <span class="character-transformations-v14-portrait">
                    @if ($imageUrl)
                        <img
                            src="{{ $imageUrl }}"
                            alt=""
                        >
                    @else
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M7.5 7.5A6.36 6.36 0 0 1 12 5.64c2.06 0 3.9.98 5.07 2.5M16.5 16.5A6.36 6.36 0 0 1 12 18.36a6.36 6.36 0 0 1-5.07-2.5M17.07 8.14V4.5m0 3.64h-3.64M6.93 15.86v3.64m0-3.64h3.64"
                            />
                        </svg>
                    @endif
                </span>

                <span class="character-transformations-v14-copy">
                    <span class="character-transformations-v14-kicker">
                        Transformação
                    </span>

                    <span class="character-transformations-v14-name">
                        {{ $activeTransformation?->name
                            ?? 'Forma Normal'
                        }}
                    </span>

                    <span class="character-transformations-v14-meta">
                        @if ($activeTransformation)
                            <span>
                                {{ $typeLabels[
                                    $activeTransformation->type
                                ] ?? 'Transformação' }}
                            </span>

                            @if (
                                $castingLabels[
                                    $activeTransformation->casting_time
                                    ?? 'none'
                                ] ?? null
                            )
                                <span>·</span>

                                <span>
                                    {{ $castingLabels[
                                        $activeTransformation->casting_time
                                    ] }}
                                </span>
                            @endif

                            @if ($activeTransformation->requires_concentration)
                                <span>·</span>
                                <span>Concentração</span>
                            @endif

                            @if ($activeState?->duration_remaining !== null)
                                <span>·</span>
                                <span x-text="activeDurationRemaining === 1
                                    ? '1 rodada restante'
                                    : `${activeDurationRemaining} rodadas restantes`">
                                </span>
                            @endif
                        @else
                            <span>
                                {{ $transformations->count() }}
                                {{ $transformations->count() === 1
                                    ? 'forma disponível'
                                    : 'formas disponíveis'
                                }}
                            </span>
                        @endif
                    </span>
                </span>

                <span class="character-transformations-v14-chevron">
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.7"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </span>
            </button>

            @if (
                $activeTransformation
                && $activeTransformation->type === 'character_sheet'
            )
                <button
                    type="button"
                    @click.stop="
                        returnToNormal()
                    "
                    :disabled="
                        normalizing
                        || actingId !== null
                    "
                    class="
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        border-t
                        border-[#93623d]/20
                        bg-[#eadbc8]/45
                        px-3
                        py-2
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.09em]
                        text-[#6b1d14]
                        transition
                        hover:bg-[#eadbc8]/80
                        disabled:cursor-wait
                        disabled:opacity-50
                    "
                >
                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M3 12a9 9 0 1 0 3-6.7" />
                        <path d="M3 4v6h6" />
                    </svg>

                    <span x-show="!normalizing">
                        Destransformar
                    </span>

                    <span
                        x-show="normalizing"
                        x-cloak
                    >
                        Voltando...
                    </span>
                </button>
            @endif
        </div>


        {{-- Confirmação de troca, antes de gastar usos ou encerrar concentração. --}}
        <template x-teleport="body">
            <div x-show="concentrationConflictItem" x-cloak
                class="character-sheet-theme fixed inset-0 z-[350] flex items-center justify-center p-4"
                data-theme="{{ $characterTheme }}"
                style="background: var(--theme-modal-backdrop, rgba(0,0,0,.78))"
                @click.self="cancelConcentrationReplacement()">
                <section role="alertdialog" aria-modal="true" aria-label="Substituir concentração"
                    class="w-full max-w-md rounded-2xl border p-5 shadow-2xl"
                    style="background: var(--theme-modal-surface, var(--theme-surface-card)); color: var(--theme-text-strong); border-color: var(--theme-border)">
                    <p class="text-[9px] font-black uppercase tracking-wide" style="color:var(--theme-label)">Concentração ativa</p>
                    <h3 class="mt-1 font-serif text-xl font-black">Substituir concentração?</h3>
                    <p class="mt-3 text-sm leading-relaxed" style="color:var(--theme-text-secondary)">
                        Você está concentrado em
                        <strong x-text="concentrationConflictCurrent?.effect_name || concentrationConflictCurrent?.spell_name || 'outro efeito'"></strong>.
                        Ativar <strong x-text="concentrationConflictItem?.name"></strong> encerrará essa concentração.
                        <span x-show="concentrationConflictCurrent?.source === 'transformation'">A forma anterior será encerrada.</span>
                    </p>
                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" @click="cancelConcentrationReplacement()"
                            class="rounded-lg border px-4 py-2 text-sm" style="border-color:var(--theme-border)">Cancelar</button>
                        <button type="button" @click="confirmConcentrationReplacement()"
                            class="rounded-lg px-4 py-2 text-sm font-bold"
                            style="background:var(--theme-button-background, var(--theme-accent-strong)); color:var(--theme-on-accent, white)">
                            Encerrar e transformar
                        </button>
                    </div>
                </section>
            </div>
        </template>

        {{-- ================================================================
             ALERTA — CONCENTRAÇÃO DA NOVA FICHA
        ================================================================= --}}

        <template x-teleport="body">
            <div
                x-show="
                    concentrationCheck
                    || concentrationError
                "
                x-cloak
                class="
                    character-sheet-theme
                    character-transformations-v14-concentration
                    fixed
                    right-4
                    top-4
                    z-[340]
                    w-[min(92vw,410px)]
                    overflow-hidden
                    rounded-2xl
                    border
                    border-[#b98e63]/60
                    bg-[#fffdf8]
                    text-[#34231b]
                    shadow-[0_24px_70px_rgba(0,0,0,.30)]
                "
                role="alertdialog"
                aria-live="assertive"
                aria-label="Teste de concentração"
                @keydown.escape.window="
                    if (concentrationCheck) {
                        closeConcentrationCheck()
                    }
                "
            
                data-theme="{{ $characterTheme }}">
                <header
                    class="
                        flex
                        items-start
                        justify-between
                        gap-3
                        border-b
                        border-[#93623d]/20
                        bg-[#eadbc8]
                        px-4
                        py-3
                    "
                >
                    <div class="min-w-0">
                        <span
                            class="
                                block
                                text-[7px]
                                font-black
                                uppercase
                                tracking-[0.14em]
                                text-[#93623d]
                            "
                        >
                            Concentração
                        </span>

                        <h3
                            class="
                                mt-0.5
                                font-serif
                                text-[17px]
                                font-black
                                leading-none
                                text-[#53150f]
                            "
                        >
                            Salvamento de Constituição
                        </h3>
                    </div>

                    <button
                        type="button"
                        class="
                            flex
                            h-7
                            w-7
                            shrink-0
                            items-center
                            justify-center
                            rounded-lg
                            text-[18px]
                            leading-none
                            text-[#755847]
                            transition
                            hover:bg-[#fffdf8]/55
                            hover:text-[#53150f]
                            disabled:opacity-40
                        "
                        @click="
                            closeConcentrationCheck()
                        "
                        :disabled="
                            concentrationRolling
                        "
                        aria-label="Fechar"
                    >
                        ×
                    </button>
                </header>

                <div class="px-4 py-3">
                    <template x-if="concentrationCheck">
                        <div>
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    class="
                                        rounded-full
                                        border
                                        border-[#93623d]/20
                                        bg-[#f3e7d8]
                                        px-2
                                        py-1
                                        text-[8px]
                                        font-black
                                        text-[#704326]
                                    "
                                >
                                    CD
                                    <span
                                        class="ml-1"
                                        x-text="
                                            concentrationCheck?.dc
                                            ?? 10
                                        "
                                    ></span>
                                </span>

                                <span
                                    class="
                                        rounded-full
                                        border
                                        border-[#93623d]/20
                                        bg-[#f3e7d8]
                                        px-2
                                        py-1
                                        text-[8px]
                                        font-black
                                        text-[#704326]
                                    "
                                >
                                    Dano
                                    <span
                                        class="ml-1"
                                        x-text="
                                            concentrationCheck?.damage
                                            ?? 0
                                        "
                                    ></span>
                                </span>

                                <span
                                    class="
                                        rounded-full
                                        border
                                        border-[#93623d]/20
                                        bg-[#f3e7d8]
                                        px-2
                                        py-1
                                        text-[8px]
                                        font-black
                                        text-[#704326]
                                    "
                                >
                                    Mod
                                    <span
                                        class="ml-1"
                                        x-text="
                                            (() => {
                                                const value =
                                                    parseInt(
                                                        concentrationCheck?.modifier
                                                        ?? 0
                                                    )
                                                    || 0;

                                                return value >= 0
                                                    ? `+${value}`
                                                    : `${value}`;
                                            })()
                                        "
                                    ></span>
                                </span>

                                <template
                                    x-if="
                                        Number(
                                            concentrationCheck?.exhaustion_penalty
                                            ?? 0
                                        ) > 0
                                    "
                                >
                                    <span
                                        class="
                                            rounded-full
                                            border
                                            border-[#93623d]/20
                                            bg-[#f3e7d8]
                                            px-2
                                            py-1
                                            text-[8px]
                                            font-black
                                            text-[#704326]
                                        "
                                    >
                                        Exaustão
                                        <span
                                            class="ml-1"
                                            x-text="
                                                `-${concentrationCheck.exhaustion_penalty}`
                                            "
                                        ></span>
                                    </span>
                                </template>
                            </div>

                            <p
                                class="
                                    mt-3
                                    text-[10px]
                                    leading-relaxed
                                    text-[#674936]
                                "
                            >
                                O dano exige um teste para manter a concentração.
                                A falha é informada, mas não encerra a transformação automaticamente.
                            </p>

                            <div
                                class="
                                    mt-3
                                    flex
                                    flex-wrap
                                    items-center
                                    gap-3
                                "
                            >
                                <template x-if="!concentrationRoll">
                                    <button
                                        type="button"
                                        @click="
                                            rollConcentrationCheck()
                                        "
                                        :disabled="
                                            concentrationRolling
                                        "
                                        class="
                                            rounded-lg
                                            bg-[#6b1d14]
                                            px-3.5
                                            py-2
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.07em]
                                            text-[#fffaf2]
                                            transition
                                            hover:bg-[#53150f]
                                            disabled:cursor-wait
                                            disabled:opacity-50
                                        "
                                    >
                                        <span x-show="!concentrationRolling">
                                            Rolar Constituição
                                        </span>

                                        <span
                                            x-show="concentrationRolling"
                                            x-cloak
                                        >
                                            Rolando...
                                        </span>
                                    </button>
                                </template>

                                <template x-if="concentrationRoll">
                                    <div
                                        class="
                                            flex
                                            items-center
                                            gap-3
                                        "
                                    >
                                        <x-dice.polyhedral
                                            :sides="20"
                                            :size="42"
                                        >
                                            <span
                                                class="tabular-nums"
                                                x-text="
                                                    concentrationRoll?.die
                                                    ?? ''
                                                "
                                            ></span>
                                        </x-dice.polyhedral>

                                        <div>
                                            <span
                                                class="
                                                    block
                                                    text-[7px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.10em]
                                                    text-[#93623d]
                                                "
                                            >
                                                Resultado
                                            </span>

                                            <strong
                                                class="
                                                    mt-0.5
                                                    block
                                                    font-serif
                                                    text-[15px]
                                                    font-black
                                                "
                                                :class="
                                                    concentrationRoll?.passed
                                                        ? 'text-[#315d45]'
                                                        : 'text-[#8b2a20]'
                                                "
                                            >
                                                <span
                                                    x-text="
                                                        concentrationRoll?.total
                                                        ?? ''
                                                    "
                                                ></span>

                                                <span>—</span>

                                                <span
                                                    x-text="
                                                        concentrationRoll?.passed
                                                            ? 'Sucesso'
                                                            : 'Falha'
                                                    "
                                                ></span>
                                            </strong>
                                        </div>
                                    </div>
                                </template>

                                <button
                                    type="button"
                                    @click="
                                        closeConcentrationCheck()
                                    "
                                    :disabled="
                                        concentrationRolling
                                    "
                                    class="
                                        text-[8px]
                                        font-black
                                        uppercase
                                        tracking-[0.06em]
                                        text-[#755847]
                                        hover:text-[#53150f]
                                    "
                                >
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </template>

                    <p
                        x-show="
                            concentrationError
                            && !concentrationCheck
                        "
                        x-cloak
                        class="
                            text-[10px]
                            font-bold
                            leading-relaxed
                            text-[#8b2a20]
                        "
                        x-text="
                            concentrationError
                        "
                    ></p>

                    <p
                        x-show="
                            concentrationError
                            && concentrationCheck
                        "
                        x-cloak
                        class="
                            mt-2
                            text-[9px]
                            font-bold
                            text-[#8b2a20]
                        "
                        x-text="
                            concentrationError
                        "
                    ></p>
                </div>
            </div>
        </template>


        {{-- ================================================================
             MODAL — TRANSFORMAÇÃO ESPECIAL
        ================================================================= --}}

        <template x-teleport="body">
            <div
                x-show="
                    specialPrompt
                    && specialPromptCandidates.length > 0
                "
                x-cloak
                class="
                    character-sheet-theme
                    fixed
                    inset-0
                    z-[260]
                    flex
                    items-center
                    justify-center
                    p-4
                "
                role="dialog"
                aria-modal="true"
                aria-label="Transformação Especial"
            
                data-theme="{{ $characterTheme }}">
                <div
                    class="
                        character-transformations-v14-backdrop
                        absolute
                        inset-0
                    "
                    @click="
                        dismissSpecialPrompt()
                    "
                ></div>


                <div
                    class="
                        character-transformations-v14-special-modal
                        relative
                        z-10
                    "
                    @click.stop
                    x-transition.opacity.duration.140ms
                >

                    <header class="character-transformations-v14-special-header">

                        <div class="min-w-0">
                            <span class="character-transformations-v14-special-eyebrow">
                                Gatilho do personagem
                            </span>

                            <h3 class="character-transformations-v14-special-title">
                                Transformação Especial
                            </h3>
                        </div>


                        <button
                            type="button"
                            class="character-transformations-v14-special-close"
                            @click="
                                dismissSpecialPrompt()
                            "
                            :disabled="
                                actingId !== null
                            "
                            title="Agora não"
                            aria-label="Fechar"
                        >
                            ×
                        </button>

                    </header>


                    <div class="character-transformations-v14-special-body">

                        <p class="character-transformations-v14-special-copy">
                            O personagem atingiu uma condição configurada para uma forma especial.
                            Deseja se transformar agora?
                        </p>


                        <span class="character-transformations-v14-special-life">
                            Pontos de Vida:
                            <span
                                class="ml-1"
                                x-text="
                                    specialPrompt
                                        ?.currentHp
                                    ?? 0
                                "
                            ></span>
                            /
                            <span
                                x-text="
                                    specialPrompt
                                        ?.maxHp
                                    ?? 1
                                "
                            ></span>
                        </span>


                        <div class="character-transformations-v14-special-list">

                            <template
                                x-for="
                                    item
                                    in specialPromptCandidates
                                "
                                :key="
                                    `special-${item.id}`
                                "
                            >
                                <article class="character-transformations-v14-special-option">

                                    <div class="character-transformations-v14-special-image">

                                        <svg
                                            class="h-6 w-6"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M7.5 7.5A6.36 6.36 0 0 1 12 5.64c2.06 0 3.9.98 5.07 2.5M16.5 16.5A6.36 6.36 0 0 1 12 18.36a6.36 6.36 0 0 1-5.07-2.5M17.07 8.14V4.5m0 3.64h-3.64M6.93 15.86v3.64m0-3.64h3.64"
                                            />
                                        </svg>

                                        <template x-if="item.image_url">
                                            <img
                                                :src="item.image_url"
                                                :alt="`Retrato de ${item.name}`"
                                                x-on:error="
                                                    $el.remove()
                                                "
                                            >
                                        </template>

                                    </div>


                                    <div class="min-w-0">

                                        <h4
                                            class="character-transformations-v14-special-name"
                                            x-text="
                                                item.name
                                            "
                                        ></h4>

                                        <span
                                            class="character-transformations-v14-special-trigger"
                                            x-text="
                                                specialTriggerLabel(
                                                    item
                                                )
                                            "
                                        ></span>

                                    </div>


                                    <button
                                        type="button"
                                        class="character-transformations-v14-special-action"
                                        @click="
                                            confirmSpecialTransformation(
                                                item
                                            )
                                        "
                                        :disabled="
                                            actingId !== null
                                        "
                                    >
                                        <span
                                            x-show="
                                                Number(actingId)
                                                    !== Number(item.id)
                                            "
                                        >
                                            Transformar
                                        </span>

                                        <span
                                            x-show="
                                                Number(actingId)
                                                    === Number(item.id)
                                            "
                                            x-cloak
                                        >
                                            Transformando...
                                        </span>
                                    </button>

                                </article>
                            </template>

                        </div>


                        <div
                            x-show="actionError"
                            x-cloak
                            class="
                                character-transformations-v14-error
                                mt-3
                            "
                            x-text="
                                actionError
                            "
                        ></div>

                    </div>


                    <footer class="character-transformations-v14-special-footer">

                        <button
                            type="button"
                            class="character-transformations-v14-special-decline"
                            @click="
                                dismissSpecialPrompt()
                            "
                            :disabled="
                                actingId !== null
                            "
                        >
                            Agora não
                        </button>

                    </footer>

                </div>
            </div>
        </template>


        <template x-teleport="body">
            <div
                x-show="open"
                x-cloak
                class="
                    character-sheet-theme
                    fixed
                    inset-0
                    z-[185]
                    flex
                    items-center
                    justify-center
                    p-3
                    sm:p-6
                "
                role="dialog"
                aria-modal="true"
                aria-label="Transformações do personagem"
            
                data-theme="{{ $characterTheme }}">
                <div
                    class="
                        character-transformations-v14-backdrop
                        absolute
                        inset-0
                    "
                    @click="closeSelector()"
                ></div>

                <div
                    x-show="open"
                    @click.stop
                    x-transition:enter="
                        transition
                        ease-out
                        duration-150
                    "
                    x-transition:enter-start="
                        opacity-0
                        translate-y-1
                        scale-[.99]
                    "
                    x-transition:enter-end="
                        opacity-100
                        translate-y-0
                        scale-100
                    "
                    x-transition:leave="
                        transition
                        ease-in
                        duration-100
                    "
                    x-transition:leave-start="
                        opacity-100
                        translate-y-0
                        scale-100
                    "
                    x-transition:leave-end="
                        opacity-0
                        translate-y-1
                        scale-[.99]
                    "
                    class="
                        character-transformations-v14-modal
                        relative
                        z-10
                    "
                >
                    <header
                        class="
                            flex
                            items-start
                            justify-between
                            gap-4
                            border-b
                            border-[#a0774d]/30
                            bg-[#eadbc8]
                            px-4
                            py-4
                        "
                    >
                        <div class="min-w-0">
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]
                                    text-[#8c6239]
                                "
                            >
                                Formas do personagem
                            </p>

                            <h2
                                class="
                                    mt-1
                                    font-serif
                                    text-[24px]
                                    font-black
                                    leading-none
                                    text-[#53150f]
                                "
                            >
                                Transformações
                            </h2>

                            <p
                                class="
                                    mt-1.5
                                    text-[11px]
                                    leading-relaxed
                                    text-[#6f4c36]
                                "
                            >
                                Escolha a forma que deverá assumir a ficha mecânica.
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <button
                                type="button"
                                @click="openCreate()"
                                class="character-transformations-v14-create-button"
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    aria-hidden="true"
                                >
                                    <path d="M12 5v14M5 12h14" />
                                </svg>

                                Nova transformação
                            </button>

                        <button
                            type="button"
                            @click="closeSelector()"
                            class="
                                flex
                                h-8
                                w-8
                                shrink-0
                                items-center
                                justify-center
                                rounded-lg
                                text-[#8c6239]
                                transition
                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]
                            "
                            title="Fechar"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M6 6l12 12M18 6 6 18"
                                />
                            </svg>
                        </button>
                        </div>

                    </header>


                    <div class="character-transformations-v14-list ct-v53-list">

                        {{-- A ficha original já é a Forma Normal. O retorno aparece somente quando transformado. --}}
                        <template x-if="active">
                            <div class="ct-form-active-strip">
                                <div class="min-w-0">
                                    <strong x-text="active?.name || 'Transformação ativa'"></strong>
                                    <small>Forma ativa · Volte à ficha original quando desejar.</small>
                                </div>
                                <div class="ct-form-active-actions">
                                    <button type="button" @click="openRoundTracker()" class="ct-form-track-btn">Rodadas</button>
                                    <button type="button" @click="returnToNormal()" :disabled="normalizing || actingId !== null"
                                        class="ct-form-return-btn" x-text="normalizing ? 'Voltando...' : 'Encerrar forma'"></button>
                                </div>
                            </div>
                        </template>

                        {{-- FORMAS CADASTRADAS --}}
                        <template
                            x-for="
                                item in transformations
                            "
                            :key="
                                item.id
                            "
                        >
                            <article
                                class="ct-v53-card"
                                :class="{'is-active': active && Number(active.id) === Number(item.id)}"
                            >
                                <div class="ct-v53-portrait">
                                    <template x-if="item.image_url">
                                        <img :src="item.image_url" :alt="'Retrato de ' + item.name" loading="lazy"
                                            x-on:error="$el.style.display='none'">
                                    </template>
                                    <template x-if="!item.image_url">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                            <path d="M12 3 4 7v10l8 4 8-4V7l-8-4ZM4 7l8 5 8-5M12 12v9"/>
                                        </svg>
                                    </template>
                                    <span x-show="active && Number(active.id) === Number(item.id)"
                                        x-cloak class="ct-v53-current-dot" aria-label="Forma ativa">✓</span>
                                </div>

                                <div class="ct-v53-content">
                                    <h3 class="ct-v53-name" x-text="item.name"></h3>
                                    <span class="ct-v53-kind" x-text="typeLabel(item)"></span>
                                    <div class="ct-v53-meta">
                                        <span class="ct-v53-tag is-magic" x-show="item.casting_time === 'spell'" x-cloak
                                            x-text="'✧ ' + linkedSpellName(item)"></span>
                                        <span class="ct-v53-tag" x-show="item.casting_time !== 'spell'" x-cloak>Ativação manual</span>
                                        <span class="ct-v53-tag" x-show="item.requires_concentration" x-cloak>Concentração</span>
                                        <span class="ct-v53-tag" x-show="item.duration_label && item.casting_time !== 'spell'" x-cloak
                                            x-text="item.duration_label"></span>
                                        <span class="ct-v53-tag" x-show="item.uses_max !== null && item.uses_max !== undefined" x-cloak
                                            x-text="'Usos ' + (item.uses_current ?? item.uses_max) + '/' + item.uses_max"></span>
                                        <span class="ct-v53-tag" x-show="item.type === 'monster_sheet' && item.monster_supported === false" x-cloak>
                                            Formato externo
                                        </span>
                                        <span class="ct-v53-tag is-active"
                                            x-show="active && Number(active.id) === Number(item.id)" x-cloak>Ativa</span>
                                    </div>
                                </div>

                                <div class="ct-v53-actions">
                                    <template x-if="active && Number(active.id) === Number(item.id)">
                                        <span class="ct-form-current">Em uso</span>
                                    </template>
                                    <template x-if="(!active || Number(active.id) !== Number(item.id)) && item.casting_time !== 'spell'">
                                        <button type="button"
                                            class="character-transformations-v14-action transform ct-form-primary"
                                            x-on:click="activateTransformation(item)"
                                            :disabled="actingId !== null || normalizing
                                                || (active && Number(active.id) !== Number(item.id))
                                                || (item.uses_max !== null && item.uses_max !== undefined && Number(item.uses_current) < 1)
                                                || (item.type === 'monster_sheet' && item.monster_supported === false)"
                                            x-text="Number(actingId) === Number(item.id) ? 'Transformando...' : 'Transformar'">
                                        </button>
                                    </template>
                                    <template x-if="(!active || Number(active.id) !== Number(item.id)) && item.casting_time === 'spell'">
                                        <span class="ct-v53-magic-hint">Conjure pelo Grimório</span>
                                    </template>
                                    <div class="ct-form-more"
                                        x-on:click.outside="if (optionMenuId === Number(item.id)) optionMenuId = null">
                                        <button type="button" class="ct-form-more-toggle"
                                            x-on:click="optionMenuId = optionMenuId === Number(item.id) ? null : Number(item.id)"
                                            :aria-expanded="optionMenuId === Number(item.id)"
                                            :aria-label="'Opções de ' + item.name" title="Mais opções">⋯</button>
                                        <div class="ct-form-more-options" x-show="optionMenuId === Number(item.id)" x-cloak>
                                            <template x-if="item.type === 'character_sheet'">
                                                <button type="button" x-on:click="optionMenuId = null; openCharacterSheet(item)"
                                                    :disabled="saving || actingId !== null || preparingSheetId !== null">
                                                    Editar ficha mecânica
                                                </button>
                                            </template>
                                            <button type="button" x-on:click="optionMenuId = null; openEdit(item)"
                                                :disabled="saving || actingId !== null || normalizing">Editar configuração</button>
                                            <button type="button" class="ct-form-danger"
                                                x-on:click="optionMenuId = null; requestDelete(item)"
                                                :disabled="saving || actingId !== null || normalizing || deletingId !== null
                                                    || (active && Number(active.id) === Number(item.id))">Excluir forma</button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>


                        <div
                            x-show="actionError"
                            x-cloak
                            class="
                                character-transformations-v14-error
                                mx-4
                                mb-3
                            "
                            x-text="actionError"
                        ></div>


                        <div
                            x-show="
                                transformations.length === 0
                            "
                            x-cloak
                            class="
                                px-5
                                py-8
                                text-center
                            "
                        >
                            <p
                                class="
                                    font-serif
                                    text-[17px]
                                    font-black
                                    text-[#53150f]
                                "
                            >
                                Nenhuma transformação cadastrada
                            </p>

                            <p
                                class="
                                    mx-auto
                                    mt-2
                                    max-w-sm
                                    text-[10px]
                                    leading-relaxed
                                    text-[#755847]
                                "
                            >
                                O módulo já está ativo para este personagem.
                                As formas criadas aparecerão aqui.
                            </p>


                            <button
                                type="button"
                                @click="openCreate()"
                                class="
                                    character-transformations-v14-create-button
                                    mt-4
                                "
                            >
                                <svg
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    aria-hidden="true"
                                >
                                    <path d="M12 5v14M5 12h14" />
                                </svg>

                                Criar primeira transformação
                            </button>
                        </div>

                    </div>


                    <footer
                        class="
                            border-t
                            border-[#a0774d]/24
                            bg-[#fffdf8]
                            px-4
                            py-3
                            text-[9px]
                            font-bold
                            leading-relaxed
                            text-[#755847]
                        "
                    >
                        Inventário, Diário, Party e Feitiços continuam pertencendo
                        ao personagem original. A transformação troca somente a
                        ficha mecânica.
                    </footer>
                </div>
            </div>
        </template>


        {{-- ================================================================
             CONFIRMAÇÃO — EXCLUIR TRANSFORMAÇÃO
        ================================================================= --}}

        <template x-teleport="body">
            <div
                x-show="deleteCandidate"
                x-cloak
                class="
                    character-sheet-theme
                    fixed
                    inset-0
                    z-[200]
                    flex
                    items-center
                    justify-center
                    p-4
                "
                role="dialog"
                aria-modal="true"
                aria-label="Excluir transformação"
            
                data-theme="{{ $characterTheme }}">
                <div
                    class="
                        character-transformations-v14-backdrop
                        absolute
                        inset-0
                    "
                    @click="cancelDelete()"
                ></div>

                <div
                    class="
                        character-transformations-v14-delete-modal
                        relative
                        z-10
                    "
                    @click.stop
                    x-transition.opacity.duration.120ms
                >
                    <div
                        class="
                            flex
                            items-start
                            gap-3
                            px-4
                            py-4
                        "
                    >
                        <span class="character-transformations-v14-delete-icon">
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M4 7h16" />
                                <path d="M9 7V4h6v3" />
                                <path d="m7 7 1 13h8l1-13" />
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.14em]
                                    text-[#93623d]
                                "
                            >
                                Excluir forma
                            </p>

                            <h3
                                class="
                                    mt-1
                                    font-serif
                                    text-[18px]
                                    font-black
                                    text-[#53150f]
                                "
                                x-text="
                                    deleteCandidate
                                        ? `Excluir ${deleteCandidate.name}?`
                                        : 'Excluir transformação?'
                                "
                            ></h3>

                            <p
                                class="
                                    mt-2
                                    text-[10px]
                                    leading-relaxed
                                    text-[#755847]
                                "
                            >
                                A definição da transformação e sua foto serão removidas.
                                Essa ação não pode ser desfeita.
                            </p>

                            <div
                                x-show="deleteError"
                                x-cloak
                                class="character-transformations-v14-error"
                                x-text="deleteError"
                            ></div>
                        </div>
                    </div>

                    <footer
                        class="
                            flex
                            items-center
                            justify-end
                            gap-2
                            border-t
                            border-[#a0774d]/22
                            bg-[#fbf8f1]
                            px-4
                            py-3
                        "
                    >
                        <button
                            type="button"
                            @click="cancelDelete()"
                            :disabled="deletingId !== null"
                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.06em]
                                text-[#755847]
                                hover:bg-[#f3e7d8]
                                disabled:opacity-40
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="confirmDelete()"
                            :disabled="deletingId !== null"
                            class="
                                rounded-lg
                                border
                                border-[#7f261d]/25
                                bg-[#7f261d]
                                px-3
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.06em]
                                text-[#fffaf2]
                                transition
                                hover:bg-[#6b1d14]
                                disabled:opacity-40
                            "
                        >
                            <span
                                x-show="deletingId === null"
                            >
                                Excluir
                            </span>

                            <span
                                x-show="deletingId !== null"
                                x-cloak
                            >
                                Excluindo...
                            </span>
                        </button>
                    </footer>
                </div>
            </div>
        </template>


        {{-- ================================================================
             MODAL — CRIAR TRANSFORMAÇÃO
        ================================================================= --}}

        <template x-teleport="body">
            <div
                x-show="createOpen"
                x-cloak
                class="
                    character-sheet-theme
                    fixed
                    inset-0
                    z-[205]
                    flex
                    items-center
                    justify-center
                    p-3
                    sm:p-6
                "
                role="dialog"
                aria-modal="true"
                aria-label="Criar transformação"
            
                data-theme="{{ $characterTheme }}">
                <div
                    class="
                        character-transformations-v14-backdrop
                        absolute
                        inset-0
                    "
                    @click="closeCreate()"
                ></div>

                <div
                    x-show="createOpen"
                    @click.stop
                    x-transition:enter="
                        transition
                        ease-out
                        duration-150
                    "
                    x-transition:enter-start="
                        opacity-0
                        translate-y-1
                        scale-[.99]
                    "
                    x-transition:enter-end="
                        opacity-100
                        translate-y-0
                        scale-100
                    "
                    x-transition:leave="
                        transition
                        ease-in
                        duration-100
                    "
                    x-transition:leave-start="
                        opacity-100
                        translate-y-0
                        scale-100
                    "
                    x-transition:leave-end="
                        opacity-0
                        translate-y-1
                        scale-[.99]
                    "
                    class="
                        character-transformations-v14-create-modal
                        relative
                        z-10
                    "
                >
                    <header
                        class="
                            character-transformations-v14-create-header
                            flex
                            items-start
                            justify-between
                            gap-4
                            border-b
                            border-[#a0774d]/30
                            bg-[#eadbc8]
                            px-4
                            py-4
                            sm:px-5
                        "
                    >
                        <div class="min-w-0">
                            <p
                                class="
                                    text-[9px]
                                    font-black
                                    uppercase
                                    tracking-[0.18em]
                                    text-[#8c6239]
                                "
                            >
                                <span x-text="modalEyebrow"></span>
                            </p>

                            <h2
                                class="
                                    mt-1
                                    font-serif
                                    text-[24px]
                                    font-black
                                    leading-none
                                    text-[#53150f]
                                "
                            >
                                <span x-text="modalTitle"></span>
                            </h2>


                        </div>

                        <button
                            type="button"
                            @click="closeCreate()"
                            :disabled="saving"
                            class="
                                flex
                                h-8
                                w-8
                                shrink-0
                                items-center
                                justify-center
                                rounded-lg
                                text-[#8c6239]
                                transition
                                hover:bg-[#fffdf8]/55
                                hover:text-[#53150f]
                                disabled:opacity-40
                            "
                            title="Fechar"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M6 6l12 12M18 6 6 18"
                                />
                            </svg>
                        </button>
                    </header>


                    <div class="character-transformations-v14-create-body">

                        {{-- V52: fluxo da criação — origem, identidade, ativação. --}}
                        <section class="ct-v51-setup">
                            <div class="ct-v52-step-head">
                                <span class="ct-v52-step-title">Escolha o formato</span>
                            </div>

                            <div class="ct-v51-switch ct-v52-type-switch ct-v54-type-row" role="group" aria-label="Formato da transformação">
                                <div class="ct-v54-type-option">
                                    <button type="button" x-on:click="setType('character_sheet')"
                                        :aria-pressed="form.type === 'character_sheet'" :class="{'is-active':form.type === 'character_sheet'}">
                                        <span class="ct-v52-type-symbol" aria-hidden="true">▤</span>
                                        <span>Nova ficha</span>
                                    </button>
                                    <div class="ct-v55-help-wrap">
                                        <button type="button" class="ct-v55-help" aria-label="Como funciona a Nova ficha?" aria-describedby="ct-v55-help-sheet">i</button>
                                        <div class="ct-v55-help-popup" id="ct-v55-help-sheet" role="tooltip">
                                            <strong>Nova ficha</strong>
                                            Uma ficha alternativa personalizável para atributos, vida e habilidades. Seu personagem, inventário e Grimório continuam os mesmos.
                                        </div>
                                    </div>
                                </div>
                                <div class="ct-v54-type-option">
                                    <button type="button" x-on:click="setType('monster_sheet')"
                                        :aria-pressed="form.type === 'monster_sheet'" :class="{'is-active':form.type === 'monster_sheet'}">
                                        <span class="ct-v52-type-symbol" aria-hidden="true">♜</span>
                                        <span>Criatura do Cofre</span>
                                    </button>
                                    <div class="ct-v55-help-wrap">
                                        <button type="button" class="ct-v55-help" aria-label="Como funciona a Ficha de Monstro?" aria-describedby="ct-v55-help-monster">i</button>
                                        <div class="ct-v55-help-popup" id="ct-v55-help-monster" role="tooltip">
                                            <strong>Criatura do Cofre</strong>
                                            Para Polymorph, True Polymorph, Forma Selvagem e itens mágicos que concedem a ficha de uma criatura.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ct-v51-npc-selection" x-show="form.type === 'monster_sheet'" x-cloak>
                                <div class="ct-v52-npc-icon" aria-hidden="true">♜</div>
                                <div class="min-w-0 ct-v52-npc-copy">
                                    <strong x-text="selectedNpc?.name || 'Nenhuma criatura selecionada'"></strong>
                                    <small x-text="selectedNpc ? (selectedNpc.format_label + (selectedNpc.challenge_rating != null ? ' · ND ' + selectedNpc.challenge_rating : '')) : 'Escolha uma criatura do seu Cofre'"></small>
                                </div>
                                <button type="button" x-on:click.stop="openNpcPicker()" x-text="selectedNpc ? 'Trocar' : 'Escolher'"></button>
                            </div>

                            <div class="ct-v52-step-head ct-v52-identity-head">
                                <span class="ct-v52-step-title">Identidade</span>
                            </div>
                            <div class="ct-v51-identity">
                                <label class="min-w-0">
                                    <span class="ct-v51-label">Nome da forma</span>
                                    <input type="text" maxlength="120" x-model="form.name"
                                        placeholder="Ex.: Forma Demoníaca"
                                        class="character-transformations-v14-input ct-v51-name">
                                </label>
                                <div class="ct-v51-image-wrap">
                                    <label class="ct-v51-image-select" title="Escolher imagem da forma">
                                        <template x-if="imagePreview || currentNpcImage()">
                                            <img :src="imagePreview || currentNpcImage()" alt="Imagem da forma">
                                        </template>
                                        <template x-if="!imagePreview && !currentNpcImage()">
                                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8" cy="9" r="1.5"/><path d="m4 17 5-5 3 3 3-3 5 5"/></svg>
                                        </template>
                                        <span class="ct-v51-photo-label">Foto</span>
                                        <input class="sr-only" type="file" accept="image/jpeg,image/png,image/webp"
                                            x-on:click="$event.target.value = null" x-on:change="handleImage($event)">
                                    </label>
                                    <button type="button" class="ct-v51-photo-remove" x-show="imagePreview" x-cloak
                                        x-on:click.stop="removeImage()" aria-label="Remover foto da forma" title="Remover foto">×</button>
                                </div>
                            </div>
                        </section>

                        {{-- V51: vínculo de feitiço com seletor dedicado --}}
                        <section class="ct-v51-activation">
                            <div class="ct-v52-step-head">
                                <h3 class="ct-v52-step-title">Ativação</h3>
                            </div>
                            <div class="ct-v51-switch" role="group" aria-label="Ativação da forma">
                                <button type="button" x-on:click="form.casting_time='none';form.linked_spell_id=null"
                                    :aria-pressed="form.casting_time !== 'spell'" :class="{'is-active': form.casting_time !== 'spell'}">Manual</button>
                                <button type="button" x-on:click="form.casting_time='spell';form.requires_concentration=true"
                                    :aria-pressed="form.casting_time === 'spell'" :class="{'is-active': form.casting_time === 'spell'}">Vincular feitiço</button>
                            </div>
                            <div x-show="form.casting_time === 'spell'" x-cloak>
                                <button type="button" class="ct-v51-linked-selection" x-on:click.stop="openSpellPicker()">
                                    <span class="ct-v51-linked-icon ct-v55-linked-art" aria-hidden="true">
                                        <img x-show="selectedLinkedSpell?.image_url && !selectedLinkedSpell?._imageFailed" x-cloak
                                            :src="selectedLinkedSpell?.image_url || null" alt="" loading="lazy"
                                            x-on:error="if (selectedLinkedSpell) selectedLinkedSpell._imageFailed = true">
                                        <span x-show="!selectedLinkedSpell?.image_url || selectedLinkedSpell?._imageFailed" x-text="selectedLinkedSpell ? (Number(selectedLinkedSpell.level) === 0 ? 'T' : selectedLinkedSpell.level) : '✧'"></span>
                                    </span>
                                    <span class="ct-v51-linked-copy">
                                        <strong x-text="selectedLinkedSpell?.name || 'Escolher feitiço do Grimório'"></strong>
                                        <small x-text="selectedLinkedSpell ? (selectedLinkedSpell.level === 0 ? 'Truque' : selectedLinkedSpell.level + 'º círculo') + ' · Concentração' : 'Selecione a magia que ativa esta forma'"></small>
                                    </span>
                                    <span class="ct-v51-linked-arrow" aria-hidden="true">›</span>
                                </button>
                                <p class="ct-v51-helper" x-show="linkedSpells.length === 0">Nenhum feitiço de concentração encontrado no Grimório.</p>
                                <p class="ct-v51-helper" x-show="linkedSpells.length > 0">Ao conjurar, você poderá assumir esta forma. A concentração será a mesma da magia.</p>
                            </div>
                            <div class="ct-v54-duration">
                                <button x-show="form.casting_time !== 'spell'" x-cloak type="button" @click="form.requires_concentration = !form.requires_concentration"
                                    class="ct-v50-check" :aria-pressed="form.requires_concentration ? 'true' : 'false'">
                                    <span class="character-transformations-v14-switch" :class="{ 'is-active': form.requires_concentration }"></span>
                                    <span>Exige concentração</span>
                                </button>
                                <label class="ct-v50-duration-label">
                                    <span class="character-transformations-v14-label">Duração da forma</span>
                                    <select x-model="form.duration_type" class="character-transformations-v14-select">
                                        <option value="indefinite">Até encerrar</option>
                                        <option value="rounds">Rodadas fixas</option>
                                        <option value="minutes">Minutos fixos</option>
                                        <option value="hours">Horas fixas</option>
                                        <option value="dice">Por dados</option>
                                    </select>
                                </label>
                                <label x-show="!['indefinite','dice'].includes(form.duration_type)" x-cloak class="ct-v50-duration-label">
                                    <span class="character-transformations-v14-label">Quantidade</span>
                                    <input type="number" min="1" max="1000000" x-model.number="form.duration_value" class="character-transformations-v14-input">
                                </label>
                                <label x-show="form.duration_type === 'dice'" x-cloak class="ct-v50-duration-label">
                                    <span class="character-transformations-v14-label">Fórmula · em rodadas</span>
                                    <input type="text" maxlength="14" spellcheck="false" placeholder="2d6+1"
                                        x-model="form.duration_formula" class="character-transformations-v14-input" autocomplete="off">
                                </label>
                                <small x-show="form.duration_type === 'dice'" x-cloak class="ct-v54-duration-hint">Ao transformar, role os dados na tela ou informe o resultado obtido na mesa.</small>
                            </div>
                        </section>

                        <details class="ct-v50-advanced character-transformations-v14-section">
                            <summary>Regras opcionais <span>Gatilhos, usos e exaustão</span></summary>
                        {{-- ESPECIAL --}}
                        <section class="character-transformations-v14-section">
                            <button
                                type="button"
                                @click="
                                    form.is_special =
                                        !form.is_special
                                "
                                class="character-transformations-v14-toggle-row w-full"
                            >
                                <span class="min-w-0 text-left">
                                    <span class="block font-serif text-[12px] font-black text-[#53150f]">
                                        Transformação Especial
                                    </span>

                                    <span class="mt-1 block text-[9px] leading-relaxed text-[#755847]">
                                        Permite que a forma seja disparada automaticamente por um momento específico.
                                    </span>
                                </span>

                                <span
                                    class="character-transformations-v14-switch"
                                    :class="{
                                        'is-active':
                                            form.is_special
                                    }"
                                ></span>
                            </button>

                            <div
                                x-show="form.is_special"
                                x-cloak
                                class="mt-3"
                            >
                                <span class="character-transformations-v14-label">
                                    Quando acontece?
                                </span>

                                <div class="character-transformations-v14-segmented cols-2">
                                    <button
                                        type="button"
                                        @click="form.special_trigger = 'half_hp'"
                                        :class="{
                                            'is-active':
                                                form.special_trigger === 'half_hp'
                                        }"
                                    >
                                        Metade dos PV
                                    </button>

                                    <button
                                        type="button"
                                        @click="form.special_trigger = 'one_or_zero_hp'"
                                        :class="{
                                            'is-active':
                                                form.special_trigger === 'one_or_zero_hp'
                                        }"
                                    >
                                        1 ou 0 PV
                                    </button>
                                </div>
                            </div>
                        </section>


                        {{-- USOS --}}
                        <section class="character-transformations-v14-section">
                            <button
                                type="button"
                                @click="
                                    form.has_limited_uses =
                                        !form.has_limited_uses
                                "
                                class="character-transformations-v14-toggle-row w-full"
                            >
                                <span class="min-w-0 text-left">
                                    <span class="block font-serif text-[12px] font-black text-[#53150f]">
                                        Limite de usos
                                    </span>

                                    <span class="mt-1 block text-[9px] text-[#755847]">
                                        Desativado significa usos ilimitados.
                                    </span>
                                </span>

                                <span
                                    class="character-transformations-v14-switch"
                                    :class="{
                                        'is-active':
                                            form.has_limited_uses
                                    }"
                                ></span>
                            </button>

                            <div
                                x-show="form.has_limited_uses"
                                x-cloak
                                class="character-transformations-v14-grid-2 mt-3"
                            >
                                <label>
                                    <span class="character-transformations-v14-label">
                                        Quantos usos?
                                    </span>

                                    <input
                                        type="number"
                                        min="1"
                                        max="999"
                                        x-model.number="form.uses_max"
                                        class="character-transformations-v14-input"
                                    >
                                </label>

                                <label>
                                    <span class="character-transformations-v14-label">
                                        Recuperação
                                    </span>

                                    <select
                                        x-model="form.recovery"
                                        class="character-transformations-v14-select"
                                    >
                                        <option value="daily">
                                            Por dia
                                        </option>

                                        <option value="short_rest">
                                            Descanso Curto
                                        </option>
                                    </select>
                                </label>
                            </div>
                        </section>


                        {{-- EXAUSTÃO --}}
                        <section class="character-transformations-v14-section">
                            <button
                                type="button"
                                @click="
                                    form.applies_exhaustion =
                                        !form.applies_exhaustion
                                "
                                class="character-transformations-v14-toggle-row w-full"
                            >
                                <span class="min-w-0 text-left">
                                    <span class="block font-serif text-[12px] font-black text-[#53150f]">
                                        Efeito punitivo — Exaustão
                                    </span>

                                    <span class="mt-1 block text-[9px] text-[#755847]">
                                        Aplica níveis de Exaustão por utilizar esta forma.
                                    </span>
                                </span>

                                <span
                                    class="character-transformations-v14-switch"
                                    :class="{
                                        'is-active':
                                            form.applies_exhaustion
                                    }"
                                ></span>
                            </button>

                            <div
                                x-show="form.applies_exhaustion"
                                x-cloak
                                class="character-transformations-v14-grid-2 mt-3"
                            >
                                <label>
                                    <span class="character-transformations-v14-label">
                                        Níveis de Exaustão
                                    </span>

                                    <select
                                        x-model.number="form.exhaustion_levels"
                                        class="character-transformations-v14-select"
                                    >
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </label>

                                <label>
                                    <span class="character-transformations-v14-label">
                                        Quando aplicar?
                                    </span>

                                    <select
                                        x-model="form.exhaustion_timing"
                                        class="character-transformations-v14-select"
                                    >
                                        <option value="end">
                                            Ao voltar à forma normal
                                        </option>

                                        <option value="activation">
                                            Ao se transformar
                                        </option>
                                    </select>
                                </label>
                            </div>
                        </section>


                        </details>

                        <div
                            x-show="saveError"
                            x-cloak
                            class="character-transformations-v14-error"
                            x-text="saveError"
                        ></div>
                    </div>


                    <footer
                        class="
                            character-transformations-v14-create-footer
                            flex
                            items-center
                            justify-end
                            gap-2
                            border-t
                            border-[#a0774d]/24
                            bg-[#fffdf8]
                            px-4
                            py-3
                            sm:px-5
                        "
                    >
                        <button
                            type="button"
                            @click="closeCreate()"
                            :disabled="saving"
                            class="
                                rounded-lg
                                px-3
                                py-2
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.06em]
                                text-[#755847]
                                transition
                                hover:bg-[#f3e7d8]
                                disabled:opacity-40
                            "
                        >
                            Cancelar
                        </button>

                        <button
                            type="button"
                            @click="saveTransformation()"
                            :disabled="saving"
                            class="character-transformations-v14-create-button"
                        >
                            <span
                                x-show="!saving"
                                x-text="submitLabel"
                            ></span>

                            <span
                                x-show="saving"
                                x-cloak
                            >
                                Salvando...
                            </span>
                        </button>
                    </footer>
                </div>
            </div>
        </template>


        {{-- Modal próprio: cofre de criaturas --}}
        <template x-teleport="body">
            <div x-show="npcPickerOpen" x-cloak class="ct-v50-overlay character-sheet-theme" data-theme="{{ $characterTheme }}"
                role="dialog" aria-modal="true" aria-label="Escolher criatura do Cofre">
                <div class="character-transformations-v14-backdrop absolute inset-0" @click="npcPickerOpen = false"></div>
                <div class="ct-v50-picker character-transformations-v14-create-modal">
                    <header class="ct-v50-picker-head">
                        <div><p class="ct-v50-kicker">Cofre</p><h2>Escolher criatura</h2></div>
                        <button type="button" @click="npcPickerOpen = false" aria-label="Fechar">×</button>
                    </header>
                    <div class="ct-v50-picker-body">
                        <div class="ct-v51-picker-search"><span aria-hidden="true">⌕</span><input type="search" x-model="monsterSearch" placeholder="Pesquisar no Cofre..." class="character-transformations-v14-input"></div>
                        <p class="ct-v50-picker-empty" x-show="npcs.length === 0">O Cofre não possui criaturas disponíveis.</p>
                        <p class="ct-v50-picker-empty" x-show="npcs.length > 0 && filteredNpcs.length === 0">Nenhuma criatura corresponde à pesquisa.</p>
                        <p class="ct-v51-picker-note" x-show="npcs.some(n => !n.supported)">Criaturas em formato externo aparecem para consulta, mas só fichas compatíveis podem ser escolhidas.</p>
                            <div
                                x-show="npcs.length > 0"
                                x-cloak
                                class="character-transformations-v14-npc-list"
                            >
                                <template
                                    x-for="npc in filteredNpcs"
                                    :key="npc.id"
                                >
                                    <button
                                        type="button"
                                        @click="selectNpc(npc.id)"
                                        :disabled="!npc.supported"
                                        :title="npc.supported ? 'Selecionar ' + npc.name : 'Formato ainda não aceito para transformações'"
                                        class="character-transformations-v14-npc"
                                        :class="{
                                            'is-active':
                                                Number(form.source_npc_id)
                                                === Number(npc.id),

                                            'is-unsupported':
                                                !npc.supported
                                        }"
                                    >
                                        <span class="character-transformations-v14-npc-image">
                                            <template x-if="npc.image_url">
                                                <img
                                                    :src="npc.image_url"
                                                    alt=""
                                                >
                                            </template>

                                            <template x-if="!npc.image_url">
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    aria-hidden="true"
                                                >
                                                    <path d="M6.5 8.5 9 5l3 2 3-2 2.5 3.5V15L12 19l-5.5-4V8.5Z" />
                                                </svg>
                                            </template>
                                        </span>

                                        <span class="min-w-0">
                                            <span
                                                class="
                                                    block
                                                    truncate
                                                    font-serif
                                                    text-[12px]
                                                    font-black
                                                    text-[#53150f]
                                                "
                                                x-text="npc.name"
                                            ></span>

                                            <span
                                                class="
                                                    mt-1
                                                    block
                                                    text-[9px]
                                                    font-bold
                                                    text-[#755847]
                                                "
                                                x-text="
                                                    npc.challenge_rating !== null
                                                        && npc.challenge_rating !== undefined
                                                        ? `ND ${npc.challenge_rating}`
                                                        : 'Criatura'
                                                "
                                            ></span>

                                            <span
                                                class="character-transformations-v14-format-badge"
                                                :class="{
                                                    'unsupported':
                                                        !npc.supported
                                                }"
                                                x-text="npc.format_label"
                                            ></span>
                                        </span>
                                    </button>
                                </template>
                            </div>

                    </div>
                </div>
            </div>
        </template>

        {{-- V51: seletor pesquisável de magias, aberto sobre a criação. --}}
        <template x-teleport="body">
            <div x-show="spellPickerOpen" x-cloak class="ct-v50-overlay character-sheet-theme" data-theme="{{ $characterTheme }}"
                role="dialog" aria-modal="true" aria-label="Escolher feitiço do Grimório">
                <div class="character-transformations-v14-backdrop absolute inset-0" x-on:click="spellPickerOpen=false"></div>
                <div class="ct-v50-picker character-transformations-v14-create-modal" x-on:click.stop>
                    <header class="ct-v50-picker-head">
                        <div><p class="ct-v50-kicker">Grimório</p><h2>Vincular feitiço</h2></div>
                        <button type="button" x-on:click="spellPickerOpen=false" aria-label="Fechar seleção de feitiço">×</button>
                    </header>
                    <div class="ct-v50-picker-body">
                        <div class="ct-v51-picker-search"><span aria-hidden="true">⌕</span>
                            <input type="search" x-model="spellSearch" class="character-transformations-v14-input" placeholder="Pesquisar magias conhecidas...">
                        </div>
                        <div class="ct-v51-picker-list" x-show="filteredLinkedSpells.length > 0" x-cloak>
                            <template x-for="spell in filteredLinkedSpells" :key="'linked-spell-' + spell.id">
                                <button type="button" class="ct-v51-spell-row"
                                    :class="{'is-active': Number(form.linked_spell_id) === Number(spell.id)}"
                                    x-on:click="selectLinkedSpell(spell.id)">
                                    <span class="ct-v51-spell-level ct-v55-picker-art" aria-hidden="true">
                                        <img x-show="spell.image_url && !spell._imageFailed" x-cloak :src="spell.image_url || null"
                                            loading="lazy" alt="" x-on:error="spell._imageFailed = true">
                                        <span x-show="!spell.image_url || spell._imageFailed" x-text="Number(spell.level) === 0 ? 'T' : spell.level"></span>
                                    </span>
                                    <span class="ct-v51-spell-row-copy">
                                        <strong x-text="spell.name"></strong>
                                        <small x-text="(Number(spell.level) === 0 ? 'Truque' : spell.level + 'º círculo') + ' · Concentração'"></small>
                                    </span>
                                    <span class="ct-v51-selection-mark" x-show="Number(form.linked_spell_id) === Number(spell.id)" aria-hidden="true">✓</span>
                                </button>
                            </template>
                        </div>
                        <p class="ct-v50-picker-empty" x-show="filteredLinkedSpells.length === 0"
                            x-text="linkedSpells.length === 0 ? 'Nenhuma magia de concentração no Grimório. Adicione uma magia primeiro.' : 'Nenhuma magia corresponde à pesquisa.'"></p>
                        <p class="ct-v51-picker-note">A transformação compartilha a concentração da magia escolhida. Não consome outro espaço de magia.</p>
                    </div>
                </div>
            </div>
        </template>

        {{-- O feitiço já foi conjurado; não há novo consumo de slot. --}}
        <template x-teleport="body">
            <div x-show="castTransformationPrompt" x-cloak class="ct-v50-overlay character-sheet-theme" data-theme="{{ $characterTheme }}"
                role="dialog" aria-modal="true" aria-label="Transformar após conjuração">
                <div class="character-transformations-v14-backdrop absolute inset-0" @click="castTransformationPrompt = null"></div>
                <div class="ct-v50-confirm character-transformations-v14-create-modal" @click.stop>
                    <p class="ct-v50-kicker">Feitiço conjurado</p>
                    <h2 x-text="castTransformationPrompt?.name || 'Nova forma'"></h2>
                    <p>Deseja assumir esta transformação agora? Ela usa a mesma concentração do feitiço que você acabou de lançar.</p>
                    <div x-show="castTransformationMatches.length > 1" x-cloak class="ct-v50-cast-choices">
                        <template x-for="form in castTransformationMatches" :key="form.id">
                            <button type="button" @click="castTransformationPrompt = form"
                                :class="{ 'is-selected': castTransformationPrompt?.id === form.id }"
                                x-text="form.name"></button>
                        </template>
                    </div>
                    <p class="character-transformations-v14-error" x-show="castTransformationError" x-text="castTransformationError"></p>
                    <div class="ct-v50-confirm-actions">
                        <button type="button" class="ct-v50-light-button" @click="castTransformationPrompt = null">Agora não</button>
                        <button type="button" class="character-transformations-v14-create-button" :disabled="actingId !== null"
                            @click="confirmLinkedTransformation()" x-text="actingId !== null ? 'Transformando...' : 'Transformar agora'"></button>
                    </div>
                </div>
            </div>
        </template>

        {{-- V54: resultado de duração escolhido antes de ativar e gastar usos. --}}
        <template x-teleport="body">
            <div x-show="durationModalOpen" x-cloak class="ct-v50-overlay ct-v54-overlay character-sheet-theme"
                data-theme="{{ $characterTheme }}" role="dialog" aria-modal="true" aria-label="Duração da transformação">
                <div class="character-transformations-v14-backdrop absolute inset-0" @click="closeDurationModal()"></div>
                <section class="ct-v54-roll-modal" @click.stop>
                    <header class="ct-v54-roll-head">
                        <div><small>Transformação</small><h2>Duração da forma</h2>
                            <p x-text="durationCandidate?.name || ''"></p></div>
                        <button type="button" title="Fechar" aria-label="Fechar" :disabled="durationRollBusy || actingId !== null" @click="closeDurationModal()">×</button>
                    </header>
                    <div class="ct-v54-roll-body">
                        <p class="ct-v54-expression" x-text="(durationCandidate?.duration_formula || '') + ' rodadas'"></p>
                        <div class="ct-v54-dice-row" x-show="durationRollMode === 'automatic'" :class="{'is-animating': durationRollBusy}">
                            <template x-for="(face, index) in durationDieFaces" :key="index">
                                <div class="ct-v54-die-cell">
                                    <template x-if="durationParts?.sides === 4"><x-ui.poly-die :sides="4" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                    <template x-if="durationParts?.sides === 6"><x-ui.poly-die :sides="6" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                    <template x-if="durationParts?.sides === 8"><x-ui.poly-die :sides="8" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                    <template x-if="durationParts?.sides === 10"><x-ui.poly-die :sides="10" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                    <template x-if="durationParts?.sides === 12"><x-ui.poly-die :sides="12" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                    <template x-if="durationParts?.sides === 20"><x-ui.poly-die :sides="20" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                    <template x-if="durationParts?.sides === 100"><x-ui.poly-die :sides="100" :size="76" x-bind:class="durationRollBusy ? 'is-rolling' : (durationRollResult !== null ? 'is-landed' : '')"><span x-text="face"></span></x-ui.poly-die></template>
                                </div>
                            </template>
                            <span class="ct-v54-modifier" x-show="durationParts?.modifier" x-text="durationParts?.modifier > 0 ? '+' + durationParts.modifier : durationParts?.modifier"></span>
                        </div>
                        <div class="ct-v54-result" x-show="durationRollMode === 'automatic'" x-cloak>
                            <small x-text="durationRollBusy ? 'Rolando…' : 'Total'"></small>
                            <strong x-text="durationRollBusy ? '···' : (durationRollResult ?? '—')"></strong>
                            <span>rodadas</span>
                        </div>
                        <label x-show="durationRollMode === 'manual'" x-cloak class="ct-v54-manual">
                            <span>Resultado dos dados lançados na mesa</span>
                            <input type="number" step="1" min="1" max="10000" x-model="durationManualValue" placeholder="Informe o total" class="character-transformations-v14-input" inputmode="numeric">
                        </label>
                        <p class="ct-v54-roll-error" x-show="durationRollError" x-cloak x-text="durationRollError"></p>

                    </div>
                    <footer class="ct-v54-roll-actions">
                        <button type="button" class="ct-v50-light-button" :disabled="durationRollBusy || actingId !== null"
                            @click="durationRollMode === 'automatic' ? (durationRollMode = 'manual') : (durationRollMode = 'automatic')"
                            x-text="durationRollMode === 'automatic' ? 'Rolar na mesa' : 'Resultado automático'"></button>
                        <button type="button" class="ct-v50-light-button" :disabled="durationRollBusy || actingId !== null"
                            x-show="durationRollMode === 'automatic'" @click="rollDurationDice()">Rolar novamente</button>
                        <button type="button" class="character-transformations-v14-create-button"
                            :disabled="durationRollBusy || actingId !== null || (durationRollMode === 'automatic' && durationRollResult === null)"
                            @click="applyDurationRoll()" x-text="actingId !== null ? 'Transformando…' : 'Usar resultado'"></button>
                    </footer>
                </section>
            </div>
        </template>

        {{-- O acesso rápido está no trilho original do show.blade.php;
             este componente fornece o mesmo modal por evento. --}}

        {{-- Modal compartilhável futuramente com efeitos e habilidades. --}}
        <template x-teleport="body">
            <div x-show="roundModalOpen" x-cloak class="ct-form-round-backdrop character-sheet-theme" data-theme="{{ $characterTheme }}" @click.self="roundModalOpen = false"
                @keydown.escape.window="roundModalOpen = false">
                <section class="ct-form-round-modal" role="dialog" aria-modal="true" aria-label="Acompanhamento de rodadas">
                    <header class="ct-form-round-header">
                        <div><small>Iniciativa · Acompanhamento</small>
                        <h2 x-text="active?.name || 'Transformação'"></h2></div>
                        <button type="button" @click="roundModalOpen = false" aria-label="Fechar">×</button>
                    </header>
                    <div class="ct-form-round-body">
                        <p class="ct-form-round-combat" x-text="roundInfo.combat_name || 'Sem combate associado'"></p>
                        <div class="ct-form-round-grid">
                            <div><small>Rodada atual</small><strong x-text="roundInfo.current_round ?? '—'"></strong></div>
                            <div><small>Começou na</small><strong x-text="roundInfo.started_round ?? '—'"></strong></div>
                            <div><small>Rodadas decorridas</small><strong x-text="roundInfo.elapsed_rounds ?? (roundInfo.current_round != null && roundInfo.started_round != null ? Math.max(0, roundInfo.current_round - roundInfo.started_round) : '—')"></strong></div>
                            <div><small>Restantes</small><strong x-text="roundInfo.duration_remaining == null ? '∞' : roundInfo.duration_remaining"></strong></div>
                        </div>
                        <p class="ct-form-round-detail" x-show="roundInfo.combat_active && roundInfo.current_turn != null"
                            x-text="`Turno atual da iniciativa: ${Number(roundInfo.current_turn) + 1}${roundInfo.initiative != null ? ' · Sua iniciativa: ' + roundInfo.initiative : ''}`"></p>
                        <p class="ct-form-round-detail" x-show="!roundInfo.combat_active">Contagem pausada: é necessário um combate ativo para avançar rodadas.</p>
                        <div class="ct-form-round-concentration" :class="active?.requires_concentration ? 'is-active' : 'is-inactive'">
                            <span class="ct-form-round-concentration-dot" aria-hidden="true"></span>
                            <span x-text="active?.requires_concentration ? 'Concentração ativa — mantenha os salvamentos de Constituição após sofrer dano.' : 'Esta transformação não exige concentração.'"></span>
                        </div>
                        <p class="ct-form-round-error" x-show="roundError" x-text="roundError"></p>
                    </div>
                    <footer class="ct-form-round-footer">
                        <button type="button" @click="refreshRoundInfo()" :disabled="roundLoading"
                            x-text="roundLoading ? 'Atualizando...' : 'Atualizar'"></button>
                        <button type="button" @click="roundModalOpen = false">Fechar</button>
                    </footer>
                </section>
            </div>
        </template>

        @if (
            $activeTransformation
            && $activeTransformation->type === 'monster_sheet'
            && $activeState
            && $activeState->is_active
        )
            @include(
                'characters.components.monster-sheet',
                [
                    'character' => $character,
                    'transformation' => $activeTransformation,
                    'state' => $activeState,
                    'npc' => $activeMonsterNpc,
                    'sheetData' => $activeMonsterSheetData,
                    'unsupportedFormat' => $activeMonsterUnsupported,
                ]
            )
        @endif
    </section>

@endif