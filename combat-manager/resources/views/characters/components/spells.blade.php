@php
    $spellbookUrls = [
        'state' =>
            route(
                'characters.spellbook.state',
                $character
            ),

        'catalog' =>
            route(
                'characters.spellbook.catalog',
                $character
            ),

        'spellStore' =>
            route(
                'characters.spellbook.spells.store',
                [
                    'character' => $character,
                    'spell' => '__SPELL__',
                ]
            ),

        'spellUpdate' =>
            route(
                'characters.spellbook.spells.update',
                [
                    'character' => $character,
                    'spell' => '__SPELL__',
                ]
            ),

        'spellDelete' =>
            route(
                'characters.spellbook.spells.destroy',
                [
                    'character' => $character,
                    'spell' => '__SPELL__',
                ]
            ),

        'spellMediaStore' => route(
            'characters.spellbook.spells.media.store',
            ['character' => $character, 'spell' => '__SPELL__']
        ),
        'spellMediaDelete' => route(
            'characters.spellbook.spells.media.destroy',
            ['character' => $character, 'spell' => '__SPELL__']
        ),
        'spellCast' =>
            route(
                'characters.spellbook.spells.cast',
                [
                    'character' => $character,
                    'spell' => '__SPELL__',
                ]
            ),

        'profileStore' =>
            route(
                'characters.spellbook.profiles.store',
                $character
            ),

        'profileImportSheet' =>
            route(
                'characters.spellbook.profiles.import-sheet',
                $character
            ),

        'profileUpdate' =>
            route(
                'characters.spellbook.profiles.update',
                [
                    'character' => $character,
                    'profile' => '__PROFILE__',
                ]
            ),

        'profileDelete' =>
            route(
                'characters.spellbook.profiles.destroy',
                [
                    'character' => $character,
                    'profile' => '__PROFILE__',
                ]
            ),

        'slotUpdate' =>
            route(
                'characters.spellbook.slots.update',
                $character
            ),

        'concentrationDelete' =>
            route(
                'characters.spellbook.concentration.destroy',
                $character
            ),

        'concentrationCheck' =>
            route(
                'characters.spellbook.concentration.check',
                $character
            ),

        'diceRoll' =>
            url('/api/roll'),

        'loadoutStore' =>
            route(
                'characters.spellbook.loadouts.store',
                $character
            ),

        'loadoutUpdate' =>
            route(
                'characters.spellbook.loadouts.update',
                [
                    'character' => $character,
                    'loadout' => '__LOADOUT__',
                ]
            ),

        'loadoutOverwrite' =>
            route(
                'characters.spellbook.loadouts.overwrite',
                [
                    'character' => $character,
                    'loadout' => '__LOADOUT__',
                ]
            ),

        'loadoutApply' =>
            route(
                'characters.spellbook.loadouts.apply',
                [
                    'character' => $character,
                    'loadout' => '__LOADOUT__',
                ]
            ),

        'loadoutDelete' =>
            route(
                'characters.spellbook.loadouts.destroy',
                [
                    'character' => $character,
                    'loadout' => '__LOADOUT__',
                ]
            ),
    ];
@endphp

