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
    $imageUrl =
        $publicImageUrl(
            $activeTransformation?->image_path
        );

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
                    $publicImageUrl
                ) {
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

                    $durationLabel =
                        $durationLabels[$durationType]
                        ?? null;

                    if (
                        $durationType !== 'indefinite'
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
                    $photo =
                        $publicImageUrl(
                            $transformation->image_path
                        );

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

                        'duration_type' =>
                            $durationType,

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

    try {
        $availableNpcs =
            collect(
                app(
                    \App\Services\NpcService::class
                )->getAllByUser(
                    (int) $character->user_id
                )
            );
    } catch (\Throwable $exception) {
        $availableNpcs =
            collect();
    }

    $npcPayload =
        $availableNpcs
            ->map(function ($npc) use (
                $isNpcBuilder,
                $publicImageUrl
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
                        $publicImageUrl(
                            $npc->image_path
                        ),

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

        </style>
    @endonce


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

            editingId: null,

            imageFile: null,
            imagePreview: null,
            imageRemove: false,

            monsterSearch: '',

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
                requires_concentration: false,
                duration_type: 'indefinite',
                duration_value: 1,
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
                    requires_concentration: false,
                    duration_type: 'indefinite',
                    duration_value: 1,
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
                        item.casting_time
                        || 'none',

                    requires_concentration:
                        !!item.requires_concentration,

                    duration_type:
                        item.duration_type
                        || 'indefinite',

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
                this.form.source_npc_id =
                    Number(id);
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

                this.concentrationCheck =
                    null;

                this.concentrationRoll =
                    null;

                this.concentrationError =
                    null;
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

                    const passed =
                        total
                        >= difficultyClass;

                    this.concentrationRoll = {
                        die,
                        total,
                        modifier,
                        passed,
                        formatted:
                            data?.formatted
                            ?? expression,
                    };

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

                body.append(
                    'casting_time',
                    this.form.casting_time
                );

                body.append(
                    'requires_concentration',
                    this.form.requires_concentration
                        ? '1'
                        : '0'
                );

                body.append(
                    'duration_type',
                    this.form.duration_type
                );

                if (
                    this.form.duration_type
                        !== 'indefinite'
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

                if (
                    this.form.duration_type
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


            async activateTransformation(item) {
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

        data-dice-roll-url="{{ url('/api/roll') }}"

        data-deactivate-url="{{ route(
            'characters.transformations.deactivate',
            $character
        ) }}"

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
            if (specialPrompt) {
                dismissSpecialPrompt()
            } else if (deleteCandidate) {
                cancelDelete()
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


                    <div class="character-transformations-v14-list">

                        {{-- FORMA NORMAL --}}
                        <div class="character-transformations-v14-option">
                            <div
                                class="character-transformations-v14-option-image"
                                aria-hidden="true"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="7"
                                        stroke-width="1.5"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9.5 12h5M12 9.5v5"
                                    />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p
                                    class="
                                        font-serif
                                        text-[17px]
                                        font-black
                                        text-[#53150f]
                                    "
                                >
                                    Forma Normal
                                </p>

                                <p
                                    class="
                                        mt-1
                                        text-[10px]
                                        leading-relaxed
                                        text-[#755847]
                                    "
                                >
                                    A ficha original de {{ $character->name }}.
                                </p>

                                @if (!$activeTransformation)
                                    <span
                                        class="
                                            mt-2
                                            inline-flex
                                            rounded-full
                                            bg-[#eadbc8]
                                            px-2
                                            py-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                            text-[#53150f]
                                        "
                                    >
                                        Forma atual
                                    </span>
                                @endif
                            </div>

                            <div class="character-transformations-v14-option-actions">
                                <template x-if="active">
                                    <button
                                        type="button"
                                        @click="returnToNormal()"
                                        :disabled="
                                            normalizing
                                            || actingId !== null
                                        "
                                        class="
                                            character-transformations-v14-action
                                            normal
                                        "
                                    >
                                        <span x-show="!normalizing">
                                            Voltar à Forma Normal
                                        </span>

                                        <span
                                            x-show="normalizing"
                                            x-cloak
                                        >
                                            Voltando...
                                        </span>
                                    </button>
                                </template>

                                <template x-if="!active">
                                    <span
                                        class="
                                            text-[9px]
                                            font-black
                                            uppercase
                                            tracking-[0.08em]
                                            text-[#6f4c36]/60
                                        "
                                    >
                                        Ativa
                                    </span>
                                </template>
                            </div>
                        </div>


                        {{-- FORMAS CADASTRADAS --}}
                        <template
                            x-for="
                                item in transformations
                            "
                            :key="
                                item.id
                            "
                        >
                            <div class="character-transformations-v14-option">
                                <div class="character-transformations-v14-option-image">

                                    <span class="character-transformations-v14-option-image-fallback">
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
                                    </span>

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
                                    <div
                                        class="
                                            flex
                                            min-w-0
                                            flex-wrap
                                            items-center
                                            gap-1.5
                                        "
                                    >
                                        <p
                                            class="
                                                min-w-0
                                                truncate
                                                font-serif
                                                text-[18px]
                                                font-black
                                                text-[#53150f]
                                            "
                                            x-text="item.name"
                                        ></p>

                                        <span
                                            class="character-transformations-v14-tag"
                                            x-text="typeLabel(item)"
                                        ></span>

                                        <template
                                            x-if="
                                                item.type === 'monster_sheet'
                                                && item.monster_supported === false
                                            "
                                        >
                                            <span
                                                class="character-transformations-v14-tag"
                                                style="
                                                    border-color: rgba(127,38,29,.30);
                                                    background: #fff2ee;
                                                    color: #7f261d;
                                                "
                                            >
                                                Formato externo
                                            </span>
                                        </template>
                                    </div>

                                    <div
                                        class="
                                            mt-2
                                            flex
                                            flex-wrap
                                            items-center
                                            gap-1.5
                                        "
                                    >
                                        <template x-if="item.casting_label">
                                            <span
                                                class="character-transformations-v14-tag"
                                                x-text="item.casting_label"
                                            ></span>
                                        </template>

                                        <template x-if="item.requires_concentration">
                                            <span class="character-transformations-v14-tag">
                                                Concentração
                                            </span>
                                        </template>

                                        <template x-if="item.duration_label">
                                            <span
                                                class="character-transformations-v14-tag"
                                                x-text="item.duration_label"
                                            ></span>
                                        </template>

                                        <template x-if="item.is_special">
                                            <span class="character-transformations-v14-tag">
                                                Especial
                                            </span>
                                        </template>

                                        <span
                                            class="character-transformations-v14-tag"
                                            x-text="recoveryLabel(item)"
                                        ></span>

                                        <template
                                            x-if="
                                                item.applies_exhaustion
                                                && item.exhaustion_levels > 0
                                            "
                                        >
                                            <span
                                                class="character-transformations-v14-tag"
                                                x-text="
                                                    `+${item.exhaustion_levels} Exaustão`
                                                "
                                            ></span>
                                        </template>
                                    </div>
                                </div>

                                <div class="character-transformations-v14-option-actions">
                                    <template x-if="item.type === 'character_sheet'">
                                        <button
                                            type="button"
                                            @click="openCharacterSheet(item)"
                                            :disabled="
                                                saving
                                                || actingId !== null
                                                || normalizing
                                                || preparingSheetId !== null
                                                || (
                                                    active
                                                    && Number(active.id)
                                                        !== Number(item.id)
                                                )
                                            "
                                            class="
                                                character-transformations-v14-action
                                                edit
                                            "
                                            :title="
                                                item.form_character_id
                                                    ? 'Editar ficha mecânica'
                                                    : 'Preparar ficha mecânica'
                                            "
                                        >
                                            <svg
                                                class="h-3 w-3"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                aria-hidden="true"
                                            >
                                                <path d="M4 5.5h16v13H4z" />
                                                <path d="M8 9h8M8 12h8M8 15h5" />
                                            </svg>

                                            <span
                                                x-show="
                                                    Number(preparingSheetId)
                                                        !== Number(item.id)
                                                "
                                                x-text="
                                                    item.form_character_id
                                                        ? 'Editar Ficha'
                                                        : 'Criar Ficha'
                                                "
                                            ></span>

                                            <span
                                                x-show="
                                                    Number(preparingSheetId)
                                                        === Number(item.id)
                                                "
                                                x-cloak
                                            >
                                                Preparando...
                                            </span>
                                        </button>
                                    </template>

                                    <button
                                        type="button"
                                        @click="openEdit(item)"
                                        :disabled="
                                            saving
                                            || actingId !== null
                                            || normalizing
                                        "
                                        class="
                                            character-transformations-v14-action
                                            edit
                                        "
                                        title="Editar transformação"
                                    >
                                        <svg
                                            class="h-3 w-3"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <path d="M4 20h4l11-11-4-4L4 16v4Z" />
                                            <path d="m13.5 6.5 4 4" />
                                        </svg>

                                        Editar
                                    </button>

                                    <button
                                        type="button"
                                        @click="
                                            requestDelete(
                                                item
                                            )
                                        "
                                        :disabled="
                                            saving
                                            || actingId !== null
                                            || normalizing
                                            || deletingId !== null
                                            || (
                                                active
                                                && Number(active.id)
                                                    === Number(item.id)
                                            )
                                        "
                                        class="
                                            character-transformations-v14-action
                                            delete
                                        "
                                        title="Excluir transformação"
                                        aria-label="Excluir transformação"
                                    >
                                        <svg
                                            class="h-3 w-3"
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
                                            <path d="M10 11v5M14 11v5" />
                                        </svg>

                                        Excluir
                                    </button>

                                    <template
                                        x-if="
                                            active
                                            && Number(active.id)
                                                === Number(item.id)
                                        "
                                    >
                                        <span
                                            class="
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-[0.08em]
                                                text-[#53150f]
                                            "
                                        >
                                            Forma atual
                                        </span>
                                    </template>

                                    <template
                                        x-if="
                                            !active
                                            || Number(active.id)
                                                !== Number(item.id)
                                        "
                                    >
                                        <button
                                            type="button"
                                            @click="activateTransformation(item)"
                                            :disabled="
                                                actingId !== null
                                                || normalizing
                                                || (
                                                    active
                                                    && Number(active.id)
                                                        !== Number(item.id)
                                                )
                                                || (
                                                    item.uses_max !== null
                                                    && item.uses_max !== undefined
                                                    && Number(item.uses_current) < 1
                                                )
                                                || (
                                                    item.type === 'monster_sheet'
                                                    && item.monster_supported === false
                                                )
                                            "
                                            class="
                                                character-transformations-v14-action
                                                transform
                                            "
                                            :title="
                                                active
                                                && Number(active.id)
                                                    !== Number(item.id)
                                                    ? 'Volte à Forma Normal primeiro'
                                                    : (
                                                        item.type === 'monster_sheet'
                                                        && item.monster_supported === false
                                                            ? 'Formato externo não suportado'
                                                            : (
                                                                item.uses_max !== null
                                                                && item.uses_max !== undefined
                                                                && Number(item.uses_current) < 1
                                                                    ? 'Sem usos disponíveis'
                                                                    : 'Transformar'
                                                            )
                                                    )
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
                                    </template>
                                </div>
                            </div>
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

                            <p
                                class="
                                    mt-1.5
                                    text-[11px]
                                    leading-relaxed
                                    text-[#6f4c36]
                                "
                            >
                                Defina ou ajuste a origem, aparência e regras desta forma.
                            </p>
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

                        {{-- TIPO --}}
                        <section class="character-transformations-v14-section">
                            <h3 class="character-transformations-v14-section-title">
                                Tipo de transformação
                            </h3>

                            <p class="character-transformations-v14-section-copy">
                                Escolha qual ficha mecânica será usada quando esta forma estiver ativa.
                            </p>

                            <div class="character-transformations-v14-type-grid">
                                <button
                                    type="button"
                                    @click="setType('character_sheet')"
                                    class="character-transformations-v14-type"
                                    :class="{
                                        'is-active':
                                            form.type === 'character_sheet'
                                    }"
                                >
                                    <span
                                        class="
                                            flex
                                            h-8
                                            w-8
                                            items-center
                                            justify-center
                                            rounded-lg
                                            bg-[#eadbc8]
                                            text-[#6b1d14]
                                        "
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                            aria-hidden="true"
                                        >
                                            <path d="M6 4h12v16H6z" />
                                            <path d="M9 8h6M9 12h6M9 16h4" />
                                        </svg>
                                    </span>

                                    <span
                                        class="
                                            mt-2
                                            block
                                            font-serif
                                            text-[14px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Nova Ficha
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#755847]
                                        "
                                    >
                                        Uma ficha mecânica alternativa do próprio personagem.
                                    </span>
                                </button>

                                <button
                                    type="button"
                                    @click="setType('monster_sheet')"
                                    class="character-transformations-v14-type"
                                    :class="{
                                        'is-active':
                                            form.type === 'monster_sheet'
                                    }"
                                >
                                    <span
                                        class="
                                            flex
                                            h-8
                                            w-8
                                            items-center
                                            justify-center
                                            rounded-lg
                                            bg-[#eadbc8]
                                            text-[#6b1d14]
                                        "
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                            aria-hidden="true"
                                        >
                                            <path d="M6.5 8.5 9 5l3 2 3-2 2.5 3.5V15L12 19l-5.5-4V8.5Z" />
                                            <path d="M9.5 11h.01M14.5 11h.01" />
                                        </svg>
                                    </span>

                                    <span
                                        class="
                                            mt-2
                                            block
                                            font-serif
                                            text-[14px]
                                            font-black
                                            text-[#53150f]
                                        "
                                    >
                                        Ficha de Monstro
                                    </span>

                                    <span
                                        class="
                                            mt-1
                                            block
                                            text-[10px]
                                            leading-relaxed
                                            text-[#755847]
                                        "
                                    >
                                        Usa uma criatura criada no Construtor de NPC do Spellbound.
                                    </span>
                                </button>
                            </div>
                        </section>


                        {{-- IDENTIDADE --}}
                        <section class="character-transformations-v14-section">
                            <h3 class="character-transformations-v14-section-title">
                                Identidade da forma
                            </h3>

                            <div class="mt-3">
                                <label>
                                    <span class="character-transformations-v14-label">
                                        Nome
                                    </span>

                                    <input
                                        type="text"
                                        maxlength="120"
                                        x-model="form.name"
                                        placeholder="Ex.: Forma Demoníaca"
                                        class="character-transformations-v14-input"
                                    >
                                </label>
                            </div>

                            <div class="character-transformations-v14-photo">
                                <div class="character-transformations-v14-photo-preview">
                                    <template x-if="imagePreview">
                                        <img
                                            :src="imagePreview"
                                            alt="Prévia da transformação"
                                        >
                                    </template>

                                    <template x-if="!imagePreview">
                                        <svg
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            aria-hidden="true"
                                        >
                                            <path d="M4 5h16v14H4z" />
                                            <path d="m7 16 4-4 3 3 2-2 4 4" />
                                            <circle cx="9" cy="9" r="1" />
                                        </svg>
                                    </template>
                                </div>

                                <div class="min-w-0">
                                    <span class="character-transformations-v14-label">
                                        Foto da transformação
                                    </span>

                                    <p class="text-[10px] leading-relaxed text-[#755847]">
                                        Opcional. Esta foto aparece apenas quando esta forma existir.
                                    </p>

                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <label
                                            class="
                                                inline-flex
                                                cursor-pointer
                                                items-center
                                                rounded-lg
                                                border
                                                border-[#b08c62]/40
                                                bg-[#fffdf8]
                                                px-3
                                                py-2
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-[0.06em]
                                                text-[#6b1d14]
                                                transition
                                                hover:bg-[#f3e7d8]
                                            "
                                        >
                                            Escolher foto

                                            <input
                                                type="file"
                                                accept="image/jpeg,image/png,image/webp"
                                                @click="$event.target.value = null"
                                                @change="handleImage($event)"
                                                class="hidden"
                                            >
                                        </label>

                                        <button
                                            x-show="imagePreview"
                                            x-cloak
                                            type="button"
                                            @click="removeImage()"
                                            class="
                                                rounded-lg
                                                px-3
                                                py-2
                                                text-[9px]
                                                font-black
                                                uppercase
                                                tracking-[0.06em]
                                                text-red-700
                                                transition
                                                hover:bg-red-50
                                            "
                                        >
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>


                        {{-- CRIATURA --}}
                        <section
                            x-show="form.type === 'monster_sheet'"
                            x-cloak
                            class="character-transformations-v14-section"
                        >
                            <h3 class="character-transformations-v14-section-title">
                                Criatura do Cofre
                            </h3>

                            <p class="character-transformations-v14-section-copy">
                                A criatura é usada como modelo. Apenas fichas criadas no Construtor de NPC do Spellbound podem ser usadas em Transformações.
                            </p>

                            <input
                                type="search"
                                x-model="monsterSearch"
                                placeholder="Buscar criatura..."
                                class="character-transformations-v14-input mt-3"
                            >

                            <div
                                x-show="npcs.length === 0"
                                x-cloak
                                class="
                                    mt-3
                                    rounded-xl
                                    border
                                    border-dashed
                                    border-[#b08c62]/40
                                    bg-[#f7f0e6]/55
                                    px-4
                                    py-5
                                    text-center
                                    text-[10px]
                                    text-[#755847]
                                "
                            >
                                Nenhuma criatura disponível no seu Cofre.
                            </div>

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

                            <div
                                x-show="
                                    selectedNpc
                                    && !selectedNpc.supported
                                "
                                x-cloak
                                class="character-transformations-v14-format-warning"
                            >
                                <strong>
                                    Formato não compatível
                                </strong>

                                Esta criatura foi importada ou usa um formato diferente do Construtor de NPC do Spellbound.
                                Ela continua disponível normalmente no Cofre, mas não pode ser usada como Ficha de Monstro em Transformações.
                                Escolha uma criatura marcada como “Criada no Spellbound”.
                            </div>
                        </section>


                        {{-- ATIVAÇÃO --}}
                        <section class="character-transformations-v14-section">
                            <h3 class="character-transformations-v14-section-title">
                                Ativação
                            </h3>

                            <p class="character-transformations-v14-section-copy">
                                A opção Feitiço é apenas uma categoria desta transformação e não se conecta ao Grimório.
                            </p>

                            <div class="mt-3">
                                <span class="character-transformations-v14-label">
                                    Tempo de Conjuração
                                </span>

                                <div class="character-transformations-v14-segmented cols-4">
                                    <button
                                        type="button"
                                        @click="form.casting_time = 'none'"
                                        :class="{
                                            'is-active':
                                                form.casting_time === 'none'
                                        }"
                                    >
                                        Nenhum
                                    </button>

                                    <button
                                        type="button"
                                        @click="form.casting_time = 'spell'"
                                        :class="{
                                            'is-active':
                                                form.casting_time === 'spell'
                                        }"
                                    >
                                        Feitiço
                                    </button>

                                    <button
                                        type="button"
                                        @click="form.casting_time = 'bonus_action'"
                                        :class="{
                                            'is-active':
                                                form.casting_time === 'bonus_action'
                                        }"
                                    >
                                        Ação Bônus
                                    </button>

                                    <button
                                        type="button"
                                        @click="form.casting_time = 'action'"
                                        :class="{
                                            'is-active':
                                                form.casting_time === 'action'
                                        }"
                                    >
                                        Ação Padrão
                                    </button>
                                </div>
                            </div>

                            <div class="character-transformations-v14-grid-2 mt-3">
                                <button
                                    type="button"
                                    @click="
                                        form.requires_concentration =
                                            !form.requires_concentration
                                    "
                                    class="character-transformations-v14-toggle-row"
                                >
                                    <span class="min-w-0 text-left">
                                        <span class="block font-serif text-[12px] font-black text-[#53150f]">
                                            Concentração
                                        </span>

                                        <span class="mt-1 block text-[9px] text-[#755847]">
                                            A forma exige concentração?
                                        </span>
                                    </span>

                                    <span
                                        class="character-transformations-v14-switch"
                                        :class="{
                                            'is-active':
                                                form.requires_concentration
                                        }"
                                    ></span>
                                </button>

                                <div>
                                    <span class="character-transformations-v14-label">
                                        Duração
                                    </span>

                                    <select
                                        x-model="form.duration_type"
                                        class="character-transformations-v14-select"
                                    >
                                        <option value="indefinite">
                                            Até ser encerrada
                                        </option>

                                        <option value="rounds">
                                            Rodadas
                                        </option>

                                        <option value="minutes">
                                            Minutos
                                        </option>

                                        <option value="hours">
                                            Horas
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div
                                x-show="form.duration_type !== 'indefinite'"
                                x-cloak
                                class="mt-3"
                            >
                                <label>
                                    <span class="character-transformations-v14-label">
                                        Quantidade
                                    </span>

                                    <input
                                        type="number"
                                        min="1"
                                        max="1000000"
                                        x-model.number="form.duration_value"
                                        class="character-transformations-v14-input"
                                    >
                                </label>
                            </div>
                        </section>


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