<style>
    .character-spellbook-v4 {
        --spellbook-ink: var(--theme-text-primary, var(--theme-text, #34231b));
        --spellbook-muted: var(--theme-text-secondary, var(--theme-muted, #674936));
        --spellbook-title: var(--theme-text-strong, var(--theme-title, #53150f));
        --spellbook-label: var(--theme-label, #704326);
        --spellbook-accent: var(--theme-accent-strong, #6b1d14);
        --spellbook-accent-deep: var(--theme-accent-deep, #53150f);
        --spellbook-accent-soft: var(--theme-accent-soft, color-mix(in srgb, var(--spellbook-accent) 14%, var(--theme-paper-strong, #fffdf8) 86%));
        --spellbook-accent-softer: var(--theme-accent-softer, color-mix(in srgb, var(--spellbook-accent) 8%, var(--theme-paper-strong, #fffdf8) 92%));
        --spellbook-paper: var(--theme-paper, #fbf8f1);
        --spellbook-paper-strong: var(--theme-paper-strong, #fffdf8);
        --spellbook-paper-alt: var(--theme-paper-alt, #f7f0e6);
        --spellbook-panel: var(--theme-panel, #f3e7d8);
        --spellbook-panel-soft: var(--theme-panel-soft, #f8f0e6);
        --spellbook-hover: var(--theme-hover, #f4e8d8);
        --spellbook-border: var(--theme-border, rgba(176, 140, 98, .34));
        --spellbook-border-soft: var(--theme-border-soft, rgba(169, 128, 83, .28));
        --spellbook-border-strong: var(--theme-border-strong, rgba(160, 119, 77, .36));
        --spellbook-input-bg: var(--theme-input-bg, #fffdf8);
        --spellbook-input-border: var(--theme-input-border, #cdbb9f);
        --spellbook-input-text: var(--theme-input-text, #34231b);
        --spellbook-input-placeholder: var(--theme-input-placeholder, #9a7b65);
        --spellbook-button-bg: var(--theme-button-bg, #6b1d14);
        --spellbook-button-hover: var(--theme-button-hover, #53150f);
        --spellbook-button-text: var(--theme-button-text, #fffaf2);
        --spellbook-shadow: var(--theme-module-shadow, inset 0 1px 0 rgba(255,255,255,.78), 0 3px 10px rgba(83,21,15,.04));
        --spellbook-shadow-strong: var(--theme-shadow-strong, 0 20px 55px rgba(83,21,15,.16));
        --spellbook-state-concentration: var(--theme-state-concentration, #75558a);
        --spellbook-state-warning: var(--theme-state-warning, #a16207);
        --spellbook-state-damage: var(--theme-state-damage, #991b1b);
        --spellbook-state-heal: var(--theme-state-heal, #276749);
        --spellbook-surface-card: var(--theme-surface-card, var(--spellbook-paper-strong));
        --spellbook-surface-card-alt: var(--theme-surface-card-alt, var(--spellbook-paper-alt));
        --spellbook-surface-inset: var(--theme-surface-inset, var(--spellbook-panel));
        --spellbook-surface-raised: var(--theme-surface-raised, var(--spellbook-paper-strong));
        --spellbook-card-hover: var(--theme-card-hover, var(--spellbook-hover));
        --spellbook-on-accent: var(--theme-on-accent, var(--spellbook-button-text));
        --spellbook-divider: var(--theme-divider, var(--spellbook-border-soft));
        --spellbook-divider-strong: var(--theme-divider-strong, var(--spellbook-border));
        --spellbook-card-background: var(--theme-card-background, var(--spellbook-paper-strong));
        --spellbook-card-alt-background: var(--theme-card-alt-background, var(--spellbook-paper-alt));
        --spellbook-inset-background: var(--theme-inset-background, var(--spellbook-panel));
        --spellbook-band-background: var(--theme-band-background, var(--spellbook-panel));
        --spellbook-button-background: var(--theme-button-background, var(--spellbook-button-bg));
        --spellbook-button-hover-background: var(--theme-button-hover-background, var(--spellbook-button-hover));
        --spellbook-modal-backdrop: var(--theme-modal-backdrop, rgba(0, 0, 0, .78));
        --spellbook-modal-surface: var(--theme-modal-surface, var(--spellbook-paper-strong));
        --spellbook-modal-surface-soft: var(--theme-modal-surface-soft, var(--spellbook-paper));
        --spellbook-modal-surface-inset: var(--theme-modal-surface-inset, var(--spellbook-panel));
        --spellbook-modal-header-background: var(--theme-modal-header-background, var(--spellbook-band-background));
        --spellbook-modal-border: var(--theme-modal-border, var(--spellbook-border));
        --spellbook-modal-border-strong: var(--theme-modal-border-strong, var(--spellbook-border-strong));
        --spellbook-modal-text: var(--theme-modal-text, var(--spellbook-ink));
        --spellbook-modal-title: var(--theme-modal-title, var(--spellbook-title));
        --spellbook-modal-muted: var(--theme-modal-muted, var(--spellbook-muted));
        --spellbook-modal-label: var(--theme-modal-label, var(--spellbook-label));
        --spellbook-modal-shadow: var(--theme-modal-shadow, var(--spellbook-shadow-strong));
        --spellbook-modal-control-shadow: var(--theme-modal-control-shadow, var(--spellbook-shadow));
        --spellbook-accent-shadow: 0 6px 16px color-mix(in srgb, var(--spellbook-accent) 18%, transparent);
        color: var(--spellbook-ink);
    }

    .character-spellbook-v4 .spell-rich-copy {
        color: var(--spellbook-ink);
        font-size: 0.92rem;
        line-height: 1.85;
    }

    .character-spellbook-v4 .spell-rich-copy br + br {
        display: block;
        content: '';
        margin-top: .8rem;
    }

    .character-spellbook-v4 .spell-roll-value {
        color: var(--spellbook-accent);
        font-weight: 900;
        white-space: nowrap;
    }

    .character-spellbook-v4 .spell-damage-type {
        color: color-mix(in srgb, var(--spellbook-accent) 78%, var(--spellbook-label) 22%);
        font-weight: 900;
    }

    .character-spellbook-v4 .spell-rich-text {
        color: var(--spellbook-ink);
        font-size: 0.86rem;
        line-height: 1.75;
    }

    .character-spellbook-v4 .spell-rich-text br + br {
        display: block;
        content: '';
        margin-top: .65rem;
    }

    .character-spellbook-v4 .spell-rule-term {
        position: relative;
        display: inline;
        color: var(--spellbook-accent);
        font-weight: 800;
        text-decoration-line: underline;
        text-decoration-style: dotted;
        text-decoration-thickness: 1px;
        text-underline-offset: 2px;
        cursor: help;
        outline: none;
    }

    .character-spellbook-v4 .spell-rule-term::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -1px;
        height: 2px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--spellbook-accent) 18%, transparent);
    }

    .character-spellbook-v4 .spell-rule-tooltip {
        position: absolute;
        z-index: 180;
        left: 50%;
        bottom: calc(100% + 9px);
        width: min(300px, 72vw);
        transform: translate(-50%, 5px);
        pointer-events: none;
        opacity: 0;
        visibility: hidden;
        border: 1px solid var(--spellbook-border-strong);
        border-radius: 11px;
        background: var(--spellbook-paper-strong);
        padding: 10px 11px;
        box-shadow: var(--spellbook-shadow-strong);
        color: var(--spellbook-ink);
        text-align: left;
        white-space: normal;
        transition: opacity .14s ease, transform .14s ease, visibility .14s ease;
    }

    .character-spellbook-v4 .spell-rule-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        width: 9px;
        height: 9px;
        transform: translate(-50%, -5px) rotate(45deg);
        border-right: 1px solid var(--spellbook-border-strong);
        border-bottom: 1px solid var(--spellbook-border-strong);
        background: var(--spellbook-paper-strong);
    }

    .character-spellbook-v4 .spell-rule-term:hover .spell-rule-tooltip,
    .character-spellbook-v4 .spell-rule-term:focus .spell-rule-tooltip,
    .character-spellbook-v4 .spell-rule-term:focus-visible .spell-rule-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, 0);
    }

    .character-spellbook-v4 .spell-rule-tooltip-kind {
        display: block;
        color: var(--spellbook-label);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .character-spellbook-v4 .spell-rule-tooltip-title {
        display: block;
        margin-top: 2px;
        color: var(--spellbook-title);
        font-family: var(--theme-font-display, Georgia, 'Times New Roman', serif);
        font-size: 13px;
        line-height: 1.25;
    }

    .character-spellbook-v4 .spell-rule-tooltip-copy {
        display: block;
        margin-top: 5px;
        color: var(--spellbook-muted);
        font-size: 10px;
        font-weight: 500;
        line-height: 1.55;
    }

    .character-spellbook-v4 .sb-paper {
        background: var(--spellbook-paper-strong);
        color: var(--spellbook-ink);
    }

    .character-spellbook-v4 .sb-paper-alt {
        background: var(--spellbook-paper-alt);
        color: var(--spellbook-ink);
    }

    .character-spellbook-v4 .sb-panel {
        background: var(--spellbook-panel-soft);
        color: var(--spellbook-ink);
    }

    .character-spellbook-v4 .sb-title {
        color: var(--spellbook-title);
    }

    .character-spellbook-v4 .sb-muted {
        color: var(--spellbook-muted);
    }

    .character-spellbook-v4 .sb-label {
        color: var(--spellbook-label);
    }

    .character-spellbook-v4 .sb-accent {
        color: var(--spellbook-accent);
    }

    .character-spellbook-v4 .sb-border {
        border-color: var(--spellbook-border);
    }

    .character-spellbook-v4 .sb-border-soft {
        border-color: var(--spellbook-border-soft);
    }

    .character-spellbook-v4 .sb-input {
        border-color: var(--spellbook-input-border);
        background: var(--spellbook-input-bg);
        color: var(--spellbook-input-text);
    }

    .character-spellbook-v4 .sb-input::placeholder {
        color: var(--spellbook-input-placeholder);
    }

    .character-spellbook-v4 .sb-input:focus {
        border-color: var(--theme-input-focus, var(--spellbook-accent));
        --tw-ring-color: color-mix(in srgb, var(--theme-input-focus, var(--spellbook-accent)) 24%, transparent);
    }

    .character-spellbook-v4 .sb-button-primary {
        background: var(--theme-button-background, var(--spellbook-button-bg));
        color: var(--spellbook-button-text);
    }

    .character-spellbook-v4 .sb-button-primary:hover {
        background: var(--theme-button-hover-background, var(--spellbook-button-hover));
    }

    .character-spellbook-v4 .sb-button-soft {
        border-color: var(--spellbook-border);
        background: var(--spellbook-paper-strong);
        color: var(--spellbook-accent);
    }

    .character-spellbook-v4 .sb-button-soft:hover {
        background: var(--spellbook-hover);
    }

    .character-spellbook-v4 .sb-shadow {
        box-shadow: var(--spellbook-shadow);
    }

    .character-spellbook-v4 .sb-shadow-strong {
        box-shadow: var(--spellbook-shadow-strong);
    }


    /* ================================================================
       SUPERFÍCIES SEMÂNTICAS DO GRIMÓRIO
       ================================================================ */

    .character-spellbook-v4.sb-modal-backdrop,
    .character-spellbook-v4 .sb-modal-backdrop {
        background: var(--spellbook-modal-backdrop) !important;
    }

    .character-spellbook-v4 .sb-modal-shell {
        border-color: var(--spellbook-modal-border) !important;
        background: var(--spellbook-modal-surface) !important;
        color: var(--spellbook-modal-text) !important;
        box-shadow: var(--spellbook-modal-shadow) !important;
    }

    .character-spellbook-v4 .sb-modal-header {
        border-color: var(--spellbook-modal-border) !important;
        background: var(--spellbook-modal-header-background) !important;
        color: var(--spellbook-modal-text) !important;
    }

    .character-spellbook-v4 .sb-card {
        border-color: var(--spellbook-border) !important;
        background: var(--spellbook-card-background) !important;
        color: var(--spellbook-ink) !important;
    }

    .character-spellbook-v4 .sb-card-alt {
        border-color: var(--spellbook-border-soft) !important;
        background: var(--spellbook-card-alt-background) !important;
        color: var(--spellbook-ink) !important;
    }

    .character-spellbook-v4 .sb-inset {
        border-color: var(--spellbook-border-soft) !important;
        background: var(--spellbook-inset-background) !important;
        color: var(--spellbook-ink) !important;
    }

    .character-spellbook-v4 .sb-band {
        border-color: var(--spellbook-border) !important;
        background: var(--spellbook-band-background) !important;
        color: var(--spellbook-ink) !important;
    }

    /* ================================================================
       COMPATIBILIDADE SEMÂNTICA COM CLASSES LEGADAS
       Cada HEX antigo é traduzido para a FUNÇÃO do tema atual.
       ================================================================ */

    .character-spellbook-v4 [class~="focus:ring-[#6b1d14]"]:focus {
        --tw-ring-color: color-mix(in srgb, var(--theme-input-focus, var(--spellbook-accent)) 28%, transparent) !important;
    }

    .character-spellbook-v4 [class~="bg-[#fbf5eb]/65"],
    .character-spellbook-v4 [class~="bg-[#fffaf3]/65"] {
        background-color: color-mix(in srgb, var(--spellbook-paper-strong) 65%, transparent) !important;
    }

    .character-spellbook-v4 [class~="bg-[#fffaf2]/75"] {
        background-color: color-mix(in srgb, var(--spellbook-paper-strong) 75%, transparent) !important;
    }

    .character-spellbook-v4 [class~="bg-[#fffaf3]/97"] {
        background-color: color-mix(in srgb, var(--spellbook-paper-strong) 97%, transparent) !important;
    }

    .character-spellbook-v4 [class~="bg-[#eee4f2]"] {
        background-color: color-mix(in srgb, var(--spellbook-state-concentration) 14%, var(--spellbook-paper-strong) 86%) !important;
    }

    .character-spellbook-v4 [class~="bg-[#6b1d14]/8"] {
        background-color: var(--spellbook-accent-softer) !important;
    }

    .character-spellbook-v4 [class~="bg-[#6b1d14]"],
    .character-spellbook-v4 [class~="bg-[#721f16]"],
    .character-spellbook-v4 [class~="bg-[#762119]"],
    .character-spellbook-v4 [class~="bg-[#7b2a20]"],
    .character-spellbook-v4 [class~="bg-[#81291f]"] {
        background-color: var(--spellbook-button-bg) !important;
    }

    .character-spellbook-v4 [class~="hover:bg-[#7a281f]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#7c2a20]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#84291f]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#862a20]"]:hover {
        background-color: var(--spellbook-button-hover) !important;
    }

    .character-spellbook-v4 .group:hover [class~="group-hover:bg-[#dfbd8f]"],
    .character-spellbook-v4 [class~="hover:bg-[#dbc6ac]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#ebd2c7]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#f1e3cf]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#f2e6d8]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#f3e4d1]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#f7ede0]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#fbf4e9]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#fbf5eb]"]:hover,
    .character-spellbook-v4 [class~="hover:bg-[#fffaf2]"]:hover {
        background-color: var(--spellbook-card-hover) !important;
    }

    .character-spellbook-v4 [class~="bg-[#d5bea0]"],
    .character-spellbook-v4 [class~="bg-[#e9cca4]"],
    .character-spellbook-v4 [class~="bg-[#ead1ab]"],
    .character-spellbook-v4 [class~="bg-[#ead7bb]"],
    .character-spellbook-v4 [class~="bg-[#eadbc6]"],
    .character-spellbook-v4 [class~="bg-[#eadbc8]"],
    .character-spellbook-v4 [class~="bg-[#eadcc9]"],
    .character-spellbook-v4 [class~="bg-[#ece0ca]"],
    .character-spellbook-v4 [class~="bg-[#efddc1]"],
    .character-spellbook-v4 [class~="bg-[#f0dfc7]"],
    .character-spellbook-v4 [class~="bg-[#f1e5c9]"],
    .character-spellbook-v4 [class~="bg-[#f2e3c9]"],
    .character-spellbook-v4 [class~="bg-[#f3dfd6]"],
    .character-spellbook-v4 [class~="bg-[#f4e7d3]"],
    .character-spellbook-v4 [class~="bg-[#f4e8d8]"],
    .character-spellbook-v4 [class~="bg-[#f5eadb]"],
    .character-spellbook-v4 [class~="bg-[#f9edda]"],
    .character-spellbook-v4 [class~="disabled:bg-[#eee4d7]"]:disabled {
        background-color: var(--spellbook-panel) !important;
    }

    .character-spellbook-v4 [class~="bg-[#f5e7d4]"],
    .character-spellbook-v4 [class~="bg-[#f6ecdf]"],
    .character-spellbook-v4 [class~="bg-[#f7eddf]"],
    .character-spellbook-v4 [class~="bg-[#f7efe4]"],
    .character-spellbook-v4 [class~="bg-[#f8efe2]"],
    .character-spellbook-v4 [class~="bg-[#f8f0e4]"],
    .character-spellbook-v4 [class~="bg-[#faf0e3]"],
    .character-spellbook-v4 [class~="bg-[#fbf3e5]"],
    .character-spellbook-v4 [class~="bg-[#fbf3e6]"],
    .character-spellbook-v4 [class~="bg-[#fbf4e8]"],
    .character-spellbook-v4 [class~="bg-[#fbf4e9]"],
    .character-spellbook-v4 [class~="bg-[#fbf5ec]"] {
        background-color: var(--spellbook-paper-alt) !important;
    }

    .character-spellbook-v4 [class~="bg-[#fbf5eb]"],
    .character-spellbook-v4 [class~="bg-[#fff5e6]"],
    .character-spellbook-v4 [class~="bg-[#fff7e6]"],
    .character-spellbook-v4 [class~="bg-[#fff8ea]"],
    .character-spellbook-v4 [class~="bg-[#fffaf2]"],
    .character-spellbook-v4 [class~="bg-[#fffaf3]"],
    .character-spellbook-v4 [class~="bg-[#fffdf8]"],
    .character-spellbook-v4 [class~="bg-[#fffdf9]"] {
        background-color: var(--spellbook-paper-strong) !important;
    }

    .character-spellbook-v4 [class~="border-[#6b1d14]/30"] {
        border-color: color-mix(in srgb, var(--spellbook-accent) 30%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#6b1d14]/40"] {
        border-color: color-mix(in srgb, var(--spellbook-accent) 40%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#7c382c]/25"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 25%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#8c6046]/30"],
    .character-spellbook-v4 [class~="border-[#8c684c]/30"],
    .character-spellbook-v4 [class~="border-[#8e633f]/30"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 30%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#87594a]/45"],
    .character-spellbook-v4 [class~="border-[#aa8867]/45"],
    .character-spellbook-v4 [class~="border-[#b89d80]/45"],
    .character-spellbook-v4 [class~="border-[#b99b79]/45"],
    .character-spellbook-v4 [class~="border-[#b99d7e]/45"],
    .character-spellbook-v4 [class~="border-[#b99d83]/45"],
    .character-spellbook-v4 [class~="border-[#c7aa89]/45"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 45%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#b89a78]/50"],
    .character-spellbook-v4 [class~="border-[#b99d7c]/50"],
    .character-spellbook-v4 [class~="border-[#baa083]/50"],
    .character-spellbook-v4 [class~="border-[#ccb69a]/50"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 50%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#a98b72]/55"],
    .character-spellbook-v4 [class~="border-[#b79b7c]/55"],
    .character-spellbook-v4 [class~="border-[#c4aa89]/55"],
    .character-spellbook-v4 [class~="border-[#c59f75]/55"],
    .character-spellbook-v4 [class~="border-[#c6aa88]/55"],
    .character-spellbook-v4 [class~="border-[#c8aa88]/55"],
    .character-spellbook-v4 [class~="border-[#c8ad8e]/55"],
    .character-spellbook-v4 [class~="border-[#ccb08e]/55"],
    .character-spellbook-v4 [class~="border-[#d5bfa3]/55"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 55%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#c2a584]/60"],
    .character-spellbook-v4 [class~="border-[#c4a887]/60"],
    .character-spellbook-v4 [class~="border-[#c59f75]/60"],
    .character-spellbook-v4 [class~="border-[#c8aa88]/60"],
    .character-spellbook-v4 [class~="border-[#c9af8e]/60"],
    .character-spellbook-v4 [class~="border-[#cfb89b]/60"],
    .character-spellbook-v4 [class~="border-[#d8c3a6]/60"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 60%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#c9af8e]/65"],
    .character-spellbook-v4 [class~="border-[#cbb08f]/65"],
    .character-spellbook-v4 [class~="border-[#cbb18f]/65"],
    .character-spellbook-v4 [class~="border-[#cdb79c]/65"],
    .character-spellbook-v4 [class~="border-[#d0b696]/65"],
    .character-spellbook-v4 [class~="border-[#d5bea0]/65"],
    .character-spellbook-v4 [class~="border-[#d5c0a7]/65"],
    .character-spellbook-v4 [class~="border-[#d6bea0]/65"],
    .character-spellbook-v4 [class~="border-[#d6bf9f]/65"],
    .character-spellbook-v4 [class~="border-[#d6c0a5]/65"],
    .character-spellbook-v4 [class~="border-[#d7c2aa]/65"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 65%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#d7c2aa]/70"],
    .character-spellbook-v4 [class~="border-[#d8c5ad]/70"],
    .character-spellbook-v4 [class~="border-[#dfcdb5]/70"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 70%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#dfccb5]/75"] {
        border-color: color-mix(in srgb, var(--spellbook-border) 75%, transparent) !important;
    }

    .character-spellbook-v4 [class~="hover:border-[#b59573]/45"]:hover {
        border-color: color-mix(in srgb, var(--spellbook-border-strong) 45%, transparent) !important;
    }

    .character-spellbook-v4 [class~="hover:border-[#9b6a4e]/50"]:hover {
        border-color: color-mix(in srgb, var(--spellbook-border-strong) 50%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-[#762119]"],
    .character-spellbook-v4 [class~="border-[#7b2a20]"] {
        border-color: var(--spellbook-accent) !important;
    }

    .character-spellbook-v4 [class~="border-[#9c795d]"],
    .character-spellbook-v4 [class~="border-[#bea384]"],
    .character-spellbook-v4 [class~="border-[#c3a584]"],
    .character-spellbook-v4 [class~="border-[#c3a686]"],
    .character-spellbook-v4 [class~="border-[#c4a887]"],
    .character-spellbook-v4 [class~="border-[#c4a98a]"],
    .character-spellbook-v4 [class~="border-[#c9ae8c]"],
    .character-spellbook-v4 [class~="border-[#d7c2aa]"],
    .character-spellbook-v4 [class~="border-[#d7c3a8]"],
    .character-spellbook-v4 [class~="border-[#dcc9b1]"],
    .character-spellbook-v4 [class~="border-[#dccab5]"],
    .character-spellbook-v4 [class~="border-[#ddc8af]"],
    .character-spellbook-v4 [class~="border-[#ddccb8]"],
    .character-spellbook-v4 [class~="border-[#dfcfbb]"],
    .character-spellbook-v4 [class~="border-[#dfd0bd]"],
    .character-spellbook-v4 [class~="border-[#e3d3c0]"],
    .character-spellbook-v4 [class~="border-[#e5d4c0]"],
    .character-spellbook-v4 [class~="border-[#ead9c6]"],
    .character-spellbook-v4 [class~="border-[#ebdccb]"],
    .character-spellbook-v4 [class~="border-[#fff8ec]"] {
        border-color: var(--spellbook-border) !important;
    }

    .character-spellbook-v4 [class~="focus:border-[#6b1d14]"]:focus {
        border-color: var(--theme-input-focus, var(--spellbook-accent)) !important;
    }

    .character-spellbook-v4 [class~="border-t-[#6b1d14]"] {
        border-top-color: var(--spellbook-accent) !important;
    }

    .character-spellbook-v4 [class~="hover:text-[#6b1d14]"]:hover,
    .character-spellbook-v4 [class~="text-[#551a13]"],
    .character-spellbook-v4 [class~="text-[#571912]"],
    .character-spellbook-v4 [class~="text-[#671d15]"],
    .character-spellbook-v4 [class~="text-[#6b1d14]"] {
        color: var(--spellbook-accent) !important;
    }

    .character-spellbook-v4 [class~="text-[#2f2019]"],
    .character-spellbook-v4 [class~="text-[#2f211b]"],
    .character-spellbook-v4 [class~="text-[#33241d]"],
    .character-spellbook-v4 [class~="text-[#35231b]"],
    .character-spellbook-v4 [class~="text-[#38251d]"],
    .character-spellbook-v4 [class~="text-[#4b362c]"],
    .character-spellbook-v4 [class~="text-[#4d392e]"] {
        color: var(--spellbook-ink) !important;
    }

    .character-spellbook-v4 [class~="placeholder:text-[#9b8370]"]::placeholder,
    .character-spellbook-v4 [class~="placeholder:text-[#9d8673]"]::placeholder {
        color: var(--spellbook-input-placeholder) !important;
    }

    .character-spellbook-v4 [class~="text-[#644636]"],
    .character-spellbook-v4 [class~="text-[#694838]"],
    .character-spellbook-v4 [class~="text-[#6e4936]"],
    .character-spellbook-v4 [class~="text-[#76523f]"],
    .character-spellbook-v4 [class~="text-[#78523a]"],
    .character-spellbook-v4 [class~="text-[#79521f]"],
    .character-spellbook-v4 [class~="text-[#795741]"],
    .character-spellbook-v4 [class~="text-[#795844]"],
    .character-spellbook-v4 [class~="text-[#7a4f35]"],
    .character-spellbook-v4 [class~="text-[#80604b]"],
    .character-spellbook-v4 [class~="text-[#81604a]"],
    .character-spellbook-v4 [class~="text-[#87654e]"],
    .character-spellbook-v4 [class~="text-[#8a654a]"],
    .character-spellbook-v4 [class~="text-[#8a664d]"],
    .character-spellbook-v4 [class~="text-[#92725b]"],
    .character-spellbook-v4 [class~="text-[#936c4e]"],
    .character-spellbook-v4 [class~="text-[#9a765c]"],
    .character-spellbook-v4 [class~="text-[#9a7b61]"],
    .character-spellbook-v4 [class~="text-[#9b7a60]"],
    .character-spellbook-v4 [class~="text-[#c9ad91]"],
    .character-spellbook-v4 [class~="text-[#d9c0a4]"] {
        color: var(--spellbook-label) !important;
    }

    .character-spellbook-v4 [class~="text-[#6e5746]"],
    .character-spellbook-v4 [class~="text-[#6f5748]"],
    .character-spellbook-v4 [class~="text-[#705746]"],
    .character-spellbook-v4 [class~="text-[#705747]"],
    .character-spellbook-v4 [class~="text-[#715847]"],
    .character-spellbook-v4 [class~="text-[#715947]"],
    .character-spellbook-v4 [class~="text-[#725946]"],
    .character-spellbook-v4 [class~="text-[#725947]"],
    .character-spellbook-v4 [class~="text-[#725b49]"],
    .character-spellbook-v4 [class~="text-[#735a47]"],
    .character-spellbook-v4 [class~="text-[#745947]"],
    .character-spellbook-v4 [class~="text-[#745c49]"],
    .character-spellbook-v4 [class~="text-[#755b48]"],
    .character-spellbook-v4 [class~="text-[#755d49]"],
    .character-spellbook-v4 [class~="text-[#755d4b]"],
    .character-spellbook-v4 [class~="text-[#765d4a]"],
    .character-spellbook-v4 [class~="text-[#765e4c]"],
    .character-spellbook-v4 [class~="text-[#79604c]"],
    .character-spellbook-v4 [class~="text-[#7a5845]"],
    .character-spellbook-v4 [class~="text-[#7d6451]"],
    .character-spellbook-v4 [class~="text-[#7d6652]"],
    .character-spellbook-v4 [class~="text-[#7f6651]"],
    .character-spellbook-v4 [class~="text-[#806653]"],
    .character-spellbook-v4 [class~="text-[#806754]"],
    .character-spellbook-v4 [class~="text-[#806956]"],
    .character-spellbook-v4 [class~="text-[#816754]"],
    .character-spellbook-v4 [class~="text-[#826b58]"],
    .character-spellbook-v4 [class~="text-[#846955]"],
    .character-spellbook-v4 [class~="text-[#846b57]"],
    .character-spellbook-v4 [class~="text-[#866b56]"],
    .character-spellbook-v4 [class~="text-[#8a6852]"],
    .character-spellbook-v4 [class~="text-[#8a6e59]"],
    .character-spellbook-v4 [class~="text-[#8a705c]"],
    .character-spellbook-v4 [class~="text-[#8a715e]"],
    .character-spellbook-v4 [class~="text-[#8b5b45]"],
    .character-spellbook-v4 [class~="text-[#8b6f5b]"],
    .character-spellbook-v4 [class~="text-[#8c6a50]"],
    .character-spellbook-v4 [class~="text-[#8c725d]"],
    .character-spellbook-v4 [class~="text-[#94735d]"],
    .character-spellbook-v4 [class~="text-[#96745c]"],
    .character-spellbook-v4 [class~="text-[#9a7a63]"],
    .character-spellbook-v4 [class~="text-[#9a7d68]"],
    .character-spellbook-v4 [class~="text-[#9b5d46]"],
    .character-spellbook-v4 [class~="text-[#9b7b62]"],
    .character-spellbook-v4 [class~="text-[#b69a82]"],
    .character-spellbook-v4 [class~="text-[#ead9c5]"] {
        color: var(--spellbook-muted) !important;
    }

    .character-spellbook-v4 [class~="text-[#6d477f]"] {
        color: var(--spellbook-state-concentration) !important;
    }

    .character-spellbook-v4 [class~="text-[#2f1712]"],
    .character-spellbook-v4 [class~="text-[#301611]"],
    .character-spellbook-v4 [class~="text-[#381712]"],
    .character-spellbook-v4 [class~="text-[#3f1712]"],
    .character-spellbook-v4 [class~="text-[#411811]"],
    .character-spellbook-v4 [class~="text-[#421911]"],
    .character-spellbook-v4 [class~="text-[#451711]"],
    .character-spellbook-v4 [class~="text-[#451a13]"],
    .character-spellbook-v4 [class~="text-[#4b1812]"],
    .character-spellbook-v4 [class~="text-[#4b1a13]"],
    .character-spellbook-v4 [class~="text-[#4c1812]"],
    .character-spellbook-v4 [class~="text-[#4e1c15]"],
    .character-spellbook-v4 [class~="text-[#4f1812]"],
    .character-spellbook-v4 [class~="text-[#53150f]"],
    .character-spellbook-v4 [class~="text-[#571a13]"],
    .character-spellbook-v4 [class~="text-[#57231a]"],
    .character-spellbook-v4 [class~="text-[#5a1913]"],
    .character-spellbook-v4 [class~="text-[#5a1e16]"],
    .character-spellbook-v4 [class~="text-[#5b1b14]"],
    .character-spellbook-v4 [class~="text-[#5d1d15]"],
    .character-spellbook-v4 [class~="text-[#5d231a]"],
    .character-spellbook-v4 [class~="text-[#5f1a13]"] {
        color: var(--spellbook-title) !important;
    }

    .character-spellbook-v4 [class~="bg-[#f3dfd6]"] {
        background-color: var(--spellbook-accent-soft) !important;
    }

    .character-spellbook-v4 [class~="hover:bg-[#ebd2c7]"]:hover {
        background-color: color-mix(
            in srgb,
            var(--spellbook-accent) 22%,
            var(--spellbook-paper-strong) 78%
        ) !important;
    }

    .character-spellbook-v4 [class~="divide-[#dfcfbb]"] > :not([hidden]) ~ :not([hidden]) {
        border-color: var(--spellbook-border) !important;
    }

    .character-spellbook-v4 .peer:checked ~ [class~="peer-checked:bg-[#6b1d14]"] {
        background-color: var(--spellbook-button-bg) !important;
    }

    .character-spellbook-v4 .peer:checked ~ [class~="peer-checked:border-[#6b1d14]/30"] {
        border-color: color-mix(in srgb, var(--spellbook-accent) 30%, transparent) !important;
    }

    .character-spellbook-v4 .peer:checked ~ [class~="peer-checked:text-white"] {
        color: var(--spellbook-button-text) !important;
    }

    .character-spellbook-v4 [class~="bg-[#7b281d]/70"] {
        background-color: color-mix(in srgb, var(--spellbook-accent) 70%, transparent) !important;
    }

    .character-spellbook-v4 .bg-white {
        background-color: var(--spellbook-input-bg) !important;
    }

    .character-spellbook-v4 [class~="bg-white/45"],
    .character-spellbook-v4 [class~="bg-white/55"] {
        background-color: color-mix(in srgb, var(--spellbook-paper-strong) 55%, transparent) !important;
    }

    .character-spellbook-v4 [class~="bg-white/10"] {
        background-color: color-mix(in srgb, var(--spellbook-accent) 10%, transparent) !important;
    }

    .character-spellbook-v4 [class~="hover:bg-white"]:hover,
    .character-spellbook-v4 [class~="hover:bg-white/80"]:hover {
        background-color: var(--spellbook-paper-strong) !important;
    }

    .character-spellbook-v4 [class~="hover:bg-white/20"]:hover {
        background-color: color-mix(in srgb, var(--spellbook-paper-strong) 20%, transparent) !important;
    }

    .character-spellbook-v4 [class~="border-white/20"],
    .character-spellbook-v4 [class~="border-white/80"] {
        border-color: var(--spellbook-border-soft) !important;
    }

    .character-spellbook-v4 .text-white {
        color: var(--spellbook-button-text) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_10px_28px_rgba(91,61,41,.09)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_12px_26px_rgba(83,45,30,.14)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_12px_28px_rgba(65,39,25,.18)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_28px_80px_rgba(38,20,14,.38)]"] {
        box-shadow: var(--spellbook-modal-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_2px_10px_rgba(91,61,41,.035)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_2px_5px_rgba(107,29,20,.18)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_2px_7px_rgba(91,61,41,.035)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_2px_7px_rgba(91,61,41,.05)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_2px_8px_rgba(91,61,41,.05)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_3px_10px_rgba(91,61,41,.05)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_3px_10px_rgba(91,61,41,.06)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_3px_9px_rgba(107,29,20,.07)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_3px_9px_rgba(91,61,41,.04)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_4px_10px_rgba(107,29,20,.14)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_4px_10px_rgba(118,33,25,.16)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_4px_10px_rgba(91,61,41,.08)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_4px_11px_rgba(91,61,41,.055)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_4px_12px_rgba(91,61,41,.055)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_5px_12px_rgba(107,29,20,.14)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_5px_12px_rgba(91,61,41,.1)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_5px_16px_rgba(91,61,41,.06)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_5px_16px_rgba(91,61,41,.07)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_6px_14px_rgba(107,29,20,.14)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_6px_18px_rgba(91,61,41,.045)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_6px_18px_rgba(91,61,41,.05)]"] {
        box-shadow: var(--spellbook-modal-control-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_7px_16px_rgba(107,29,20,.16)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[0_7px_16px_rgba(107,29,20,.18)]"] {
        box-shadow: var(--spellbook-accent-shadow) !important;
    }

    .character-spellbook-v4 [class~="shadow-[inset_0_0_0_1px_rgba(139,94,64,.12)]"] {
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--spellbook-border) 42%, transparent) !important;
    }

    .character-spellbook-v4 [class~="shadow-[inset_0_0_0_1px_rgba(139,94,64,.14)]"] {
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--spellbook-border) 42%, transparent) !important;
    }

    .character-spellbook-v4 [class~="shadow-[inset_0_0_0_1px_rgba(141,98,72,.12)]"] {
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--spellbook-border) 42%, transparent) !important;
    }

    .character-spellbook-v4 * {
        scrollbar-width: thin;
        scrollbar-color: color-mix(in srgb, var(--spellbook-accent) 36%, transparent) transparent;
    }

    .character-spellbook-v4 *::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    .character-spellbook-v4 *::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: color-mix(in srgb, var(--spellbook-accent) 30%, transparent);
    }
</style>

<div
    class="character-spellbook-v4 min-h-full"
    x-data="{
        csrf:
            @js(csrf_token()),

        urls:
            @js($spellbookUrls),

        loading:
            true,

        busy:
            false,

        error:
            '',

        notice:
            '',

        spellbookThemeStyle:
            '',

        spellbookThemeObserver:
            null,

        campaigns:
            [],

        campaign:
            null,

        selectedCampaignId:
            null,

        characterContext: {
            level: @js((int) $character->level),
            proficiency_bonus: @js((int) $character->proficiency_bonus),
            classes: [],
            spellcasting_suggestions: [],
        },

        spellSearch:
            '',

        preparedOnly:
            false,

        loadouts:
            [],

        loadoutOpen:
            false,

        loadoutEditingId:
            null,

        loadoutForm: {
            name: '',
            description: '',
            spellcasting_id: '',
        },

        loadoutSearch:
            '',

        loadoutSelectedIds:
            [],

        loadoutHelpOpen:
            false,

        concentrationCheckOpen:
            false,

        concentrationCheck:
            null,

        concentrationRoll:
            null,
        concentrationDieDisplay:
            '—',

        concentrationRolling:
            false,

        concentrationError:
            '',

        concentrationLoading:
            false,

        concentrationManualMode:
            false,

        concentrationManualTotal:
            '',

        profiles:
            [],

        slots:
            [],

        sharedEffectiveCasterLevel:
            0,

        concentration: {
            active: false,
            spell_id: null,
            spell_name: null,
        },

        spells:
            [],

        catalogMeta: {
            count: 0,
            sources: [],
            schools: [],
            lists: [],
        },

        catalogListCodes: [],
        catalogListsInitialized: false,

        catalogOpen:
            false,

        catalogLoading:
            false,

        catalog:
            [],

        catalogSearch:
            '',

        catalogLevel:
            '',

        catalogSchool:
            '',

        catalogSource:
            '',

        catalogProvider:
            '',

        catalogPage: {
            page: 1,
            total: 0,
            last_page: 1,
            has_previous: false,
            has_next: false,
        },

        catalogSelected:
            null,

        catalogProfileId:
            '',

        detailOpen:
            false,

        detailSpell:
            null,

        detailTab:
            'spell',

        detailCastFocus:
            false,

        castConfirmOpen:
            false,

        castConfirmSpell:
            null,

        castProfileId: '',
        noteSaving: false,
        mediaSaving: false,
        noteStatus: '',

        castResource:
            'slot',

        castSlotKey:
            '',

        castLevel:
            1,

        settingsOpen:
            false,

        profileEditingId:
            null,

        profileForm: {
            character_class_id: '',
            sync_with_character_class: false,
            class: '',
            ability: 'intelligence',
            caster_level: 1,
            caster_type: 'full',
            slot_mode: 'automatic',
            effective_caster_level_override: '',
            spell_save_dc_bonus: 0,
            spell_attack_bonus_extra: 0,
            spell_list_codes: [],
            spell_limit: '',
            prepared_limit: '',
            is_active: true,
            sort_order: 0,
            notes: '',
        },

        slotDrafts:
            [],

        init() {
            this.syncSpellbookTheme();
            this.watchSpellbookTheme();

            const stored =
                window.localStorage
                    .getItem(
                        this.campaignStorageKey()
                    );

            if (
                stored
                &&
                /^\d+$/.test(stored)
            ) {
                this.selectedCampaignId =
                    Number(stored);
            }

            window.addEventListener(
                'character-damage-applied',
                event => this.handleSpellConcentrationDamage(event)
            );

            window.addEventListener(
                'character-dice-rolled',
                event => this.handleSpellConcentrationRollResult(event)
            );

            this.loadState();
        },

        syncSpellbookTheme() {
            const host =
                this.$root
                    ?.closest(
                        '.character-sheet-theme'
                    );

            if (!host) {
                this.spellbookThemeStyle = '';
                return;
            }

            const computed =
                window.getComputedStyle(host);

            const names = [
                '--theme-paper',
                '--theme-paper-strong',
                '--theme-paper-alt',
                '--theme-panel',
                '--theme-panel-soft',
                '--theme-band',
                '--theme-band-soft',
                '--theme-hover',
                '--theme-text',
                '--theme-muted',
                '--theme-title',
                '--theme-label',
                '--theme-accent',
                '--theme-accent-strong',
                '--theme-accent-deep',
                '--theme-border',
                '--theme-border-soft',
                '--theme-border-strong',
                '--theme-button-bg',
                '--theme-button-hover',
                '--theme-button-text',
                '--theme-button-soft-bg',
                '--theme-button-soft-text',
                '--theme-input-bg',
                '--theme-input-border',
                '--theme-input-text',
                '--theme-input-placeholder',
                '--theme-input-focus',
                '--theme-shadow',
                '--theme-shadow-strong',
                '--theme-module-shadow',
                '--theme-state-concentration',
                '--theme-text-primary',
                '--theme-text-secondary',
                '--theme-text-strong',
                '--theme-accent-soft',
                '--theme-accent-softer',
                '--theme-divider',
                '--theme-divider-strong',
                '--theme-card-background',
                '--theme-card-alt-background',
                '--theme-module-background',
                '--theme-module-alt-background',
                '--theme-button-background',
                '--theme-button-hover-background',
                '--theme-surface-card',
                '--theme-surface-card-alt',
                '--theme-surface-inset',
                '--theme-surface-raised',
                '--theme-card-hover',
                '--theme-on-accent',
                '--theme-inset-background',
                '--theme-band-background',
                '--theme-state-warning',
                '--theme-state-damage',
                '--theme-state-heal',
                '--theme-modal-backdrop',
                '--theme-modal-surface',
                '--theme-modal-surface-soft',
                '--theme-modal-surface-inset',
                '--theme-modal-header-background',
                '--theme-modal-border',
                '--theme-modal-border-strong',
                '--theme-modal-text',
                '--theme-modal-title',
                '--theme-modal-muted',
                '--theme-modal-label',
                '--theme-modal-shadow',
                '--theme-modal-control-shadow',
                '--theme-font-body',
                '--theme-font-display',
            ];

            this.spellbookThemeStyle =
                names
                    .map(name => {
                        const value =
                            computed
                                .getPropertyValue(name)
                                .trim();

                        return value
                            ? `${name}:${value}`
                            : '';
                    })
                    .filter(Boolean)
                    .join(';');
        },

        watchSpellbookTheme() {
            const host =
                this.$root
                    ?.closest(
                        '.character-sheet-theme'
                    );

            if (
                !host
                ||
                typeof MutationObserver
                ===
                'undefined'
            ) {
                return;
            }

            this.spellbookThemeObserver
                ?.disconnect();

            this.spellbookThemeObserver =
                new MutationObserver(
                    () =>
                        this.syncSpellbookTheme()
                );

            this.spellbookThemeObserver
                .observe(
                    host,
                    {
                        attributes: true,
                        attributeFilter: [
                            'data-theme',
                            'class',
                            'style',
                        ],
                    }
                );
        },

        campaignStorageKey() {
            return @js(
                'spellbound:character:'
                . $character->id
                . ':spellbook-campaign'
            );
        },

        urlWithId(
            template,
            token,
            id
        ) {
            return String(template)
                .replace(
                    token,
                    String(id)
                );
        },

        spellUrl(
            key,
            spellId
        ) {
            return this.urlWithId(
                this.urls[key],
                '__SPELL__',
                spellId
            );
        },

        profileUrl(
            key,
            profileId
        ) {
            return this.urlWithId(
                this.urls[key],
                '__PROFILE__',
                profileId
            );
        },


        loadoutUrl(
            key,
            loadoutId
        ) {
            return this.urlWithId(
                this.urls[key],
                '__LOADOUT__',
                loadoutId
            );
        },

        async request(
            url,
            options = {}
        ) {
            const response =
                await fetch(
                    url,
                    {
                        credentials:
                            'same-origin',

                        ...options,

                        headers: {
                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest',

                            ...(options.headers
                                ?? {}),
                        },
                    }
                );

            const data =
                await response
                    .json()
                    .catch(
                        () => ({})
                    );

            if (!response.ok) {
                const firstValidation =
                    data?.errors
                        ? Object
                            .values(
                                data.errors
                            )
                            .flat()
                            .filter(Boolean)[0]
                        : null;

                throw new Error(
                    firstValidation
                    ??
                    data?.message
                    ??
                    `Falha na requisição (${response.status}).`
                );
            }

            return data;
        },

        jsonOptions(
            method,
            payload = null
        ) {
            return {
                method,

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        this.csrf,
                },

                body:
                    payload === null
                        ? null
                        : JSON.stringify(
                            payload
                        ),
            };
        },

        campaignQuery() {
            return this.selectedCampaignId
                ? `campaign_id=${encodeURIComponent(this.selectedCampaignId)}`
                : '';
        },

        async loadState() {
            this.loading =
                true;

            this.error =
                '';

            try {
                let url =
                    this.urls.state;

                const query =
                    this.campaignQuery();

                if (query) {
                    url +=
                        `?${query}`;
                }

                const data =
                    await this.request(
                        url
                    );

                this.characterContext = {
                    level:
                        Number(
                            data
                                ?.character_context
                                ?.level
                            ?? @js((int) $character->level)
                        ),

                    proficiency_bonus:
                        Number(
                            data
                                ?.character_context
                                ?.proficiency_bonus
                            ?? @js((int) $character->proficiency_bonus)
                        ),

                    classes:
                        Array.isArray(
                            data
                                ?.character_context
                                ?.classes
                        )
                            ? data
                                .character_context
                                .classes
                            : [],

                    spellcasting_suggestions:
                        Array.isArray(
                            data
                                ?.character_context
                                ?.spellcasting_suggestions
                        )
                            ? data
                                .character_context
                                .spellcasting_suggestions
                            : [],
                };

                this.campaigns =
                    Array.isArray(
                        data?.campaigns
                    )
                        ? data.campaigns
                        : [];

                this.campaign =
                    data?.campaign
                    ?? null;

                if (
                    this.campaign?.id
                ) {
                    this.selectedCampaignId =
                        Number(
                            this.campaign.id
                        );

                    window.localStorage
                        .setItem(
                            this.campaignStorageKey(),
                            String(
                                this.selectedCampaignId
                            )
                        );
                } else {
                    this.selectedCampaignId =
                        null;

                    window.localStorage
                        .removeItem(
                            this.campaignStorageKey()
                        );
                }

                this.profiles =
                    Array.isArray(
                        data?.profiles
                    )
                        ? data.profiles
                        : [];

                this.loadouts =
                    Array.isArray(
                        data?.loadouts
                    )
                        ? data.loadouts
                        : [];

                this.slots =
                    Array.isArray(
                        data?.slots
                    )
                        ? data.slots
                        : [];

                this.sharedEffectiveCasterLevel =
                    Number(
                        data
                            ?.shared_effective_caster_level
                        ?? 0
                    );

                this.concentration =
                    data?.concentration
                    ?? {
                        active: false,
                        spell_id: null,
                        spell_name: null,
                    };

                this.spells =
                    Array.isArray(
                        data?.spells
                    )
                        ? data.spells
                        : [];

                this.catalogMeta = {
                    count:
                        Number(
                            data
                                ?.catalog
                                ?.count
                            ?? 0
                        ),

                    sources:
                        Array.isArray(
                            data
                                ?.catalog
                                ?.sources
                        )
                            ? data
                                .catalog
                                .sources
                            : [],

                    schools:
                        Array.isArray(
                            data
                                ?.catalog
                                ?.schools
                        )
                            ? data
                                .catalog
                                .schools
                            : [],
               

                    lists:
                        Array.isArray(
                            data
                                ?.catalog
                                ?.lists
                        )
                            ? data.catalog.lists
                            : [],
                };

                if (!this.catalogListsInitialized) {
                    const storedLists = window.localStorage.getItem(
                        @js('spellbound:character:' . $character->id . ':spell-lists')
                    );

                    if (storedLists) {
                        try {
                            const parsed = JSON.parse(storedLists);
                            this.catalogListCodes = Array.isArray(parsed) ? parsed : [];
                        } catch (_error) {
                            this.catalogListCodes = [];
                        }
                    } else {
                        const fromProfiles = this.profiles
                            .flatMap(profile => Array.isArray(profile.spell_list_codes) ? profile.spell_list_codes : []);

                        const fromSheet = (this.characterContext?.classes ?? [])
                            .flatMap(row => row?.spellcasting_default?.spell_list_codes ?? []);

                        this.catalogListCodes = [...new Set([...fromProfiles, ...fromSheet])];
                    }

                    this.catalogListsInitialized = true;
                }

                this.slotDrafts =
                    this.slots.map(
                        slot => ({
                            ...slot,

                            manual_maximum_input:
                                slot.manual_maximum
                                === null
                                ||
                                slot.manual_maximum
                                === undefined
                                    ? ''
                                    : Number(
                                        slot.manual_maximum
                                    ),

                            current_input:
                                Number(
                                    slot.current
                                    ?? 0
                                ),
                        })
                    );

                if (
                    this.detailSpell
                ) {
                    const refreshed =
                        this.spells.find(
                            row =>
                                Number(row.id)
                                ===
                                Number(
                                    this.detailSpell.id
                                )
                        );

                    if (refreshed) {
                        this.prepareSpellEditor(
                            refreshed
                        );

                        this.detailSpell =
                            refreshed;
                    }
                }

                if (
                    this.castConfirmSpell
                ) {
                    const refreshedCastSpell =
                        this.spells.find(
                            row =>
                                Number(row.id)
                                ===
                                Number(
                                    this.castConfirmSpell.id
                                )
                        );

                    if (refreshedCastSpell) {
                        this.castConfirmSpell =
                            refreshedCastSpell;
                    }
                }
            } catch (error) {
                console.error(
                    'Spellbook state:',
                    error
                );

                this.error =
                    error?.message
                    ??
                    'Não foi possível carregar o Grimório.';
            } finally {
                this.loading =
                    false;
            }
        },

        async changeCampaign() {
            this.catalogSelected =
                null;

            this.catalogSearch =
                '';

            this.catalogLevel =
                '';

            this.catalogSchool =
                '';

            this.catalogSource =
                '';

            this.catalogProvider =
                '';

            await this.loadState();

            if (
                this.catalogOpen
            ) {
                await this.loadCatalog(1);
            }
        },

        normalizedSpellSearch() {
            return String(
                this.spellSearch
                ?? ''
            )
                .trim()
                .toLocaleLowerCase();
        },

        filteredSpells() {
            const search =
                this.normalizedSpellSearch();

            return this.spells.filter(
                spell => {
                    if (
                        this.preparedOnly
                        &&
                        Number(spell.level ?? 0) > 0
                        &&
                        !spell.prepared
                    ) {
                        return false;
                    }

                    if (!search) {
                        return true;
                    }

                    const haystack = [
                        spell.name,
                        spell.school,
                        spell.source,
                        spell.profile_name,
                        spell.casting_time,
                    ]
                        .filter(Boolean)
                        .join(' ')
                        .toLocaleLowerCase();

                    return haystack.includes(
                        search
                    );
                }
            );
        },

        spellLevels() {
            return [
                ...new Set(
                    this.filteredSpells()
                        .map(
                            spell =>
                                Number(
                                    spell.level
                                    ?? 0
                                )
                        )
                ),
            ]
                .sort(
                    (a, b) =>
                        a - b
                );
        },

        spellsAtLevel(
            level
        ) {
            return this.filteredSpells()
                .filter(
                    spell =>
                        Number(
                            spell.level
                        )
                        ===
                        Number(
                            level
                        )
                );
        },

        classSummary() {
            const classes =
                Array.isArray(
                    this.characterContext
                        ?.classes
                )
                    ? this.characterContext.classes
                    : [];

            if (classes.length === 0) {
                return `Nível ${this.characterContext.level}`;
            }

            return classes
                .map(
                    row =>
                        `${row.class} ${row.level}`
                )
                .join(' · ');
        },

        primaryProfile() {
            return this.profiles.find(
                row => row.is_active
            ) ?? this.profiles[0] ?? null;
        },

        cantripCount() {
            return this.spells.filter(
                spell => Number(spell.level ?? 0) === 0
            ).length;
        },

        leveledSpellCount(
            profileId = null
        ) {
            return this.spells.filter(spell => {
                if (Number(spell.level ?? 0) < 1) {
                    return false;
                }

                if (profileId === null) {
                    return true;
                }

                return Number(spell.spellcasting_id ?? 0) === Number(profileId);
            }).length;
        },

        preparedSpellCount(
            profileId = null
        ) {
            return this.spells.filter(spell => {
                if (Number(spell.level ?? 0) < 1) {
                    return false;
                }

                if (!(spell.prepared || spell.always_prepared)) {
                    return false;
                }

                if (profileId === null) {
                    return true;
                }

                return Number(spell.spellcasting_id ?? 0) === Number(profileId);
            }).length;
        },

        isSpellLimitExceeded(
            profile = null
        ) {
            const selected = profile ?? this.primaryProfile();

            if (!selected || selected.spell_limit === null || selected.spell_limit === undefined) {
                return false;
            }

            return this.leveledSpellCount(selected.id) > Number(selected.spell_limit);
        },

        isPreparedLimitExceeded(
            profile = null
        ) {
            const selected = profile ?? this.primaryProfile();

            if (!selected || selected.prepared_limit === null || selected.prepared_limit === undefined) {
                return false;
            }

            return this.preparedSpellCount(selected.id) > Number(selected.prepared_limit);
        },

        listSelected(code) {
            return this.catalogListCodes.includes(String(code));
        },

        toggleCatalogList(code) {
            const value = String(code);

            if (this.catalogListCodes.includes(value)) {
                this.catalogListCodes = this.catalogListCodes.filter(item => item !== value);
            } else {
                this.catalogListCodes = [...this.catalogListCodes, value];
            }

            window.localStorage.setItem(
                @js('spellbound:character:' . $character->id . ':spell-lists'),
                JSON.stringify(this.catalogListCodes)
            );

            if (this.catalogOpen) {
                this.loadCatalog(1);
            }
        },

        clearCatalogLists() {
            this.catalogListCodes = [];
            window.localStorage.setItem(
                @js('spellbound:character:' . $character->id . ':spell-lists'),
                '[]'
            );

            if (this.catalogOpen) {
                this.loadCatalog(1);
            }
        },

        spellListLabels(spell) {
            const lists = Array.isArray(spell?.class_lists) ? spell.class_lists : [];
            return [...new Set(lists.map(row => row.label || row.name).filter(Boolean))];
        },

        availableCastProfiles(spell) {
            const origins = Array.isArray(spell?.casting_profiles)
                ? spell.casting_profiles
                : [];
            if (origins.length > 0) {
                const ids = new Set(origins.filter(origin =>
                    origin.known && origin.is_active
                ).map(origin => Number(origin.id)));
                return this.profiles.filter(profile => profile.is_active && ids.has(Number(profile.id)));
            }
            // Retrocompatibilidade para feitiços antigos sem relações ainda.
            const legacy = this.profileForSpell(spell);
            return legacy?.is_active ? [legacy] : [];
        },

        profileForSpell(
            spell
        ) {
            if (!spell?.spellcasting_id) {
                return null;
            }

            return this.profiles.find(
                profile =>
                    Number(profile.id)
                    ===
                    Number(
                        spell.spellcasting_id
                    )
            ) ?? null;
        },

        levelTitle(
            level
        ) {
            const value =
                Number(level);

            return value === 0
                ? 'Truques'
                : `${value}º círculo`;
        },

        currentCantripScaling(
            spell
        ) {
            if (
                Number(
                    spell?.level
                    ?? 0
                )
                !==
                0
                ||
                !Array.isArray(
                    spell?.scaling_level_dice
                )
            ) {
                return '';
            }

            const characterLevel =
                Number(
                    this.characterContext
                        ?.level
                    ?? 1
                );

            return spell
                .scaling_level_dice
                .map(
                    progression => {
                        const levels =
                            Array.isArray(
                                progression?.levels
                            )
                                ? progression.levels
                                : [];

                        const current =
                            [...levels]
                                .filter(
                                    row =>
                                        Number(
                                            row.level
                                            ?? 0
                                        )
                                        <=
                                        characterLevel
                                )
                                .sort(
                                    (a, b) =>
                                        Number(
                                            b.level
                                            ?? 0
                                        )
                                        -
                                        Number(
                                            a.level
                                            ?? 0
                                        )
                                )[0];

                        if (!current) {
                            return '';
                        }

                        const label =
                            String(
                                progression?.label
                                ?? ''
                            )
                                .trim();

                        return label
                            ? `${label}: ${current.value}`
                            : String(
                                current.value
                                ?? ''
                            );
                    }
                )
                .filter(Boolean)
                .join(' · ');
        },

        // V20: visualização conservadora, baseada no texto de regras recebido do servidor.
        // Não altera a rolagem, as classes ou o consumo de slots.
        castPlainText(value) {
            return String(value ?? '')
                .replace(/<[^>]*>/g, ' ')
                .replace(/\{@[^ ]+\s+([^}|]+)[^}]*\}/g, '$1')
                .replace(/\*\*|__/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        },

        castDiceGroups(text) {
            const groups = [];
            const rx = /\b\d+d\d+(?:\s*[+\-]\s*\d+)?\b/ig;
            const clean = this.castPlainText(text);
            for (const match of clean.matchAll(rx)) {
                const value = match[0].replace(/\s*([+\-])\s*/g, ' $1 ').trim();
                if (!groups.includes(value)) groups.push(value);
            }
            return groups;
        },

        castDiceParts(expression) {
            const match = String(expression ?? '').match(/^(\d+)d(\d+)(?:\s*([+\-])\s*(\d+))?$/i);
            if (!match) return null;
            return {
                count: Number(match[1]),
                faces: Number(match[2]),
                mod: match[4] ? (match[3] === '-' ? -Number(match[4]) : Number(match[4])) : 0,
            };
        },

        castDiceText(parts) {
            if (!parts) return '';
            return `${parts.count}d${parts.faces}`
                + (parts.mod > 0 ? ` + ${parts.mod}` : parts.mod < 0 ? ` - ${Math.abs(parts.mod)}` : '');
        },

        castUpcastPreview(spell) {
            const initial = Number(spell?.level ?? 0);
            const selected = Number(this.castLevel ?? initial);
            const text = this.castPlainText(spell?.higher_level_text ?? '');
            if (!spell || initial === 0 || selected <= initial || !text) {
                return { active: false, shortText: '', description: '', increment: null };
            }
            // Só fazemos conta quando o texto declara explicitamente o incremento POR nível.
            const incrementMatch = text.match(
                /(?:damage|dano)[^.]{0,95}?(?:increas(?:es|ed?)\s+by|aumenta\s+em)\s+(\d+d\d+(?:\s*[+\-]\s*\d+)?)[^.]{0,90}?(?:each|every|per|cada)\s+(?:spell\s+slot\s+)?(?:slot\s+)?(?:level|nível|circulo|círculo)/iu
            );
            const increment = incrementMatch ? this.castDiceParts(incrementMatch[1]) : null;
            const shortText = increment
                ? `+${this.castDiceText(increment)} / círculo acima`
                : text.length > 94 ? text.slice(0, 91).trimEnd() + '…' : text;
            return { active: true, shortText, description: text, increment };
        },

        // V21: cada opção de dano mantém a fórmula, seu tipo e a condição
        // descrita pela magia. Não escolhemos uma condição do alvo sem seus PV.
        castDamageTypeLabels(spell) {
            const names = {
                acid: 'Ácido', cold: 'Frio', fire: 'Fogo', force: 'Energia',
                lightning: 'Elétrico', necrotic: 'Necrótico', poison: 'Veneno',
                psychic: 'Psíquico', radiant: 'Radiante', thunder: 'Trovejante',
                bludgeoning: 'Concussão', piercing: 'Perfurante', slashing: 'Cortante',
            };
            const data = spell?.damage_types;
            const input = Array.isArray(data) ? data :
                typeof data === 'string' ? (() => {
                    try { const parsed = JSON.parse(data); return Array.isArray(parsed) ? parsed : [data]; }
                    catch (_) { return [data]; }
                })() : [];
            return [...new Set(input.map(type => names[String(type).toLowerCase().trim()] ??
                (Object.values(names).includes(type) ? type : '')).filter(Boolean))];
        },

        castDamageTypeFromContext(before, after) {
            const types = {
                'acid': 'Ácido', 'ácido': 'Ácido', 'cold': 'Frio', 'frio': 'Frio',
                'fire': 'Fogo', 'fogo': 'Fogo', 'force': 'Energia', 'energia': 'Energia',
                'lightning': 'Elétrico', 'elétrico': 'Elétrico', 'electric': 'Elétrico',
                'necrotic': 'Necrótico', 'necrótico': 'Necrótico',
                'poison': 'Veneno', 'veneno': 'Veneno',
                'psychic': 'Psíquico', 'psíquico': 'Psíquico',
                'radiant': 'Radiante', 'radiante': 'Radiante',
                'thunder': 'Trovejante', 'trovejante': 'Trovejante',
                'bludgeoning': 'Concussão', 'concussão': 'Concussão',
                'piercing': 'Perfurante', 'perfurante': 'Perfurante',
                'slashing': 'Cortante', 'cortante': 'Cortante',
            };
            const pattern = new RegExp('\\b(' + Object.keys(types).filter(x => !/[^a-z]/.test(x)).join('|') + ')\\b', 'gi');
            // O tipo junto DEPOIS do dado tem precedência: 1d8 Necrotic damage.
            const next = (after.slice(0, 54).match(pattern) ?? [])[0];
            if (next) return types[next.toLowerCase()] ?? '';
            const previous = [...before.slice(-38).matchAll(pattern)].pop()?.[0];
            if (previous) return types[previous.toLowerCase()] ?? '';
            // Termos portugueses com acento não passam por \b em JS.
            const accented = /(?:ácido|elétrico|necrótico|psíquico|concussão)/iu;
            const later = after.slice(0,54).match(accented)?.[0];
            const earlier = before.slice(-38).match(accented)?.[0];
            return types[(later || earlier || '').toLowerCase()] ?? '';
        },

        castDamageCondition(before) {
            const sentence = String(before).split(/(?<=[.!?])\s+/u).pop() || '';
            const value = sentence.toLowerCase();
            if (/(?:missing any of its hit points|lost (?:any|some) hit points|below (?:its )?(?:hit point )?maximum|already (?:damaged|wounded)|faltar? (?:algum|pontos?)|perdeu pontos? de vida|estiver ferid)/iu.test(value)) {
                return 'Alvo ferido';
            }
            if (/(?:if|when|se|quando)[^.!?]{0,100}(?:move|moves|moving|mover|moviment)/iu.test(value)) {
                return 'Se o alvo se mover';
            }
            if (/(?:another creature|second creature|other creature|outra criatura|segundo alvo)/iu.test(value)) {
                return 'Outro alvo';
            }
            if (/(?:if|when|se|quando)[^.!?]{0,95}(?:enter(?:s|ed)?|start(?:s)? (?:its|their) turn|entrar|começar o turno)/iu.test(value)) {
                return 'Ao entrar ou iniciar turno';
            }
            return '';
        },

        castScaleCantripDamage(expression, spell) {
            if (Number(spell?.level ?? 0) !== 0) return expression;
            const original = this.castDiceParts(expression);
            if (!original) return expression;
            const rows = Array.isArray(spell?.scaling_level_dice) ? spell.scaling_level_dice : [];
            const level = Number(this.characterContext?.level ?? 1);
            const progressions = [];
            for (const row of rows) {
                const levels = Array.isArray(row?.levels) ? row.levels : [];
                const baseline = [...levels].sort((a,b) => Number(a.level ?? 0) - Number(b.level ?? 0))[0];
                const active = [...levels].filter(l => Number(l.level ?? 0) <= level)
                    .sort((a,b) => Number(b.level ?? 0) - Number(a.level ?? 0))[0];
                const start = this.castDiceParts(this.castDiceGroups(baseline?.value ?? '')[0]);
                const now = this.castDiceParts(this.castDiceGroups(active?.value ?? '')[0]);
                if (start && now && start.faces === now.faces) progressions.push({start,now});
            }
            // Procura a progressão do mesmo dado. Se a regra permite outro dado
            // (d8 OU d12), herda APENAS a quantidade de dados, nunca as faces.
            const match = progressions.find(p => p.start.faces === original.faces && p.start.count === original.count)
                ?? (progressions.length === 1 && progressions[0].start.count === original.count ? progressions[0] : null);
            if (match) {
                return this.castDiceText({...original, count: original.count + match.now.count - match.start.count});
            }
            // Fallback somente quando o texto declara explicitamente aumento de 1 dado
            // nos níveis dos truques. Não assume progressão para efeitos arbitrários.
            const upgrade = this.castPlainText([spell?.description,spell?.higher_level_text].filter(Boolean).join(' '));
            if (/(?:damage increases by one die|dano aumenta em um dado|cantrip upgrade|aprimoramento do truque)/iu.test(upgrade)) {
                const steps = level >= 17 ? 3 : level >= 11 ? 2 : level >= 5 ? 1 : 0;
                return this.castDiceText({...original, count: original.count + steps});
            }
            return expression;
        },

        castDamageOptions(spell) {
            if (!spell) return [];
            const plain = this.castPlainText(spell.description ?? '');
            const damageWord = /(?:damage|dano|damages|danos)/iu;
            if (!damageWord.test(plain)) return [];
            const typeList = this.castDamageTypeLabels(spell);
            // Separa a descrição básica do texto sobre evolução do truque.
            // Esse texto contém dados históricos (2d8, 3d8...) que NÃO são
            // ataques adicionais nem devem aparecer como danos distintos.
            const cut = plain.search(/(?:cantrip upgrade|at higher levels|aprimoramento do truque|em níveis superiores)/iu);
            const source = cut >= 0 ? plain.slice(0, cut) : plain;
            const matches = [...source.matchAll(/\b\d+d\d+(?:\s*[+\-]\s*\d+)?\b/gi)];
            const options = [];
            for (const match of matches) {
                const start = match.index;
                const end = start + match[0].length;
                const before = source.slice(Math.max(0, start-125), start);
                const after = source.slice(end, Math.min(source.length, end+72));
                const type = this.castDamageTypeFromContext(before,after);
                const nearby = (before.slice(-40) + ' ' + after.slice(0,65));
                if (!type && !damageWord.test(nearby)) continue;
                const condition = this.castDamageCondition(before);
                const dice = Number(spell.level ?? 0) === 0
                    ? this.castScaleCantripDamage(match[0].replace(/\s*([+\-])\s*/g,' $1 '),spell)
                    : match[0].replace(/\s*([+\-])\s*/g,' $1 ');
                const qualifier = /\b(?:each|every)\s+(?:dart|missile)\b|\b(?:cada)\s+(?:dardo|projétil)/iu.test(before+' '+after)
                    ? 'Por projétil' : '';
                const outputType = type || (typeList.length === 1 ? typeList[0] : typeList.join(' / '));
                const item = { dice, type:outputType, condition, qualifier };
                if (!options.some(row => row.dice === dice && row.type === outputType && row.condition === condition && row.qualifier === qualifier)) {
                    options.push(item);
                }
                if (options.length >= 5) break;
            }
            // Texto de regra que define alternativa apenas para um alvo ferido:
            // a primeira linha é a situação de PV completos.
            if (options.length === 2 && options[1].condition === 'Alvo ferido' && !options[0].condition) {
                options[0].condition = 'Alvo com PV completos';
            }
            // Upcast aritmético seguro apenas quando há UMA fórmula de dano.
            // Em magias com diferentes componentes, mantém cada valor original.
            if (options.length === 1 && Number(spell.level ?? 0) > 0) {
                const upcast = this.castUpcastPreview(spell);
                const initial = Number(spell.level ?? 0);
                const base = this.castDiceParts(options[0].dice);
                if (upcast.active && upcast.increment && base && base.faces === upcast.increment.faces && !options[0].qualifier) {
                    const steps = Math.max(0,Number(this.castLevel)-initial);
                    options[0].dice = this.castDiceText({count:base.count+steps*upcast.increment.count,faces:base.faces,mod:base.mod+steps*upcast.increment.mod});
                }
            }
            return options;
        },

        // Contrato anterior mantido para qualquer outro componente que use o método.
        castDamagePreview(spell) {
            const first = this.castDamageOptions(spell)[0];
            return first ? {dice:first.dice,label:'Dano',qualifier:first.condition || first.qualifier}
                : {dice:'',label:'Dano',qualifier:''};
        },

        isCurrentCantripScalingRow(
            progression,
            row
        ) {
            const characterLevel =
                Number(
                    this.characterContext
                        ?.level
                    ?? 1
                );

            const levels =
                Array.isArray(
                    progression?.levels
                )
                    ? progression.levels
                    : [];

            const current =
                [...levels]
                    .filter(
                        item =>
                            Number(
                                item.level
                                ?? 0
                            )
                            <=
                            characterLevel
                    )
                    .sort(
                        (a, b) =>
                            Number(
                                b.level
                                ?? 0
                            )
                            -
                            Number(
                                a.level
                                ?? 0
                            )
                    )[0];

            return !!current
                &&
                Number(current.level)
                ===
                Number(row?.level);
        },

        casterTypeLabel(
            type
        ) {
            return {
                full:
                    'Conjurador completo',

                half:
                    'Meio-conjurador',

                half_up:
                    'Meio-conjurador (arred. cima)',

                third:
                    '1/3 conjurador',

                pact:
                    'Pact Magic',

                custom:
                    'Personalizado',
            }[type]
            ?? type
            ?? '—';
        },

        abilityLabel(
            ability
        ) {
            return {
                strength:
                    'Força',

                dexterity:
                    'Destreza',

                constitution:
                    'Constituição',

                intelligence:
                    'Inteligência',

                wisdom:
                    'Sabedoria',

                charisma:
                    'Carisma',
            }[ability]
            ?? ability
            ?? '—';
        },

        formatBonus(
            value
        ) {
            const number =
                Number(
                    value
                    ?? 0
                );

            return number >= 0
                ? `+${number}`
                : String(number);
        },

        slotLabel(
            slot
        ) {
            if (
                String(
                    slot.slot_type
                ).startsWith(
                    'pact:'
                )
            ) {
                const profileId =
                    Number(
                        String(
                            slot.slot_type
                        )
                            .split(':')[1]
                    );

                const profile =
                    this.profiles
                        .find(
                            row =>
                                Number(
                                    row.id
                                )
                                ===
                                profileId
                        );

                return `${
                    profile?.class
                    ?? 'Pacto'
                } · ${slot.slot_level}º`;
            }

            return `${slot.slot_level}º`;
        },

        slotRecoveryLabel(
            recovery
        ) {
            return {
                short_rest:
                    'Descanso curto',

                long_rest:
                    'Descanso longo',

                day:
                    'Dia',

                manual:
                    'Manual',
            }[recovery]
            ?? recovery
            ?? '—';
        },

        pactProfileIsActive(
            slot
        ) {
            if (
                !String(
                    slot?.slot_type
                    ?? ''
                )
                    .startsWith(
                        'pact:'
                    )
            ) {
                return true;
            }

            const profileId =
                Number(
                    String(
                        slot.slot_type
                    )
                        .split(':')[1]
                );

            return this.profiles
                .some(
                    profile =>
                        Number(
                            profile.id
                        )
                        ===
                        profileId
                        &&
                        !!profile.is_active
                );
        },

        displaySlots() {
            return this.slots.filter(
                slot =>
                    Number(
                        slot.maximum
                        ?? 0
                    )
                    > 0
                    &&
                    this.pactProfileIsActive(
                        slot
                    )
            );
        },

        settingsSlots() {
            const hasManualStandard =
                this.profiles.some(
                    profile =>
                        !!profile.is_active
                        &&
                        profile.caster_type
                        !==
                        'pact'
                        &&
                        (
                            profile.slot_mode
                            ===
                            'manual'
                            ||
                            profile.caster_type
                            ===
                            'custom'
                        )
                );

            return this.slotDrafts.filter(
                slot => {
                    const isPact =
                        String(
                            slot.slot_type
                            ?? ''
                        )
                            .startsWith(
                                'pact:'
                            );

                    const hasValue =
                        Number(
                            slot.maximum
                            ?? 0
                        )
                        >
                        0
                        ||
                        Number(
                            slot.calculated_maximum
                            ?? 0
                        )
                        >
                        0
                        ||
                        slot.manual_maximum
                        !==
                        null;

                    if (!isPact) {
                        return hasManualStandard
                            ||
                            hasValue;
                    }

                    const profileId =
                        Number(
                            String(
                                slot.slot_type
                            )
                                .split(':')[1]
                        );

                    const profile =
                        this.profiles.find(
                            row =>
                                Number(
                                    row.id
                                )
                                ===
                                profileId
                        );

                    if (
                        !profile
                        ||
                        !profile.is_active
                    ) {
                        return false;
                    }

                    return profile.slot_mode
                        ===
                        'manual'
                            ? true
                            : hasValue;
                }
            );
        },

        standardSlots() {
            return this.slots.filter(
                slot =>
                    slot.slot_type
                    ===
                    'spell'
            );
        },

        pactSlots() {
            return this.slots.filter(
                slot =>
                    String(
                        slot.slot_type
                    )
                        .startsWith(
                            'pact:'
                        )
            );
        },

        availableSlotsFor(
            spell
        ) {
            const minimum =
                Math.max(
                    1,
                    Number(
                        spell?.level
                        ?? 1
                    )
                );

            return this.slots
                .filter(
                    slot =>
                        Number(slot.current ?? 0) > 0
                        &&
                        Number(slot.maximum ?? 0) > 0
                        &&
                        this.pactProfileIsActive(
                            slot
                        )
                        &&
                        Number(
                            slot.slot_level
                            ?? 0
                        )
                        >=
                        minimum
                )
                .sort(
                    (a, b) =>
                        Number(
                            a.slot_level
                        )
                        -
                        Number(
                            b.slot_level
                        )
                );
        },

        firstAvailableSlotFor(
            spell
        ) {
            return this
                .availableSlotsFor(
                    spell
                )
                .find(
                    slot =>
                        Number(
                            slot.current
                            ?? 0
                        )
                        > 0
                )
                ??
                this
                    .availableSlotsFor(
                        spell
                    )[0]
                ??
                null;
        },

        makeSlotKey(
            slot
        ) {
            return `${slot.slot_type}|${slot.slot_level}`;
        },

        parseSlotKey(
            key
        ) {
            const [
                slot_type,
                level,
            ] =
                String(
                    key
                    ?? ''
                )
                    .split('|');

            return {
                slot_type:
                    slot_type
                    || 'spell',

                slot_level:
                    Number(
                        level
                        || 0
                    ),
            };
        },

        sourceBadge(
            spell
        ) {
            if (
                spell?.kind
                ===
                'homebrew'
            ) {
                return 'Homebrew';
            }

            if (
                spell?.kind
                ===
                'custom'
            ) {
                return 'Custom';
            }

            return spell?.rules_version
                ===
                '2024'
                    ? '2024'
                    : 'Padrão';
        },

        sourceBadgeClass(
            spell
        ) {
            if (
                spell?.kind
                ===
                'homebrew'
            ) {
                return 'bg-amber-700/15 text-amber-950 border-amber-800/20';
            }

            if (
                spell?.kind
                ===
                'custom'
            ) {
                return 'bg-violet-700/10 text-violet-900 border-violet-800/20';
            }

            return 'bg-emerald-800/10 text-emerald-900 border-emerald-800/20';
        },

        showNotice(
            message
        ) {
            this.notice =
                message
                ?? '';

            if (
                this.notice
            ) {
                window.setTimeout(
                    () => {
                        if (
                            this.notice
                            ===
                            message
                        ) {
                            this.notice =
                                '';
                        }
                    },
                    3500
                );
            }
        },

        async openCatalog() {
            this.catalogOpen =
                true;

            this.catalogSelected =
                null;

            if (
                !this.catalogProfileId
                &&
                this.profiles.length
                ===
                1
            ) {
                this.catalogProfileId =
                    String(
                        this.profiles[0].id
                    );
            }

            await this.loadCatalog(1);
        },

        closeCatalog() {
            this.catalogOpen =
                false;

            this.catalogSelected =
                null;
        },

        async loadCatalog(
            page = 1
        ) {
            this.catalogLoading =
                true;

            this.error =
                '';

            try {
                const params =
                    new URLSearchParams();

                if (
                    this.selectedCampaignId
                ) {
                    params.set(
                        'campaign_id',
                        String(
                            this.selectedCampaignId
                        )
                    );
                }

                if (
                    this.catalogSearch
                        .trim()
                ) {
                    params.set(
                        'q',
                        this.catalogSearch
                            .trim()
                    );
                }

                if (
                    this.catalogLevel
                    !==
                    ''
                ) {
                    params.set(
                        'level',
                        String(
                            this.catalogLevel
                        )
                    );
                }

                if (
                    this.catalogSchool
                ) {
                    params.set(
                        'school',
                        this.catalogSchool
                    );
                }

                if (
                    this.catalogSource
                ) {
                    params.set(
                        'source',
                        this.catalogSource
                    );
                }

                if (
                    this.catalogProvider
                ) {
                    params.set(
                        'provider',
                        this.catalogProvider
                    );
                }

                this.catalogListCodes.forEach(
                    code => params.append('lists[]', code)
                );

                params.set(
                    'page',
                    String(page)
                );

                params.set(
                    'per_page',
                    '40'
                );

                const data =
                    await this.request(
                        `${
                            this.urls.catalog
                        }?${
                            params.toString()
                        }`
                    );

                this.catalog =
                    Array.isArray(
                        data?.data
                    )
                        ? data.data
                        : [];

                this.catalogPage = {
                    page:
                        Number(
                            data
                                ?.pagination
                                ?.page
                            ?? 1
                        ),

                    total:
                        Number(
                            data
                                ?.pagination
                                ?.total
                            ?? 0
                        ),

                    last_page:
                        Number(
                            data
                                ?.pagination
                                ?.last_page
                            ?? 1
                        ),

                    has_previous:
                        !!data
                            ?.pagination
                            ?.has_previous,

                    has_next:
                        !!data
                            ?.pagination
                            ?.has_next,
                };

                if (
                    this.catalogSelected
                ) {
                    const selected =
                        this.catalog
                            .find(
                                row =>
                                    Number(
                                        row.id
                                    )
                                    ===
                                    Number(
                                        this
                                            .catalogSelected
                                            .id
                                    )
                            );

                    if (selected) {
                        this.catalogSelected =
                            selected;
                    }
                }

                if (
                    !this.catalogSelected
                    &&
                    this.catalog.length
                    > 0
                ) {
                    this.catalogSelected =
                        this.catalog[0];
                }
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível carregar o catálogo.';
            } finally {
                this.catalogLoading =
                    false;
            }
        },

        resetCatalogFilters() {
            this.catalogSearch =
                '';

            this.catalogLevel =
                '';

            this.catalogSchool =
                '';

            this.catalogSource =
                '';

            this.catalogProvider =
                '';

            this.loadCatalog(1);
        },

        async addCatalogSelected() {
            const spell =
                this.catalogSelected;

            if (
                !spell
                ||
                spell.in_spellbook
                ||
                this.busy
            ) {
                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const payload = {
                    campaign_id:
                        this.selectedCampaignId
                        ?? null,

                    spellcasting_id:
                        this.catalogProfileId
                            ? Number(
                                this.catalogProfileId
                            )
                            : null,
                };

                const data =
                    await this.request(
                        this.spellUrl(
                            'spellStore',
                            spell.id
                        ),
                        this.jsonOptions(
                            'POST',
                            payload
                        )
                    );

                const profile =
                    payload.spellcasting_id
                        ? this.profiles.find(
                            row =>
                                Number(row.id)
                                ===
                                Number(payload.spellcasting_id)
                        )
                        : null;

                const localSpell = {
                    ...spell,
                    spellcasting_id:
                        payload.spellcasting_id,
                    profile_name:
                        profile?.class
                        ?? null,
                    source_type:
                        profile
                            ? 'class'
                            : 'catalog',
                    source_class:
                        profile?.class
                        ?? null,
                    known:
                        true,
                    prepared:
                        false,
                    prepared_manual:
                        false,
                    always_prepared:
                        false,
                    free_casts_current:
                        null,
                    free_casts_max:
                        null,
                    free_casts_recovery:
                        null,
                    cast_without_slot:
                        false,
                    allowed_in_campaign:
                        true,
                };

                spell.in_spellbook =
                    true;

                this.catalog =
                    this.catalog.map(
                        row =>
                            Number(row.id)
                            ===
                            Number(spell.id)
                                ? {
                                    ...row,
                                    in_spellbook:
                                        true,
                                }
                                : row
                    );

                if (
                    !this.spells.some(
                        row =>
                            Number(row.id)
                            ===
                            Number(spell.id)
                    )
                ) {
                    this.spells = [
                        ...this.spells,
                        localSpell,
                    ].sort(
                        (a, b) => {
                            const level =
                                Number(a.level ?? 0)
                                -
                                Number(b.level ?? 0);

                            if (level !== 0) {
                                return level;
                            }

                            return String(
                                a.name
                                ?? ''
                            ).localeCompare(
                                String(
                                    b.name
                                    ?? ''
                                ),
                                'pt-BR'
                            );
                        }
                    );
                }

                this.showNotice(
                    data?.message
                    ??
                    'Feitiço adicionado.'
                );
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível adicionar o feitiço.';
            } finally {
                this.busy =
                    false;
            }
        },

        prepareCast(
            spell
        ) {
            if (!spell) {
                return;
            }
            const options = this.availableCastProfiles(spell);
            this.castProfileId = options.length === 1
                ? String(options[0].id)
                : (options.some(row => String(row.id) === String(this.castProfileId))
                    ? this.castProfileId : '');

            const firstSlot =
                this.firstAvailableSlotFor(
                    spell
                );

            if (
                Number(
                    spell.level
                    ?? 0
                )
                ===
                0
            ) {
                this.castResource =
                    'none';

                this.castLevel =
                    0;

                this.castSlotKey =
                    '';

                return;
            }

            if (spell.cast_without_slot) {
                this.castResource =
                    'none';

                this.castLevel =
                    Number(spell.level ?? 0);

                this.castSlotKey =
                    '';

                return;
            }

            if (
                Number(
                    spell
                        .free_casts_current
                    ?? 0
                )
                > 0
            ) {
                this.castResource =
                    'free';

                this.castLevel =
                    Number(
                        spell.level
                    );

                this.castSlotKey =
                    '';

                return;
            }

            if (firstSlot) {
                this.castResource =
                    'slot';

                this.castSlotKey =
                    this.makeSlotKey(
                        firstSlot
                    );

                this.castLevel =
                    Number(
                        firstSlot
                            .slot_level
                    );

                return;
            }

            this.castResource =
                'slot';

            this.castLevel =
                Number(
                    spell.level
                );

            this.castSlotKey =
                '';
        },

        openSpell(
            spell,
            castFocus = false
        ) {
            this.prepareSpellEditor(
                spell
            );

            this.detailSpell =
                spell;
            this.noteStatus = '';

            this.detailTab =
                'spell';

            this.detailCastFocus =
                !!castFocus;

            this.detailOpen =
                true;

            this.prepareCast(
                spell
            );
        },

        async openCastConfirm(spell) {
            if (!spell || this.busy) return;

            // Atualiza o estado antes de confirmar: feitiços e formas
            // compartilham a mesma concentração da ficha original.
            try {
                const url = this.urls.state
                    + (this.urls.state.includes('?') ? '&' : '?')
                    + 'scope=concentration';
                const state = await this.request(url);
                this.concentration = state?.concentration ?? { active: false };
            } catch (error) {
                this.error = error?.message || 'Não foi possível conferir a concentração ativa.';
                return;
            }

            this.castConfirmSpell = spell;
            this.castProfileId = '';
            this.prepareCast(spell);
            this.castConfirmOpen = true;
        },

        closeCastConfirm() {
            this.castConfirmOpen =
                false;

            this.castConfirmSpell =
                null;
        },

        castConfirmProfile() {
            const profiles = this.availableCastProfiles(this.castConfirmSpell);
            return profiles.find(row => String(row.id) === String(this.castProfileId))
                ?? (profiles.length === 1 ? profiles[0] : null);
        },

        canCastSelectedProfile() {
            const spell = this.castConfirmSpell;
            const profile = this.castConfirmProfile();
            if (!spell || Number(spell.level ?? 0) === 0) return true;
            if (this.castResource === 'free' ||
                (this.castResource === 'none' && spell.cast_without_slot)) return true;
            const origins = Array.isArray(spell.casting_profiles) ? spell.casting_profiles : [];
            if (origins.length === 0) return !!profile && !!spell.prepared;
            if (!profile) return false;
            const origin = origins.find(row => Number(row.id) === Number(profile.id));
            return !!origin && !!(origin.prepared || origin.always_prepared);
        },

        castWillReplaceConcentration() {
            // Re-conjurar o MESMO feitiço também substitui a concentração.
            return !!this.castConfirmSpell?.concentration
                && !!this.concentration?.active;
        },

        concentrationConflictLabel() {
            return String(this.concentration?.effect_name
                || this.concentration?.spell_name
                || 'o efeito anterior');
        },

        closeSpell() {
            this.detailOpen =
                false;

            this.detailSpell =
                null;

            this.detailTab =
                'spell';

            this.detailCastFocus =
                false;
        },

        changeCastSlot() {
            const parsed =
                this.parseSlotKey(
                    this.castSlotKey
                );

            if (
                parsed.slot_level
                > 0
            ) {
                this.castLevel =
                    parsed.slot_level;
            }
        },

        applySpellPatchLocal(
            spell,
            payload
        ) {
            if (!spell) {
                return;
            }

            const targets =
                [
                    spell,
                    this.spells.find(
                        row =>
                            Number(row.id)
                            ===
                            Number(spell.id)
                    ),
                    (
                        this.detailSpell
                        &&
                        Number(this.detailSpell.id)
                        ===
                        Number(spell.id)
                    )
                        ? this.detailSpell
                        : null,
                    (
                        this.castConfirmSpell
                        &&
                        Number(this.castConfirmSpell.id)
                        ===
                        Number(spell.id)
                    )
                        ? this.castConfirmSpell
                        : null,
                ]
                    .filter(Boolean)
                    .filter(
                        (
                            row,
                            index,
                            array
                        ) =>
                            array.indexOf(row)
                            ===
                            index
                    );

            targets.forEach(target => {
                if (
                    Object.prototype.hasOwnProperty.call(
                        payload,
                        'prepared'
                    )
                ) {
                    target.prepared_manual =
                        !!payload.prepared;
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        payload,
                        'always_prepared'
                    )
                ) {
                    target.always_prepared =
                        !!payload.always_prepared;
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        payload,
                        'known'
                    )
                ) {
                    target.known =
                        !!payload.known;
                }

                if (
                    Object.prototype.hasOwnProperty.call(
                        payload,
                        'spellcasting_id'
                    )
                ) {
                    const profileId =
                        payload.spellcasting_id
                        === null
                        ||
                        payload.spellcasting_id
                        === ''
                            ? null
                            : Number(
                                payload.spellcasting_id
                            );

                    const profile =
                        profileId
                            ? this.profiles.find(
                                row =>
                                    Number(row.id)
                                    ===
                                    profileId
                            )
                            : null;

                    target.spellcasting_id =
                        profileId;

                    target.profile_name =
                        profile?.class
                        ?? null;

                    target.source_class =
                        profile?.class
                        ?? null;

                    target.source_type =
                        profile
                            ? 'class'
                            : 'catalog';
                }

                [
                    'free_casts_max',
                    'free_casts_current',
                    'free_casts_recovery',
                ].forEach(field => {
                    if (
                        Object.prototype.hasOwnProperty.call(
                            payload,
                            field
                        )
                    ) {
                        target[field] =
                            payload[field];
                    }
                });

                if (
                    Object.prototype.hasOwnProperty.call(
                        payload,
                        'cast_without_slot'
                    )
                ) {
                    target.cast_without_slot =
                        !!payload.cast_without_slot;
                }

                if (
                    Number(
                        target.level
                        ?? 0
                    )
                    > 0
                ) {
                    target.prepared =
                        !!(
                            target.prepared_manual
                            ||
                            target.always_prepared
                        );
                } else {
                    target.prepared =
                        false;
                }

                target.free_casts_max_input =
                    target.free_casts_max
                    ?? '';

                target.free_casts_current_input =
                    target.free_casts_current
                    ?? '';

                target.free_casts_recovery_input =
                    target.free_casts_recovery
                    ?? '';

                target.cast_without_slot_input =
                    !!target.cast_without_slot;
            });
        },

        async saveSpellNotes(spell) {
            if (!spell?.character_spell_id) return;
            if (spell._noteSaving) return;
            spell._noteSaving = true;
            spell._noteStatus = 'Salvando…';
            try {
                for (let attempt = 0; attempt < 6; attempt++) {
                    const snapshot = String(spell.personal_notes ?? '');
                    await this.request(
                        this.spellUrl('spellUpdate', spell.id),
                        this.jsonOptions('PATCH', { personal_notes: snapshot })
                    );
                    if (snapshot === String(spell.personal_notes ?? '')) {
                        spell._noteStatus = 'Salvo';
                        return;
                    }
                }
                spell._noteStatus = 'Salvando…';
            } catch (error) {
                spell._noteStatus = 'Erro ao salvar';
                this.error = error?.message || 'Não foi possível salvar a anotação.';
            } finally {
                spell._noteSaving = false;
            }
        },

        async updateSpellCastingProfiles(spell, id, checked) {
            if (!spell || this.busy) return;
            const existing = (spell.casting_profiles || []).map(row => Number(row.id));
            const ids = checked ? [...new Set([...existing, Number(id)])]
                : existing.filter(value => value !== Number(id));
            this.busy = true;
            try {
                await this.request(this.spellUrl('spellUpdate', spell.id),
                    this.jsonOptions('PATCH', { casting_profile_ids: ids }));
                // Recarrega apenas os dados do grimório após alteração estrutural.
                await this.loadState();
                const fresh = this.spells.find(row => Number(row.id) === Number(spell.id));
                if (fresh) this.detailSpell = fresh;
            } catch (error) {
                this.error = error?.message || 'Não foi possível vincular o perfil.';
            } finally { this.busy = false; }
        },

        async updateSpellOriginPreparation(spell, profileId, status) {
            if (!spell || this.busy) return;
            this.busy = true;
            try {
                await this.request(this.spellUrl('spellUpdate', spell.id),
                    this.jsonOptions('PATCH', { origin_status: {
                        spellcasting_id: Number(profileId), prepared: !!status,
                    } }));
                const origin = (spell.casting_profiles || []).find(row => Number(row.id) === Number(profileId));
                if (origin) origin.prepared = !!status;
                await this.loadState();
                const fresh = this.spells.find(row => Number(row.id) === Number(spell.id));
                if (fresh) this.detailSpell = fresh;
            } catch (error) {
                this.error = error?.message || 'Não foi possível mudar a preparação.';
            } finally { this.busy = false; }
        },

        async uploadSpellMedia(spell, event) {
            const file = event?.target?.files?.[0];
            if (!spell || !file || this.mediaSaving) return;
            const accepted = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!accepted.includes(file.type) || file.size > 10 * 1024 * 1024) {
                spell._mediaStatus = 'Use JPG, PNG, WebP ou GIF de até 10 MB.';
                if (event?.target) event.target.value = '';
                return;
            }
            this.mediaSaving = true;
            spell._mediaStatus = 'Enviando imagem...';
            try {
                const form = new FormData();
                form.append('media', file);
                const data = await this.request(this.spellUrl('spellMediaStore', spell.id), {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf }, body: form,
                });
                spell._mediaError = false;
                spell.personal_media_url = `${data.personal_media_url}?v=${Date.now()}`;
                spell._mediaStatus = 'Imagem salva.';
            } catch (error) {
                spell._mediaStatus = error?.message || 'Falha ao enviar imagem.';
                this.error = spell._mediaStatus;
            } finally {
                this.mediaSaving = false;
                if (event?.target) event.target.value = '';
            }
        },

        async removeSpellMedia(spell) {
            if (!spell || this.mediaSaving) return;
            this.mediaSaving = true;
            spell._mediaStatus = 'Removendo imagem...';
            try {
                await this.request(this.spellUrl('spellMediaDelete', spell.id),
                    this.jsonOptions('DELETE'));
                spell.personal_media_url = null;
                spell._mediaError = false;
                spell._mediaStatus = 'Imagem removida. O selo padrão voltou.';
            } catch (error) {
                spell._mediaStatus = error?.message || 'Não foi possível remover a imagem.';
                this.error = spell._mediaStatus;
            } finally { this.mediaSaving = false; }
        },

        async patchSpell(
            spell,
            payload,
            message = null
        ) {
            if (
                !spell
                ||
                this.busy
            ) {
                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const data =
                    await this.request(
                        this.spellUrl(
                            'spellUpdate',
                            spell.id
                        ),
                        this.jsonOptions(
                            'PATCH',
                            payload
                        )
                    );

                /*
                 * Atualização local intencional.
                 * Não recarrega o estado inteiro e, portanto,
                 * não fecha gavetas nem produz o efeito visual de F5.
                 */
                this.applySpellPatchLocal(
                    spell,
                    payload
                );

                if (message) {
                    this.showNotice(
                        message
                    );
                } else if (
                    data?.message
                ) {
                    this.showNotice(
                        data.message
                    );
                }
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível atualizar o feitiço.';
            } finally {
                this.busy =
                    false;
            }
        },

        async togglePrepared(
            spell
        ) {
            if (
                Number(spell?.level ?? 0) === 0
                || spell.always_prepared
            ) {
                return;
            }

            await this.patchSpell(
                spell,
                {
                    prepared:
                        !spell.prepared_manual,
                },
                spell.prepared_manual
                    ? 'Feitiço despreparado.'
                    : 'Feitiço preparado.'
            );
        },

        async toggleAlwaysPrepared(
            spell
        ) {
            await this.patchSpell(
                spell,
                {
                    always_prepared:
                        !spell.always_prepared,
                }
            );
        },

        async changeSpellProfile(
            spell,
            value
        ) {
            await this.patchSpell(
                spell,
                {
                    spellcasting_id:
                        value
                            ? Number(value)
                            : null,
                }
            );
        },

        async saveFreeCasts(
            spell
        ) {
            const maximum =
                spell.free_casts_max_input
                ===
                ''
                    ? null
                    : Math.max(
                        0,
                        Number(
                            spell
                                .free_casts_max_input
                        )
                    );

            const current =
                spell.free_casts_current_input
                ===
                ''
                    ? (
                        maximum
                        ===
                        null
                            ? null
                            : maximum
                    )
                    : Math.max(
                        0,
                        Number(
                            spell
                                .free_casts_current_input
                        )
                    );

            await this.patchSpell(
                spell,
                {
                    free_casts_max:
                        maximum,

                    free_casts_current:
                        current,

                    free_casts_recovery:
                        spell
                            .free_casts_recovery_input
                        || null,

                    cast_without_slot:
                        !!spell.cast_without_slot_input,
                },
                'Configuração de conjuração salva.'
            );
        },

        async removeSpell(
            spell
        ) {
            if (
                !spell
                ||
                this.busy
            ) {
                return;
            }

            if (
                !window.confirm(
                    `Remover “${spell.name}” do Grimório?`
                )
            ) {
                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const data =
                    await this.request(
                        this.spellUrl(
                            'spellDelete',
                            spell.id
                        ),
                        this.jsonOptions(
                            'DELETE'
                        )
                    );

                const spellId =
                    Number(
                        spell.id
                    );

                this.spells =
                    this.spells.filter(
                        row =>
                            Number(row.id)
                            !==
                            spellId
                    );

                this.catalog =
                    this.catalog.map(
                        row =>
                            Number(row.id)
                            ===
                            spellId
                                ? {
                                    ...row,
                                    in_spellbook:
                                        false,
                                }
                                : row
                    );

                if (
                    this.catalogSelected
                    &&
                    Number(
                        this.catalogSelected.id
                    )
                    ===
                    spellId
                ) {
                    this.catalogSelected.in_spellbook =
                        false;
                }

                this.loadoutSelectedIds =
                    this.loadoutSelectedIds
                        .filter(
                            id =>
                                Number(id)
                                !==
                                spellId
                        );

                if (
                    this.detailSpell
                    &&
                    Number(
                        this.detailSpell.id
                    )
                    ===
                    spellId
                ) {
                    this.closeSpell();
                }

                this.showNotice(
                    data?.message
                    ??
                    'Feitiço removido.'
                );
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível remover o feitiço.';
            } finally {
                this.busy =
                    false;
            }
        },

        async removeCatalogSelected() {
            if (
                !this.catalogSelected
                ||
                !this.catalogSelected.in_spellbook
            ) {
                return;
            }

            await this.removeSpell(
                this.catalogSelected
            );
        },

        castLevelOptions(
            spell
        ) {
            const base =
                Number(
                    spell?.level
                    ?? 0
                );

            if (
                base === 0
            ) {
                return [0];
            }

            return Array
                .from(
                    {
                        length:
                            10
                            -
                            base,
                    },
                    (
                        _,
                        index
                    ) =>
                        base
                        +
                        index
                )
                .filter(
                    level =>
                        level <= 9
                );
        },

        async castSpell() {
            const spell =
                this.castConfirmSpell
                ??
                this.detailSpell;

            if (
                !spell
                ||
                this.busy
            ) {
                return false;
            }

            let slotType =
                null;

            let castLevel =
                Number(
                    this.castLevel
                    ??
                    spell.level
                );

            if (
                this.castResource
                ===
                'slot'
            ) {
                const parsed =
                    this.parseSlotKey(
                        this.castSlotKey
                    );

                slotType =
                    parsed.slot_type;

                castLevel =
                    parsed.slot_level;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const data =
                    await this.request(
                        this.spellUrl(
                            'spellCast',
                            spell.id
                        ),
                        this.jsonOptions(
                            'POST',
                            {
                                spellcasting_id:
                                    this.castConfirmProfile()?.id ?? null,
                                resource_type:
                                    this.castResource,

                                slot_type:
                                    slotType,

                                cast_level:
                                    castLevel,
                                confirm_replace_concentration:
                                    !!this.castConfirmOpen
                                    && this.castWillReplaceConcentration(),
                            }
                        )
                    );

                this.showNotice(
                    data?.message
                    ??
                    'Feitiço conjurado.'
                );

                await this.loadState();

                // A V53 avisa Transformações somente após conjuração bem-sucedida.
                // A forma vinculada usa a concentração deste mesmo feitiço.
                window.dispatchEvent(
                    new CustomEvent('spellbound-spell-cast', {
                        detail: {
                            spell_id: Number(spell.id),
                            cast_level: Number(castLevel),
                        },
                    })
                );

                window.dispatchEvent(
                    new CustomEvent(
                        'character-concentration-changed'
                    )
                );

                return true;
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível conjurar o feitiço.';

                return false;
            } finally {
                this.busy =
                    false;
            }
        },

        async confirmCast() {
            const success =
                await this.castSpell();

            if (success) {
                this.closeCastConfirm();
            }
        },

        async stopConcentration() {
            if (
                this.busy
            ) {
                return;
            }

            this.busy =
                true;

            try {
                const data =
                    await this.request(
                        this
                            .urls
                            .concentrationDelete,
                        this.jsonOptions(
                            'DELETE'
                        )
                    );

                this.showNotice(
                    data?.message
                    ??
                    'Concentração encerrada.'
                );

                await this.loadState();

                window.dispatchEvent(
                    new CustomEvent(
                        'character-concentration-changed'
                    )
                );
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível encerrar a concentração.';
            } finally {
                this.busy =
                    false;
            }
        },

        openLoadouts() {
            this.loadoutOpen = true;
            this.loadoutHelpOpen = false;
            this.newLoadout();
        },

        closeLoadouts() {
            if (this.busy) {
                return;
            }

            this.loadoutOpen = false;
            this.loadoutHelpOpen = false;
            this.newLoadout();
        },

        newLoadout() {
            this.loadoutEditingId = null;
            this.loadoutSearch = '';
            this.loadoutSelectedIds = [];
            this.loadoutForm = {
                name: '',
                description: '',
                spellcasting_id: this.primaryProfile()?.id
                    ? String(this.primaryProfile().id)
                    : '',
            };
        },

        editLoadout(loadout) {
            this.loadoutEditingId = Number(loadout.id);
            this.loadoutSearch = '';
            this.loadoutSelectedIds = Array.isArray(loadout.spell_ids)
                ? loadout.spell_ids.map(id => Number(id))
                : [];
            this.loadoutForm = {
                name: loadout.name ?? '',
                description: loadout.description ?? '',
                spellcasting_id: loadout.spellcasting_id !== null
                    && loadout.spellcasting_id !== undefined
                        ? String(loadout.spellcasting_id)
                        : '',
            };
        },

        loadoutAvailableSpells(
            applySearch = true
        ) {
            const profileId = this.loadoutForm.spellcasting_id
                ? Number(this.loadoutForm.spellcasting_id)
                : null;

            const search = applySearch
                ? String(this.loadoutSearch ?? '').trim().toLowerCase()
                : '';

            const unique = new Map();

            this.spells.forEach(spell => {
                if (Number(spell.level ?? 0) < 1) {
                    return;
                }

                if (spell.always_prepared) {
                    return;
                }

                if (
                    profileId !== null
                    && Number(spell.spellcasting_id ?? 0) !== profileId
                ) {
                    return;
                }

                if (search) {
                    const haystack = [
                        spell.name,
                        spell.subtitle_pt,
                        spell.subtitle,
                    ]
                        .filter(Boolean)
                        .join(' ')
                        .toLowerCase();

                    if (!haystack.includes(search)) {
                        return;
                    }
                }

                const id = Number(spell.id);

                if (!unique.has(id)) {
                    unique.set(id, spell);
                }
            });

            return Array.from(unique.values()).sort((a, b) => {
                const levelDiff = Number(a.level ?? 0) - Number(b.level ?? 0);

                if (levelDiff !== 0) {
                    return levelDiff;
                }

                return String(a.name ?? '').localeCompare(
                    String(b.name ?? ''),
                    'pt-BR'
                );
            });
        },

        loadoutSelectedSpells() {
            const selected = new Set(
                this.loadoutSelectedIds.map(id => Number(id))
            );

            const unique = new Map();

            this.spells.forEach(spell => {
                const id = Number(spell.id);

                if (!selected.has(id) || unique.has(id)) {
                    return;
                }

                unique.set(id, spell);
            });

            return Array.from(unique.values()).sort((a, b) => {
                const levelDiff = Number(a.level ?? 0) - Number(b.level ?? 0);

                if (levelDiff !== 0) {
                    return levelDiff;
                }

                return String(a.name ?? '').localeCompare(
                    String(b.name ?? ''),
                    'pt-BR'
                );
            });
        },

        loadoutSpellSelected(
            spellId
        ) {
            return this.loadoutSelectedIds.includes(Number(spellId));
        },

        toggleLoadoutSpell(
            spell
        ) {
            if (!spell) {
                return;
            }

            const id = Number(spell.id);

            if (this.loadoutSpellSelected(id)) {
                this.loadoutSelectedIds = this.loadoutSelectedIds.filter(
                    selectedId => Number(selectedId) !== id
                );
                return;
            }

            this.loadoutSelectedIds = [
                ...this.loadoutSelectedIds,
                id,
            ];
        },

        loadoutProfileChanged() {
            const allowed = new Set(
                this.loadoutAvailableSpells(false)
                    .map(spell => Number(spell.id))
            );

            this.loadoutSelectedIds = this.loadoutSelectedIds.filter(
                id => allowed.has(Number(id))
            );
        },

        loadoutUseCurrentPrepared() {
            const profileId = this.loadoutForm.spellcasting_id
                ? Number(this.loadoutForm.spellcasting_id)
                : null;

            const ids = this.spells
                .filter(spell => {
                    if (Number(spell.level ?? 0) < 1) {
                        return false;
                    }

                    if (spell.always_prepared || !spell.prepared_manual) {
                        return false;
                    }

                    if (
                        profileId !== null
                        && Number(spell.spellcasting_id ?? 0) !== profileId
                    ) {
                        return false;
                    }

                    return true;
                })
                .map(spell => Number(spell.id));

            this.loadoutSelectedIds = Array.from(new Set(ids));
        },

        loadoutPreparedLimit() {
            if (!this.loadoutForm.spellcasting_id) {
                return null;
            }

            const profileId = Number(this.loadoutForm.spellcasting_id);
            const profile = this.profiles.find(
                row => Number(row.id) === profileId
            );

            if (
                !profile
                || profile.prepared_limit === null
                || profile.prepared_limit === undefined
            ) {
                return null;
            }

            return Number(profile.prepared_limit);
        },

        loadoutSelectionExceedsLimit() {
            const limit = this.loadoutPreparedLimit();

            return limit !== null
                && this.loadoutSelectedIds.length > limit;
        },

        async saveLoadout() {
            if (this.busy) {
                return;
            }

            const name = String(this.loadoutForm.name ?? '').trim();

            if (!name) {
                this.error = 'Informe um nome para o loadout.';
                return;
            }

            if (this.loadoutSelectedIds.length === 0) {
                this.error = 'Escolha pelo menos um feitiço para o loadout.';
                return;
            }

            this.busy = true;
            this.error = '';

            try {
                const editing = this.loadoutEditingId !== null;
                const payload = {
                    name,
                    description: String(this.loadoutForm.description ?? '').trim() || null,
                    spellcasting_id: this.loadoutForm.spellcasting_id
                        ? Number(this.loadoutForm.spellcasting_id)
                        : null,
                    spell_ids: this.loadoutSelectedIds.map(id => Number(id)),
                };

                const data = await this.request(
                    editing
                        ? this.loadoutUrl('loadoutUpdate', this.loadoutEditingId)
                        : this.urls.loadoutStore,
                    this.jsonOptions(
                        editing ? 'PATCH' : 'POST',
                        payload
                    )
                );

                this.showNotice(
                    data?.message
                    ?? (editing ? 'Loadout atualizado.' : 'Loadout criado.')
                );

                await this.loadState();
                this.newLoadout();
            } catch (error) {
                this.error = error?.message ?? 'Não foi possível salvar o loadout.';
            } finally {
                this.busy = false;
            }
        },

        async applyLoadout(loadout) {
            if (!loadout || this.busy) {
                return;
            }

            this.busy = true;
            this.error = '';

            try {
                const data = await this.request(
                    this.loadoutUrl('loadoutApply', loadout.id),
                    this.jsonOptions('POST', {})
                );

                this.showNotice(data?.message ?? 'Loadout aplicado.');
                await this.loadState();
                this.loadoutOpen = false;
            } catch (error) {
                this.error = error?.message ?? 'Não foi possível aplicar o loadout.';
            } finally {
                this.busy = false;
            }
        },

        async overwriteLoadout(loadout) {
            if (!loadout || this.busy) {
                return;
            }

            this.busy = true;
            this.error = '';

            try {
                const data = await this.request(
                    this.loadoutUrl('loadoutOverwrite', loadout.id),
                    this.jsonOptions('POST', {})
                );

                this.showNotice(data?.message ?? 'Loadout substituído pela preparação atual.');
                await this.loadState();
            } catch (error) {
                this.error = error?.message ?? 'Não foi possível sobrescrever o loadout.';
            } finally {
                this.busy = false;
            }
        },

        async deleteLoadout(loadout) {
            if (!loadout || this.busy) {
                return;
            }

            if (!window.confirm(`Excluir o loadout “${loadout.name}”?`)) {
                return;
            }

            this.busy = true;
            this.error = '';

            try {
                const data = await this.request(
                    this.loadoutUrl('loadoutDelete', loadout.id),
                    this.jsonOptions('DELETE')
                );

                this.showNotice(data?.message ?? 'Loadout removido.');
                await this.loadState();
                this.newLoadout();
            } catch (error) {
                this.error = error?.message ?? 'Não foi possível excluir o loadout.';
            } finally {
                this.busy = false;
            }
        },

        async handleSpellConcentrationDamage(event) {
            const detail = event?.detail ?? {};
            const characterId = Number(@js((int) $character->id));
            const eventCharacterIds = [
                Number(detail.characterId ?? 0),
                Number(detail.mechanicalCharacterId ?? 0),
            ];

            if (!eventCharacterIds.includes(characterId)) {
                return;
            }

            const damage = Math.max(0, parseInt(detail.damage ?? 0) || 0);

            if (
                damage < 1
                || !this.concentration?.active
                || !this.concentration?.spell_id
                || this.concentrationLoading
            ) {
                return;
            }

            this.concentrationLoading = true;
            this.concentrationError = '';

            try {
                const data = await this.request(
                    this.urls.concentrationCheck,
                    this.jsonOptions('POST', { damage })
                );

                const check = data?.concentration_check ?? null;

                if (check?.required) {
                    this.openSpellConcentrationCheck(check);
                }
            } catch (error) {
                console.error('Spell concentration check:', error);
                this.concentrationError = error?.message ?? 'Não foi possível preparar o teste de concentração.';
            } finally {
                this.concentrationLoading = false;
            }
        },

        openSpellConcentrationCheck(check) {
            if (!check?.required) {
                return;
            }

            this.concentrationCheck = check;
            this.concentrationRoll = null;
            this.concentrationDieDisplay = '—';
            this.concentrationError = '';
            this.concentrationManualMode = false;
            this.concentrationManualTotal = '';
            this.concentrationCheckOpen = true;

            window.dispatchEvent(
                new CustomEvent(
                    'character-concentration-check',
                    { detail: check }
                )
            );
        },

        closeSpellConcentrationCheck() {
            if (this.concentrationRolling) {
                return;
            }

            this.concentrationCheckOpen = false;
            this.concentrationCheck = null;
            this.concentrationRoll = null;
            this.concentrationDieDisplay = '—';
            this.concentrationError = '';
            this.concentrationManualMode = false;
            this.concentrationManualTotal = '';
        },

        extractSpellConcentrationRollTotal(data) {
            const candidate =
                data?.data?.total
                ?? data?.data?.result
                ?? data?.data?.value
                ?? data?.data?.sum
                ?? data?.total
                ?? data?.result
                ?? null;

            if (candidate === null) {
                return null;
            }

            const parsed = parseInt(candidate);
            return Number.isNaN(parsed) ? null : parsed;
        },

        async rollSpellConcentrationCheck() {
            if (!this.concentrationCheck || this.concentrationRolling) {
                return;
            }

            const check = this.concentrationCheck;
            const expression = String(check.expression || '1d20').trim();

            this.concentrationRolling = true;
            this.concentrationError = '';
            this.concentrationManualMode = false;
            this.concentrationDieDisplay = String(Math.floor(Math.random() * 20) + 1);
            const animationTimer = window.setInterval(() => {
                this.concentrationDieDisplay = String(Math.floor(Math.random() * 20) + 1);
            }, 70);

            try {
                const [data] = await Promise.all([
                    this.request(this.urls.diceRoll, this.jsonOptions('POST', { expression })),
                    new Promise(resolve => window.setTimeout(resolve, 1050)),
                ]);

                const total = this.extractSpellConcentrationRollTotal(data);

                if (total === null) {
                    throw new Error('A rolagem não retornou um total válido.');
                }

                const modifier = parseInt(check.modifier ?? 0) || 0;
                const die = total - modifier;
                if (!Number.isInteger(die) || die < 1 || die > 20) {
                    throw new Error('A API retornou um resultado incompatível com o d20.');
                }

                window.clearInterval(animationTimer);
                this.concentrationDieDisplay = String(die);
                await new Promise(resolve => window.setTimeout(resolve, 160));
                const difficultyClass = Math.max(1, parseInt(check.dc ?? 10) || 10);
                const passed = total >= difficultyClass;

                this.concentrationRoll = {
                    die,
                    total,
                    modifier,
                    passed,
                    formatted: data?.formatted ?? expression,
                };

                window.dispatchEvent(
                    new CustomEvent(
                        'character-dice-rolled',
                        {
                            detail: {
                                label: check.label || 'Salvamento de Constituição',
                                modifier: check.base_modifier ?? modifier,
                                exhaustionPenalty: check.exhaustion_penalty ?? 0,
                                effectiveModifier: modifier,
                                die,
                                total,
                                formatted: data?.formatted ?? expression,
                                concentration: {
                                    dc: difficultyClass,
                                    passed,
                                    damage: check.damage ?? null,
                                    context: check.context ?? {},
                                },
                            },
                        }
                    )
                );
            } catch (error) {
                console.error('Spell concentration roll:', error);
                this.concentrationError = error?.message ?? 'Não foi possível rolar o teste de concentração.';
            } finally {
                window.clearInterval(animationTimer);
                this.concentrationRolling = false;
            }
        },

        async confirmSpellConcentrationCheck() {
            if (this.concentrationRolling || !this.concentrationCheck) {
                return;
            }

            const difficultyClass = Math.max(1, parseInt(this.concentrationCheck?.dc ?? 10) || 10);
            let result = this.concentrationRoll;

            if (this.concentrationManualMode) {
                const total = parseInt(this.concentrationManualTotal);
                if (!Number.isFinite(total)) {
                    this.concentrationError = 'Informe o resultado total rolado na mesa.';
                    return;
                }

                result = {
                    die: null,
                    total,
                    modifier: parseInt(this.concentrationCheck?.modifier ?? 0) || 0,
                    passed: total >= difficultyClass,
                    formatted: 'Rolado na mesa',
                };
                this.concentrationRoll = result;
            }

            if (!result) {
                this.concentrationError = 'Role os dados ou informe o resultado manual.';
                return;
            }

            this.concentrationError = '';

            if (!result.passed) {
                const spellName = this.concentration?.spell_name ?? 'o feitiço';
                await this.stopConcentration();
                this.showNotice(`Falha de concentração: ${spellName} foi encerrado.`);
            } else {
                const spellName = this.concentration?.spell_name ?? this.concentrationCheck?.spell_name ?? 'o feitiço';
                this.showNotice(`Concentração mantida: ${spellName}.`);
            }

            this.closeSpellConcentrationCheck();
        },

        async handleSpellConcentrationRollResult(event) {
            // Outros componentes podem rolar o mesmo payload de concentração.
            // Se a rolagem foi do spellbook e falhou, encerra a concentração.
            const concentrationResult = event?.detail?.concentration ?? null;
            const context = concentrationResult?.context ?? {};

            if (
                !concentrationResult
                || concentrationResult.passed !== false
                || context.source !== 'spell'
                || Number(context.character_id ?? 0) !== Number(@js((int) $character->id))
                || !this.concentration?.active
            ) {
                return;
            }

            if (
                context.spell_id
                && Number(context.spell_id) !== Number(this.concentration?.spell_id ?? 0)
            ) {
                return;
            }

            // A modal local já encerra imediatamente após a falha. Evita chamada dupla.
            if (this.concentrationCheckOpen && this.concentrationRoll?.passed === false) {
                return;
            }

            await this.stopConcentration();
        },

        openSettings() {
            this.settingsOpen =
                true;

            this.newProfile();
        },

        closeSettings() {
            this.settingsOpen =
                false;
        },

        async importProfilesFromSheet() {
            if (this.busy) {
                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const data =
                    await this.request(
                        this
                            .urls
                            .profileImportSheet,
                        this.jsonOptions(
                            'POST',
                            {}
                        )
                    );

                this.showNotice(
                    data?.message
                    ??
                    'Classes da ficha importadas.'
                );

                await this.loadState();
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível importar as classes da ficha.';
            } finally {
                this.busy =
                    false;
            }
        },

        applyCharacterClassToProfile(
            classId
        ) {
            const numericId =
                Number(
                    classId
                    || 0
                );

            if (!numericId) {
                this.profileForm
                    .character_class_id =
                    '';

                this.profileForm
                    .sync_with_character_class =
                    false;

                return;
            }

            const row =
                this.characterContext
                    .classes
                    .find(
                        item =>
                            Number(item.id)
                            ===
                            numericId
                    );

            if (!row) {
                return;
            }

            this.profileForm
                .character_class_id =
                numericId;

            this.profileForm
                .sync_with_character_class =
                true;

            this.profileForm.class =
                row.class;

            this.profileForm
                .caster_level =
                Number(
                    row.level
                    ?? 1
                );

            if (
                row
                    .spellcasting_default
            ) {
                this.profileForm
                    .ability =
                    row
                        .spellcasting_default
                        .ability
                    ??
                    this.profileForm
                        .ability;

                this.profileForm
                    .caster_type =
                    row
                        .spellcasting_default
                        .caster_type
                    ??
                    this.profileForm
                        .caster_type;

                this.profileForm.spell_list_codes = Array.isArray(
                    row.spellcasting_default.spell_list_codes
                )
                    ? [...row.spellcasting_default.spell_list_codes]
                    : [];
            }
        },

        newProfile() {
            this.profileEditingId =
                null;

            this.profileForm = {
                character_class_id: '',
                sync_with_character_class: false,
                class: '',
                ability: 'intelligence',
                caster_level: 1,
                caster_type: 'full',
                slot_mode: 'automatic',
                effective_caster_level_override: '',
                spell_save_dc_bonus: 0,
                spell_attack_bonus_extra: 0,
                spell_list_codes: [],
                spell_limit: '',
                prepared_limit: '',
                is_active: true,
                sort_order: this.profiles.length,
                notes: '',
            };
        },

        editProfile(
            profile
        ) {
            this.profileEditingId =
                Number(
                    profile.id
                );

            this.profileForm = {
                character_class_id:
                    profile.character_class_id
                    ?? '',

                sync_with_character_class:
                    !!profile.sync_with_character_class,

                class:
                    profile.class
                    ?? '',

                ability:
                    profile.ability
                    ?? 'intelligence',

                caster_level:
                    Number(
                        profile.caster_level
                        ?? 1
                    ),

                caster_type:
                    profile.caster_type
                    ?? 'full',

                slot_mode:
                    profile.slot_mode
                    ?? 'automatic',

                effective_caster_level_override:
                    profile
                        .effective_caster_level_override
                    ?? '',

                spell_save_dc_bonus:
                    Number(profile.spell_save_dc_bonus ?? 0),

                spell_attack_bonus_extra:
                    Number(profile.spell_attack_bonus_extra ?? 0),

                spell_list_codes:
                    Array.isArray(profile.spell_list_codes)
                        ? [...profile.spell_list_codes]
                        : [],

                spell_limit:
                    profile.spell_limit
                    ?? '',

                prepared_limit:
                    profile.prepared_limit
                    ?? '',

                is_active:
                    !!profile.is_active,

                sort_order:
                    Number(
                        profile.sort_order
                        ?? 0
                    ),

                notes:
                    profile.notes
                    ?? '',
            };
        },

        nullableNumber(
            value
        ) {
            return value
                ===
                ''
                ||
                value
                ===
                null
                ||
                value
                ===
                undefined
                    ? null
                    : Number(value);
        },

        async saveProfile() {
            if (
                this.busy
            ) {
                return;
            }

            if (
                !String(
                    this
                        .profileForm
                        .class
                    ?? ''
                )
                    .trim()
            ) {
                this.error =
                    'Informe um nome para o conjurador.';

                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const payload = {
                    character_class_id:
                        this.profileForm
                            .character_class_id
                            ? Number(
                                this.profileForm
                                    .character_class_id
                            )
                            : null,

                    sync_with_character_class:
                        !!this.profileForm
                            .sync_with_character_class,

                    class:
                        String(
                            this
                                .profileForm
                                .class
                        )
                            .trim(),

                    ability:
                        this
                            .profileForm
                            .ability,

                    caster_level:
                        Number(
                            this
                                .profileForm
                                .caster_level
                        ),

                    caster_type:
                        this
                            .profileForm
                            .caster_type,

                    slot_mode:
                        this
                            .profileForm
                            .slot_mode,

                    effective_caster_level_override:
                        this.nullableNumber(
                            this
                                .profileForm
                                .effective_caster_level_override
                        ),

                    spell_save_dc_bonus:
                        Number(
                            this.profileForm.spell_save_dc_bonus
                            ?? 0
                        ),

                    spell_attack_bonus_extra:
                        Number(
                            this.profileForm.spell_attack_bonus_extra
                            ?? 0
                        ),

                    spell_list_codes:
                        Array.isArray(this.profileForm.spell_list_codes)
                            ? this.profileForm.spell_list_codes
                            : [],

                    spell_limit:
                        this.nullableNumber(
                            this.profileForm.spell_limit
                        ),

                    prepared_limit:
                        this.nullableNumber(
                            this.profileForm.prepared_limit
                        ),

                    is_active:
                        !!this
                            .profileForm
                            .is_active,

                    sort_order:
                        Number(
                            this
                                .profileForm
                                .sort_order
                            ?? 0
                        ),

                    notes:
                        String(
                            this
                                .profileForm
                                .notes
                            ?? ''
                        )
                            .trim()
                        || null,
                };

                const editing =
                    this.profileEditingId
                    !==
                    null;

                const url =
                    editing
                        ? this.profileUrl(
                            'profileUpdate',
                            this
                                .profileEditingId
                        )
                        : this
                            .urls
                            .profileStore;

                const data =
                    await this.request(
                        url,
                        this.jsonOptions(
                            editing
                                ? 'PATCH'
                                : 'POST',
                            payload
                        )
                    );

                this.showNotice(
                    data?.message
                    ??
                    'Conjurador salvo.'
                );

                await this.loadState();

                if (
                    editing
                ) {
                    const refreshed =
                        this.profiles.find(
                            row =>
                                Number(
                                    row.id
                                )
                                ===
                                Number(
                                    this
                                        .profileEditingId
                                )
                        );

                    if (refreshed) {
                        this.editProfile(
                            refreshed
                        );
                    }
                } else {
                    this.newProfile();
                }
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível salvar o conjurador.';
            } finally {
                this.busy =
                    false;
            }
        },

        async deleteProfile(
            profile
        ) {
            if (
                !profile
                ||
                this.busy
            ) {
                return;
            }

            if (
                !window.confirm(
                    `Remover o perfil de conjuração “${profile.class}”? Os feitiços permanecem no Grimório.`
                )
            ) {
                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const data =
                    await this.request(
                        this.profileUrl(
                            'profileDelete',
                            profile.id
                        ),
                        this.jsonOptions(
                            'DELETE'
                        )
                    );

                this.showNotice(
                    data?.message
                    ??
                    'Conjurador removido.'
                );

                this.newProfile();

                await this.loadState();
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível remover o conjurador.';
            } finally {
                this.busy =
                    false;
            }
        },

        async setSlotCurrent(slot, targetCurrent) {
            if (!slot || this.busy) {
                return;
            }

            const maximum = Math.max(0, Number(slot.maximum ?? 0));
            const next = Math.max(0, Math.min(maximum, Number(targetCurrent ?? 0)));
            const previous = Number(slot.current ?? 0);

            slot.current = next;

            const draft = this.slotDrafts.find(row => Number(row.id) === Number(slot.id));
            if (draft) {
                draft.current_input = next;
            }

            try {
                await this.request(
                    this.urls.slotUpdate,
                    this.jsonOptions('PATCH', {
                        slot_type: slot.slot_type,
                        slot_level: Number(slot.slot_level),
                        manual_maximum: slot.manual_maximum ?? null,
                        current: next,
                    })
                );
            } catch (error) {
                slot.current = previous;
                if (draft) {
                    draft.current_input = previous;
                }
                this.error = error?.message ?? 'Não foi possível atualizar o slot.';
            }
        },

        toggleSlotPip(slot, pip) {
            const current = Number(slot?.current ?? 0);
            const target = Number(pip) <= current
                ? Number(pip) - 1
                : Number(pip);

            return this.setSlotCurrent(slot, target);
        },

        async saveSlot(
            slot
        ) {
            if (
                !slot
                ||
                this.busy
            ) {
                return;
            }

            this.busy =
                true;

            this.error =
                '';

            try {
                const data =
                    await this.request(
                        this.urls.slotUpdate,
                        this.jsonOptions(
                            'PATCH',
                            {
                                slot_type:
                                    slot.slot_type,

                                slot_level:
                                    Number(
                                        slot.slot_level
                                    ),

                                manual_maximum:
                                    slot
                                        .manual_maximum_input
                                    ===
                                    ''
                                        ? null
                                        : Number(
                                            slot
                                                .manual_maximum_input
                                        ),

                                current:
                                    Number(
                                        slot
                                            .current_input
                                        ?? 0
                                    ),
                            }
                        )
                    );

                this.showNotice(
                    data?.message
                    ??
                    'Slots atualizados.'
                );

                await this.loadState();
            } catch (error) {
                this.error =
                    error?.message
                    ??
                    'Não foi possível atualizar os slots.';
            } finally {
                this.busy =
                    false;
            }
        },

        prepareSpellEditor(
            spell
        ) {
            if (!spell) {
                return;
            }

            spell.free_casts_max_input =
                spell.free_casts_max
                ?? '';

            spell.free_casts_current_input =
                spell.free_casts_current
                ?? '';

            spell.free_casts_recovery_input =
                spell.free_casts_recovery
                ?? '';

            spell.cast_without_slot_input =
                !!spell.cast_without_slot;
        },
    }"
    @character-rest-completed.window="loadState()"
    @character-concentration-changed.window="loadState()"
    @keydown.escape.window="
        if (concentrationCheckOpen) {
            closeSpellConcentrationCheck()
        } else if (castConfirmOpen) {
            closeCastConfirm()
        } else if (loadoutOpen) {
            closeLoadouts()
        } else if (catalogOpen) {
            closeCatalog()
        } else if (detailOpen) {
            closeSpell()
        } else if (settingsOpen) {
            closeSettings()
        }
    "
>
    <div
        x-show="notice"
        x-cloak
        class="pointer-events-none fixed right-5 top-5 z-[190] w-[min(92vw,420px)]"
        :style="spellbookThemeStyle"
        role="status"
        aria-live="polite"
    >
        <div
            class="pointer-events-auto rounded-2xl border px-4 py-3 shadow-[var(--spellbook-shadow)] backdrop-blur-sm"
            style="
                border-color: color-mix(in srgb, var(--spellbook-state-heal) 26%, var(--spellbook-border) 74%);
                background: color-mix(in srgb, var(--spellbook-paper-strong) 90%, var(--spellbook-state-heal) 10%);
                color: var(--spellbook-title);
            "
        >
            <div class="flex items-start gap-3">
                <div
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl"
                    style="
                        background: color-mix(in srgb, var(--spellbook-state-heal) 16%, var(--spellbook-paper-strong) 84%);
                        color: var(--spellbook-state-heal);
                    "
                    aria-hidden="true"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 12.5 9.2 16.7 19 7" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[7px] font-black uppercase tracking-[0.14em]" style="color: var(--spellbook-state-heal);">
                        Grimório
                    </p>
                    <strong class="mt-0.5 block font-serif text-[18px] font-black leading-tight" style="color: var(--spellbook-title);">
                        Ação concluída
                    </strong>
                    <p class="mt-1 text-[10px] font-semibold leading-5" style="color: var(--spellbook-muted);" x-text="notice"></p>
                </div>
                <button
                    type="button"
                    @click="notice = ''"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-lg border text-sm"
                    style="
                        border-color: color-mix(in srgb, var(--spellbook-border) 82%, transparent 18%);
                        color: var(--spellbook-muted);
                        background: color-mix(in srgb, var(--spellbook-paper-strong) 88%, transparent 12%);
                    "
                    aria-label="Dispensar aviso"
                >×</button>
            </div>
        </div>
    </div>

    <div
        x-show="error"
        x-cloak
        class="
            mb-3
            rounded-xl
            border
            border-red-900/25
            bg-red-50
            px-3
            py-2.5
            text-[10px]
            font-bold
            text-red-950
        "
        x-text="error"
    ></div>

    <div
        x-show="loading"
        x-cloak
        class="
            flex
            min-h-[260px]
            items-center
            justify-center
        "
    >
        <div class="text-center">
            <div
                class="
                    mx-auto
                    h-7
                    w-7
                    animate-spin
                    rounded-full
                    border-2
                    border-[#d7c3a8]
                    border-t-[#6b1d14]
                "
            ></div>

            <p
                class="
                    mt-3
                    text-[9px]
                    font-black
                    uppercase
                    tracking-[0.16em]
                    text-[#7f6651]
                "
            >
                Preparando Grimório
            </p>
        </div>
    </div>

    <div
        x-show="!loading"
        x-cloak
        class="space-y-4"
    >
        @include(
            'characters.components.spells.overview'
        )
    </div>

    @include(
        'characters.components.spells.catalog-modal'
    )

    @include(
        'characters.components.spells.cast-confirm-modal'
    )

    @include(
        'characters.components.spells.concentration-check-modal'
    )

    @include(
        'characters.components.spells.loadout-modal'
    )

    @include(
        'characters.components.spells.detail-modal'
    )

    @include(
        'characters.components.spells.settings-modal'
    )
</div>