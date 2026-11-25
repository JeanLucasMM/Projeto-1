{{-- V66.2.100 — Criação e edição do Inventário pelo editor do Cofre.
     Compatibilidade com os oito temas; maldições do Player e prévia de pergaminhos. --}}
@php
        $vaultSpellCatalog = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('spells')) {
                $schema = \Illuminate\Support\Facades\Schema::class;
                $hasProvider = $schema::hasColumn('spells', 'provider');
                $spellColumns = ['id', 'name', 'level', 'source', 'school'];
                if ($hasProvider) $spellColumns[] = 'provider';
                $spellTable = \Illuminate\Support\Facades\DB::table('spells');
                if ($schema::hasColumn('spells', 'active')) $spellTable->where('active', true);
                if ($schema::hasColumn('spells', 'upstream_missing')) $spellTable->where('upstream_missing', false);
                $spellTable->whereNotNull('name')->where('name', '!=', '')
                    ->where(function ($query) {
                        $query->whereNull('source')->orWhereRaw('UPPER(TRIM(source)) <> ?', ['PHB']);
                    })
                    ->orderBy('name')->orderBy('level');
                // cursor() evita materializar coleção E cópia transformada juntas.
                foreach ($spellTable->cursor($spellColumns) as $spell) {
                    $source = strtoupper(trim((string) ($spell->source ?? '')));
                    $provider = strtolower(trim((string) ($hasProvider ? ($spell->provider ?? '') : '')));
                    $isTestOrHomebrew = preg_match('/^(?:UA|HB|HOME|AU$)/', $source) === 1;
                    $isOfficial = ! $isTestOrHomebrew && (
                        $provider === '5etools'
                        || ($source === 'XPHB' && ! in_array($provider, ['homebrew', 'custom', 'hb'], true))
                        || (! $hasProvider && $source === 'XPHB')
                    );
                    $vaultSpellCatalog[] = [
                        'id' => (int) $spell->id,
                        'name' => (string) $spell->name,
                        'level' => (int) $spell->level,
                        'source' => $source ?: 'PERSONALIZADO',
                        'provider' => $provider ?: 'local',
                        'is_official' => $isOfficial,
                        'school' => (string) ($spell->school ?? ''),
                    ];
                }
            }
        } catch (\Throwable $exception) {
            $vaultSpellCatalog = [];
        }

        // V66.2.69 — nomes reais das fontes e permissões das campanhas do Mestre.
        // Leitura apenas: escolher uma campanha na biblioteca não altera suas regras.
        $vaultSpellSourceNames = [];
        $vaultSpellCampaigns = [];
        try {
            $db = \Illuminate\Support\Facades\DB::class;
            $schema = \Illuminate\Support\Facades\Schema::class;
            if ($schema::hasTable('homebrew_spell_sources')) {
                $sourceRows = $db::table('homebrew_spell_sources')
                    ->where('is_available', true)
                    ->get(['provider', 'source_code', 'name']);
                foreach ($sourceRows as $row) {
                    $key = strtolower(trim((string) $row->provider)) . '|' . strtoupper(trim((string) $row->source_code));
                    $name = trim((string) ($row->name ?? ''));
                    if ($name !== '') {
                        $vaultSpellSourceNames[$key] = $name;
                    }
                }
            }
            if ($schema::hasTable('campaigns') && $schema::hasColumn('campaigns', 'owner_user_id')) {
                $campaignQuery = $db::table('campaigns')
                    ->whereIn('id', $character->campaigns()->pluck('campaigns.id')->all());
                if ($schema::hasColumn('campaigns', 'deleted_at')) {
                    $campaignQuery->whereNull('deleted_at');
                }
                $campaignColumns = ['id', 'name'];
                $hasAllowAll = $schema::hasColumn('campaigns', 'allow_all_homebrew_spells');
                if ($hasAllowAll) {
                    $campaignColumns[] = 'allow_all_homebrew_spells';
                }
                $campaignRows = $campaignQuery->orderBy('name')->get($campaignColumns);
                $allowedByCampaign = [];
                if ($campaignRows->isNotEmpty()
                    && $schema::hasTable('campaign_homebrew_spell_sources')
                    && $schema::hasTable('homebrew_spell_sources')) {
                    $enabledRows = $db::table('campaign_homebrew_spell_sources as ch')
                        ->join('homebrew_spell_sources as hs', 'hs.id', '=', 'ch.homebrew_spell_source_id')
                        ->whereIn('ch.campaign_id', $campaignRows->pluck('id')->all())
                        ->where('hs.is_available', true)
                        ->get(['ch.campaign_id', 'hs.provider', 'hs.source_code']);
                    foreach ($enabledRows as $entry) {
                        $key = strtolower(trim((string) $entry->provider)) . '|'
                            . strtoupper(trim((string) $entry->source_code));
                        $allowedByCampaign[(int) $entry->campaign_id][] = $key;
                    }
                }
                foreach ($campaignRows as $campaignRow) {
                    $id = (int) $campaignRow->id;
                    $vaultSpellCampaigns[] = [
                        'id' => $id,
                        'name' => (string) $campaignRow->name,
                        'allow_all' => $hasAllowAll && (bool) ($campaignRow->allow_all_homebrew_spells ?? false),
                        'sources' => array_values(array_unique($allowedByCampaign[$id] ?? [])),
                    ];
                }
            }
        } catch (\Throwable $exception) {
            // Ausência de tabelas auxiliares não bloqueia a criação de pergaminhos.
            $vaultSpellSourceNames = [];
            $vaultSpellCampaigns = [];
        }


    $damageTypeDefinitions = ['Cortante','Impacto','Perfurante','Ácido','Frio','Fogo','Elétrico','Energia','Necrótico','Veneno','Psíquico','Radiante','Trovão'];
@endphp
@push('styles')
<style>
@include('master.vault.partials.estilos-base')
@include('master.vault.partials.estilos-complementares')
/* O modal é teleportado para body; as variáveis de papel devem acompanhá-lo. */
.master-vault-v4-layer, .master-vault-v4-modal, .master-vault-v11-modal-layer {
    --vault-ink: #53150f;
    --vault-accent: #6b1d14;
    --vault-muted: #8c6239;
    --vault-border: rgba(176,140,98,.35);
    --vault-border-soft: rgba(205,187,159,.58);
    --vault-paper: #f8f5ee;
    --vault-paper-strong: #fffdf8;
    --vault-band: #efe9dc;
}
/* V66.2.87 · Ponte de temas entre o Cofre e a ficha do Player.
   Atua somente nos portais do editor de criação: NÃO modifica o Cofre do Mestre.
   Os oito temas são definidos pelo character-sheet.css do usuário. */
.inventory-vault-themed.character-sheet-theme[data-theme] { 
    --vault-ink: var(--theme-text-strong);
    --vault-accent: var(--theme-accent-strong);
    --vault-muted: var(--theme-text-secondary);
    --vault-border: var(--theme-border-strong);
    --vault-border-soft: var(--theme-border-soft);
    --vault-paper: var(--theme-paper);
    --vault-paper-strong: var(--theme-paper-strong);
    --vault-band: var(--theme-band);
    --iv-card: var(--theme-surface-card, var(--theme-paper-strong));
    --iv-card-alt: var(--theme-surface-card-alt, var(--theme-paper-alt));
    --iv-quiet: var(--theme-panel-soft);
    --iv-soft: var(--theme-button-soft-bg);
    --iv-selected: var(--theme-accent-soft);
    --iv-muted: var(--theme-text-secondary);
    --iv-text: var(--theme-text-primary);
    --iv-title: var(--theme-text-strong);
    --iv-border: var(--theme-border);
    --iv-strong-border: var(--theme-border-strong);
    --iv-accent: var(--theme-accent-strong);
    --iv-button-text: var(--theme-button-text);
    --iv-reader-ink: var(--theme-text-primary);
    color:var(--iv-text);
    scrollbar-color: var(--theme-border-strong) transparent;
 }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal { 
 --vault-ink:var(--theme-text-strong);--vault-accent:var(--theme-accent-strong);
 --vault-muted:var(--theme-text-secondary);--vault-border:var(--theme-border-strong);
 --vault-border-soft:var(--theme-border-soft);--vault-paper:var(--theme-paper);
 --vault-paper-strong:var(--theme-paper-strong);--vault-band:var(--theme-band);
 color:var(--iv-text)!important; background:var(--theme-modal-surface)!important;
 border-color:var(--theme-modal-border)!important; box-shadow:var(--theme-modal-shadow)!important;
  }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-backdrop,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-backdrop { background:var(--theme-modal-backdrop)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-foot,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer { background:var(--theme-modal-header-background)!important;border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-scroll { background:var(--theme-paper)!important;color:var(--iv-text)!important;scrollbar-color:var(--theme-border-strong) transparent!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-close,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header button { background:var(--iv-card-alt)!important;border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-close:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header button:hover { background:var(--theme-hover)!important;color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-equipment,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-module,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-category-shell,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-abilities,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v250-weapon,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v255-damage,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-damage,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-armor-editor,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-shield,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v266-scroll-editor,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-scroll-editor,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-scroll-builder,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-sidebar-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-toggle-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-stage-group,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-ability,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-tracker,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v10-8-drawer { color:var(--iv-text)!important;background:var(--theme-module-background)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-card-heading,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-card-heading:after,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-module-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-category-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-abilities-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-ability-bar,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense-header,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v266-scroll-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-heading,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-tracker-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v10-8-drawer-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-stage-bar,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-toolbar,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v282-title-panel { background:var(--theme-band-background)!important;border-color:var(--iv-border)!important;color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-rule,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-rule,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-property-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-property-layout,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-main-ability,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-mode-panel,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-stealth-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-strength-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-secondary-properties,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-formula,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-damage-line,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-damage-line,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-dice-panel,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-type-panel,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v258-modifier-picker,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-config-field,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-capacity,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-recovery,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-build,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-spend,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-roll,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-single-note,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-current-spell,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings-content,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings-grid,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-feature-reader,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v282-title-body,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-ability-content,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v10-8-drawer-body { background:var(--iv-card)!important;border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v45-inventory-details,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v45-inventory-fields,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-roll-preview,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v250-damage-summary,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-bonuses,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense-result,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-armor-result,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-progress,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-bonus-detail,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-stage-content,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-foot,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-abilities-body { background:var(--theme-module-alt-background)!important;border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal h2,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal h3,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal h4,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal h5,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal legend,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-rule-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-section-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-card-heading h3,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-ability-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-current-copy strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-topline-title strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-row-heading h4,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense-header h3 { color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-field label,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-section-kicker,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-overline,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v252-field-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-eyebrow,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-section-name,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-custom-fields label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-section-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-field-caption,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v282-title-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-field-group label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-stage-text,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-compact-heading { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-rule-copy,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-photo-note,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-quiet,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-finesse-hint,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-roll-description,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-recovery-copy,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-topline-title small,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-current-copy small,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings-values,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-suggestion-meta,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-spell-meta,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v282-title-copy small { color:var(--iv-muted)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="file"]),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal select,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal textarea,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal [contenteditable="true"] { background:var(--theme-input-bg)!important;border-color:var(--theme-input-border)!important;color:var(--theme-input-text)!important;caret-color:var(--theme-accent-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal input::placeholder,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal textarea::placeholder,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal [contenteditable="true"]:empty:before { color:var(--theme-input-placeholder)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal input:focus,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal select:focus,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal textarea:focus,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal [contenteditable="true"]:focus { border-color:var(--theme-input-focus)!important;outline-color:var(--theme-input-focus)!important;box-shadow:0 0 0 2px var(--theme-focus)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-toolbar,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-actions,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-foot { border-color:var(--iv-border)!important;background:var(--theme-band-soft)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-canvas,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-rich-surface,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v31-fallback-input { border-color:var(--theme-input-border)!important;background:var(--theme-input-bg)!important;color:var(--theme-input-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-secondary,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-choice,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-chip-button,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-module-action,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-category,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-ability-tool,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-nature-option,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-option,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v250-choice,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-ability-chip,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-type-chip,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v259-mod-chip,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-ability-choice,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-mode-button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-extra-chip,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-category-card,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-library-action,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-read-call,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-change-spell,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v282-title-reset,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings-actions button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v45-rarity-trigger,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v82-rarity-choice,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-stepper button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-start-choices button { background:var(--iv-card)!important;border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-secondary:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-choice:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-chip-button:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-category:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-ability-tool:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-nature-option:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-option:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v250-choice:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-type-chip:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-ability-choice:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-category-card:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-library-action:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-read-call:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-stepper button:hover { background:var(--theme-hover)!important;color:var(--iv-title)!important;border-color:var(--iv-strong-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-primary,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-add-ability,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-preview-action,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-link-spell,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-apply { background:var(--theme-button-background)!important;color:var(--iv-button-text)!important;border-color:var(--theme-button-bg)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-primary:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-add-ability:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-apply:hover { background:var(--theme-button-hover-background)!important;color:var(--iv-button-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-choice.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-chip-button.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-category.is-selected,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-category.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v18-nature-option.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-option.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v250-choice.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-ability-chip.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-type-chip.is-selected,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v251-type-chip.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v259-mod-chip.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-ability-choice.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-mode-button.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-extra-chip.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-category-card.is-active,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-settings[open],
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v82-rarity-choice.is-selected { background:var(--iv-selected)!important;color:var(--iv-title)!important;border-color:var(--theme-accent)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-switch.is-on,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-toggle.is-on,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-enable-switch.is-on,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-stealth-switch.is-active { background:var(--theme-accent-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-stepper,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-strength-stepper,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v259-modifier-picker,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v45-rarity-options { border-color:var(--iv-border)!important;background:var(--iv-card)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-stepper button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v263-strength-stepper button { background:var(--iv-soft)!important;color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v5-abilities-mark,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-book-emblem,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-current-mark,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-emblem,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-result-symbol,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-mode-mark,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v282-title-icon { background:var(--iv-soft)!important;border-color:var(--iv-border)!important;color:var(--theme-accent-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense-result,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-magic-tag,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v261-result-tag,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-roll-preview,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-damage-line,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-dice-panel,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-type-panel,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-build,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-spend,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-start-choices,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-bonus-detail { border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-dice-d,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v254-bonus-number,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense-result strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v260-defense-result b,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v253-enchant-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v281-current-spell strong { color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-progress,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-roll-result,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-complete,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v256-fixed { background:var(--theme-panel-soft)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-secret,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-secret-body,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v10-8-selected,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v10-8-drawer,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-stage-content { background:var(--iv-card-alt)!important;border-color:var(--iv-border)!important;color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-layer { color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog { background:var(--theme-modal-surface)!important;color:var(--iv-text)!important;border-color:var(--theme-modal-border)!important;box-shadow:var(--theme-modal-shadow)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-tabs,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-footer { background:var(--theme-modal-header-background)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-content,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-content,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-books-tab,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-books-workspace,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-list-pane,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-preview-pane,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-browser,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-detail { background:var(--theme-paper)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-list-pane,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-preview,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-list,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-results,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-inspector-list,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-detail,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-paper,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-campaign-bar,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-inspector-layout { background:var(--iv-card)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title h3,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-inspector-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-spell-name,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader h4,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-list-title,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-browser-head h4 { color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title small,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title p,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-spell-meta,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-section-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-footer small,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-preview small,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-subtitle,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-source,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-inspector-empty { color:var(--iv-muted)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-close,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-tabs>button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-spell,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-row,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-line,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-enable,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-explore,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-campaign-choices button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-load-more,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-books-empty,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-suggestion-row,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-clear-books { background:var(--iv-card)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-tabs>button.is-current,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-spell.is-selected,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-row.is-selected,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-line.is-selected,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-campaign-choices button.is-active { background:var(--iv-selected)!important;color:var(--iv-title)!important;border-color:var(--theme-accent)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-close:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-tabs>button:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-spell:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-row:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-line:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-explore:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-enable:hover { background:var(--theme-hover)!important;color:var(--iv-title)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog input:not([type="checkbox"]):not([type="radio"]),
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog select,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog textarea { background:var(--theme-input-bg)!important;color:var(--theme-input-text)!important;border-color:var(--theme-input-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog input::placeholder,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog textarea::placeholder { color:var(--theme-input-placeholder)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog input:focus,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog textarea:focus,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog select:focus { border-color:var(--theme-input-focus)!important;outline-color:var(--theme-input-focus)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-link-spell,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-preview-action,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-book-enable.is-active { background:var(--theme-button-background)!important;color:var(--iv-button-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader { 
 --spellbook-ink:var(--iv-text);
 --spellbook-title:var(--iv-title);
 --spellbook-label:var(--theme-label);
 --spellbook-accent:var(--theme-accent-strong);
 --spellbook-muted:var(--iv-muted);
 --spellbook-border-strong:var(--iv-strong-border);
 --spellbook-paper-strong:var(--iv-card);
 --spellbook-shadow-strong:var(--theme-shadow);
 background:var(--iv-card)!important;color:var(--iv-text)!important;
 }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader p,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-rich-copy,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader li,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader .vault-v272-facts { color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-reader a,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-higher h5 { color:var(--theme-accent-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-higher,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v272-facts { border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-panel { background:var(--theme-modal-surface)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-tools,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-stage,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control { background:var(--iv-card-alt)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-align-row button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-file-actions button { background:var(--iv-card)!important;color:var(--iv-text)!important;border-color:var(--iv-border)!important; }

.inventory-vault-themed.character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .master-vault-v4-modal :is(
  p, small, label, em, summary, legend,
  .vault-v250-choice, .vault-v251-type-chip,
  .vault-v263-ability-choice, .vault-v254-ability-chip,
  .vault-v19-option, .vault-v18-nature-option,
  .vault-v281-settings-values, .vault-v260-rule,
  .vault-v256-config-field, .vault-v256-mode-select,
  .master-vault-v4-rule, .master-vault-v4-module-body,
  .master-vault-v5-ability-content,
  .vault-v281-scroll-builder) { color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .vault-v267-library-dialog :is(p, small, label, span, strong, em) { color:var(--iv-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .master-vault-v4-modal :is(.master-vault-v4-primary,.master-vault-v5-add-ability) :is(span,small,strong) {color:var(--iv-button-text)!important;}
.inventory-vault-themed.character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .vault-v267-library-dialog :is(.vault-v267-link-spell,.vault-v270-preview-action) :is(span,small,strong) {color:var(--iv-button-text)!important;}

.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal ::selection,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog ::selection { background:var(--iv-selected)!important;color:var(--iv-title)!important; }
/* V66.2.88 — CONTRASTE REAL PARA O EDITOR DO PLAYER.
   Corrige classes internas com cores fixas do Cofre. Não afeta o Cofre do Mestre.
   Todo o sistema usa a paleta vigente de character-sheet.css; sem temas paralelos. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal { 
    --vault-ink:var(--theme-text-primary);
    --vault-accent:var(--theme-accent-strong);
    --vault-muted:var(--theme-text-secondary);
    --vault-border:var(--theme-border);
    --vault-border-soft:var(--theme-border-soft);
    --vault-paper:var(--theme-paper);
    --vault-paper-strong:var(--theme-paper-strong);
    --vault-band:var(--theme-band);
    color:var(--theme-text-primary)!important;
 }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category-head > strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-head h3 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-secret-head h3 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-rule-title { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-title strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-side-title { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-card-heading h3 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-sidebar-preview strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-compact-heading { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v260-defense-header h3 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v260-row-heading h4 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v281-scroll-builder h3 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v281-current-copy strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-damage-heading h3 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v282-title-copy strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-option strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-choice strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-toggle-card strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-choice strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-choice b { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-category-card strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-ability-choice strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-option strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v251-type-chip strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-chip strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-mode-select strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v281-settings strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v82-rarity-trigger strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v82-rarity-choice { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-chip { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-choice { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-category-card { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-option { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-ability-choice { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-option strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-category-card strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-ability-choice strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-option strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-choice strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-choice strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-field > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-fields label > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-group > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-field-group > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-group label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-secret-head > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-head-main > div > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-overline { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v252-field-label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-section-name { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v260-eyebrow { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-field-caption { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v281-settings-label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v282-title-label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-dice-label { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category-head p { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-head p { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-head span { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-card-heading p { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-field-help { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-rule-copy { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-help { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-sidebar-preview small { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category span span { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-option small { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-title span { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-side-copy { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-finesse-hint { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v281-current-copy small { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v281-settings-values { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v282-title-copy small { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v267-suggestion-meta { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-roll-description { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-empty { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-side { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-drawer { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nav { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-sidebar-card { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-ability-row { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-formula { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v251-damage-info { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-progress { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-summary { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-help { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-empty { background:var(--theme-panel-soft)!important;color:var(--theme-text-secondary)!important;border:1px dashed var(--theme-border-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-bar { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category-head { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-abilities-head { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-secret-head { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-card-heading { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-toolbar { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-actions { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-card-heading { background:transparent!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-photo-frame { background:var(--theme-panel-soft)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-photo .master-vault-v4-photo-frame { background:var(--theme-panel-soft)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-photo-empty { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-photo-empty span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-photo-empty svg { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category-icon { background:var(--theme-button-soft-bg)!important;color:var(--theme-button-soft-text)!important;border-color:var(--theme-border-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-number { background:var(--theme-button-soft-bg)!important;color:var(--theme-button-soft-text)!important;border-color:var(--theme-border-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-step { background:var(--theme-button-soft-bg)!important;color:var(--theme-button-soft-text)!important;border-color:var(--theme-border-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-mark { background:var(--theme-button-soft-bg)!important;color:var(--theme-button-soft-text)!important;border-color:var(--theme-border-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category.is-active .master-vault-v5-category-icon { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-option.is-active .vault-v18-nature-mark { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-option { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v82-rarity-choice { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-choice { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-choice { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-chip { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-ability-choice { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v251-type-chip { background:var(--theme-surface-card-alt)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v18-nature-option:is(:hover,.is-active) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v82-rarity-choice:is(:hover,.is-selected) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-category:is(:hover,.is-active) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-choice:is(:hover,.is-active) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-choice:is(:hover,.is-active) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-chip:is(:hover,.is-active) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v263-ability-choice:is(:hover,.is-active) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v251-type-chip:is(:hover,.is-selected) { background:var(--theme-accent-soft)!important;border-color:var(--theme-accent)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-actions button { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-toolbar button { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-tool { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-actions button:is(:hover,.is-active,:focus-visible) { background:var(--theme-accent-soft)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-ability-tool:hover { background:var(--theme-accent-soft)!important;border-color:var(--theme-border-strong)!important;color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-canvas { background:var(--theme-input-bg)!important;border-color:var(--theme-input-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-canvas [contenteditable] { color:var(--theme-input-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-canvas p { color:var(--theme-input-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v31-rich-canvas li { color:var(--theme-input-text)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-row { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-notice { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v255-ability-notice { background:var(--theme-panel-soft)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-row span { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-notice span { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v254-ability-index { border-color:var(--theme-accent)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-build-counter { border-color:var(--theme-accent)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-mode-mark { border-color:var(--theme-accent)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-secret-head > span { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-photo-remove { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-secret { background:var(--theme-module-alt-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-search { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-book-search { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v269-filter-search-wrap { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-filters { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-toolbar { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-source { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v270-campaign-bar { background:var(--theme-surface-card-alt)!important;color:var(--theme-text-primary)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-search input { background:transparent!important;color:var(--theme-input-text)!important;border:0!important;box-shadow:none!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-book-search input { background:transparent!important;color:var(--theme-input-text)!important;border:0!important;box-shadow:none!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-search svg { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-book-search svg { color:var(--theme-label)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-list-title { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v270-book-browser-head { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v270-inspector-head { background:var(--theme-band-background)!important;border-color:var(--theme-border)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-list-title strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-preview-inner h4 { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-preview-empty strong { color:var(--theme-text-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-list-title span { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-spell-meta { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-spell-meta b { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-spell-meta small { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-preview-inner dt { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-library-footer span { color:var(--theme-text-secondary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-preview-inner dd { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-spell-name strong { color:var(--theme-text-primary)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-preview-tags span { background:var(--theme-accent-soft)!important;color:var(--theme-text-strong)!important;border-color:var(--theme-border-strong)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v4-primary { background:var(--theme-button-background)!important;color:var(--theme-button-text)!important;border-color:var(--theme-button-bg)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .master-vault-v5-add-ability { background:var(--theme-button-background)!important;color:var(--theme-button-text)!important;border-color:var(--theme-button-bg)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v267-link-spell { background:var(--theme-button-background)!important;color:var(--theme-button-text)!important;border-color:var(--theme-button-bg)!important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v270-preview-action { background:var(--theme-button-background)!important;color:var(--theme-button-text)!important;border-color:var(--theme-button-bg)!important; }

/* V66.2.89 — ponte completa dos tokens do Grimório para os modais de criação.
   Resolve fundos claros/creme, rastros brancos e superfícies desalinhadas no tema escuro. */
.inventory-vault-themed.character-sheet-theme[data-theme] :is(.master-vault-v4-modal,.vault-v267-library-dialog,.vault-v272-reader,.vault-v270-spell-paper,.vault-v270-book-detail,.vault-v270-book-results,.vault-v270-inspector-list,.vault-v270-spell-detail) {
    --spellbook-ink: var(--theme-text-primary);
    --spellbook-muted: var(--theme-text-secondary);
    --spellbook-title: var(--theme-text-strong);
    --spellbook-label: var(--theme-label);
    --spellbook-accent: var(--theme-accent-strong);
    --spellbook-accent-deep: var(--theme-accent-deep, var(--theme-accent-strong));
    --spellbook-accent-soft: var(--theme-accent-soft);
    --spellbook-accent-softer: var(--theme-accent-softer, var(--theme-accent-soft));
    --spellbook-paper: var(--theme-paper);
    --spellbook-paper-strong: var(--theme-paper-strong);
    --spellbook-paper-alt: var(--theme-paper-alt);
    --spellbook-panel: var(--theme-panel);
    --spellbook-panel-soft: var(--theme-panel-soft);
    --spellbook-hover: var(--theme-hover);
    --spellbook-border: var(--theme-border);
    --spellbook-border-soft: var(--theme-border-soft);
    --spellbook-border-strong: var(--theme-border-strong);
    --spellbook-input-bg: var(--theme-input-bg);
    --spellbook-input-border: var(--theme-input-border);
    --spellbook-input-text: var(--theme-input-text);
    --spellbook-input-placeholder: var(--theme-input-placeholder);
    --spellbook-button-bg: var(--theme-button-bg);
    --spellbook-button-hover: var(--theme-button-hover);
    --spellbook-button-text: var(--theme-button-text);
    --spellbook-shadow: var(--theme-shadow);
    --spellbook-shadow-strong: var(--theme-shadow-strong);
    --spellbook-state-concentration: var(--theme-state-concentration);
    --spellbook-state-warning: var(--theme-state-warning);
    --spellbook-state-damage: var(--theme-state-damage);
    --spellbook-state-heal: var(--theme-state-heal);
    --spellbook-surface-card: var(--theme-surface-card, var(--theme-paper-strong));
    --spellbook-surface-card-alt: var(--theme-surface-card-alt, var(--theme-paper-alt));
    --spellbook-surface-inset: var(--theme-surface-inset, var(--theme-panel));
    --spellbook-surface-raised: var(--theme-surface-raised, var(--theme-paper-strong));
    --spellbook-card-hover: var(--theme-card-hover, var(--theme-hover));
    --spellbook-on-accent: var(--theme-on-accent, var(--theme-button-text));
    --spellbook-divider: var(--theme-divider, var(--theme-border-soft));
    --spellbook-divider-strong: var(--theme-divider-strong, var(--theme-border));
    --spellbook-card-background: var(--theme-card-background, var(--theme-paper-strong));
    --spellbook-card-alt-background: var(--theme-card-alt-background, var(--theme-paper-alt));
    --spellbook-inset-background: var(--theme-inset-background, var(--theme-panel));
    --spellbook-band-background: var(--theme-band-background, var(--theme-panel));
    --spellbook-button-background: var(--theme-button-background, var(--theme-button-bg));
    --spellbook-button-hover-background: var(--theme-button-hover-background, var(--theme-button-hover));
    --spellbook-modal-backdrop: var(--theme-modal-backdrop);
    --spellbook-modal-surface: var(--theme-modal-surface, var(--theme-paper-strong));
    --spellbook-modal-surface-soft: var(--theme-modal-surface-soft, var(--theme-paper));
    --spellbook-modal-surface-inset: var(--theme-modal-surface-inset, var(--theme-panel));
    --spellbook-modal-header-background: var(--theme-modal-header-background, var(--theme-band-background));
    --spellbook-modal-border: var(--theme-modal-border, var(--theme-border));
    --spellbook-modal-border-strong: var(--theme-modal-border-strong, var(--theme-border-strong));
    --spellbook-modal-text: var(--theme-modal-text, var(--theme-text-primary));
    --spellbook-modal-title: var(--theme-modal-title, var(--theme-text-strong));
    --spellbook-modal-muted: var(--theme-modal-muted, var(--theme-text-secondary));
    --spellbook-modal-label: var(--theme-modal-label, var(--theme-label));
    --spellbook-modal-shadow: var(--theme-modal-shadow, var(--theme-shadow-strong));
    --spellbook-modal-control-shadow: var(--theme-modal-control-shadow, var(--theme-shadow));
    --spellbook-accent-shadow: 0 8px 22px color-mix(in srgb, var(--theme-accent-strong) 22%, transparent);
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(.vault-v267-library-preview,.vault-v267-library-list,.vault-v270-book-results,.vault-v270-inspector-list,.vault-v270-spell-detail,.vault-v270-spell-paper,.vault-v270-book-detail,.vault-v272-reader,.vault-v267-preview-inner,.vault-v267-preview-empty) {
    background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
    color: var(--theme-text-primary) !important;
    border-color: var(--theme-border) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v272-reader :is(h4,h5,p,li,span,strong,em,small,dt,dd,.spell-rich-copy,.spell-rich-text,.spell-damage-type,.spell-rule-term,.vault-v272-facts,.vault-v272-higher-line) {
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v272-reader :is(.vault-v272-source,.vault-v272-subtitle,.vault-v272-scaling,.spell-rule-tooltip-kind) {
    color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v272-reader :is(.spell-rule-tooltip,.spell-rule-tooltip::after) {
    background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
    color: var(--theme-text-primary) !important;
    border-color: var(--theme-border-strong) !important;
    box-shadow: var(--theme-shadow-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(.vault-v267-library-search,.vault-v267-book-search,.vault-v269-filter-search-wrap,.vault-v267-library-filters,.vault-v267-library-toolbar,.vault-v267-library-source) {
    background: var(--theme-input-bg) !important;
    border-color: var(--theme-input-border) !important;
    color: var(--theme-input-text) !important;
    box-shadow: none !important;
    overflow: hidden;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(.vault-v267-library-search,.vault-v267-book-search,.vault-v269-filter-search-wrap) > * {
    background: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
    color: var(--theme-input-text) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(.vault-v267-library-search,.vault-v267-book-search,.vault-v269-filter-search-wrap) input {
    background: transparent !important;
    color: var(--theme-input-text) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.master-vault-v4-photo-button,.master-vault-v4-photo-frame,.master-vault-v4-photo-placeholder,.vault-v31-rich-surface,.vault-v31-rich-canvas,.vault-v31-fallback-input,.master-vault-v5-empty,.master-vault-v4-secret textarea,.master-vault-v4-secret-body) {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    color: var(--theme-text-primary) !important;
    border-color: var(--theme-border) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.master-vault-v4-photo-placeholder,.master-vault-v5-empty,.master-vault-v4-photo-button span,.master-vault-v4-photo-button small,.master-vault-v4-photo-placeholder small) {
    color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.master-vault-v4-module,.master-vault-v5-category-shell,.master-vault-v5-abilities,.master-vault-v4-secret,.vault-v250-weapon,.vault-v260-defense,.vault-v261-armor-editor,.vault-v266-scroll-editor,.vault-v267-scroll-editor,.vault-v281-scroll-builder,.vault-v18-card,.vault-v19-equipment,.vault-v282-title-panel) {
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-choice,.vault-v251-type-chip,.vault-v254-ability-chip,.vault-v263-ability-choice,.vault-v18-nature-option,.master-vault-v5-category,.master-vault-v4-choice,.vault-v82-rarity-choice) {
    background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
    border-color: var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-choice,.vault-v251-type-chip,.vault-v254-ability-chip,.vault-v263-ability-choice,.vault-v18-nature-option,.master-vault-v5-category,.master-vault-v4-choice,.vault-v82-rarity-choice):is(:hover,.is-active,.is-selected) {
    background: var(--theme-accent-soft) !important;
    border-color: var(--theme-accent) !important;
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v254-ability-summary,.vault-v254-ability-row,.vault-v254-ability-notice,.vault-v255-ability-notice,.vault-v256-progress,.vault-v256-roll-result,.vault-v256-complete,.vault-v256-fixed,.vault-v256-build,.vault-v256-spend,.vault-v256-capacity,.vault-v256-recovery,.vault-v256-single-note) {
    background: var(--theme-panel-soft) !important;
    border-color: var(--theme-border) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v31-rich-toolbar,.vault-v31-rich-actions,.vault-v31-rich-foot,.master-vault-v4-head,.master-vault-v4-foot) {
    box-shadow: none !important;
}

/* V66.2.90 — controles de busca e ícones de categoria do editor DO PLAYER.
   O Cofre do Mestre permanece intacto. */

/* Todos os cinco ícones utilizam uma única linguagem visual neutra.
   As imagens PNG são monocromatizadas; o SVG usa a mesma tinta. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category.is-active .master-vault-v5-category-icon {
    background: var(--theme-surface-card-alt, var(--theme-panel-soft)) !important;
    background-image: none !important;
    border: 1px solid var(--theme-border-strong) !important;
    box-shadow: none !important;
    color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon svg {
    color: var(--theme-text-secondary) !important;
    stroke: currentColor !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon img.vault-v247-category-image {
    filter: grayscale(1) brightness(0) invert(.36) !important;
    opacity: .94 !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category-icon img.vault-v247-category-image {
    filter: grayscale(1) brightness(0) invert(.78) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v19-category-grid .master-vault-v5-category.is-active:after {
    background: var(--theme-accent-strong) !important;
    color: var(--theme-button-text) !important;
    box-shadow: 0 0 0 2px var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
}

/* Corrige a moldura clara em pesquisas: o container é a superfície do campo,
   e o input interno é totalmente transparente, inclusive nos estados de foco. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v267-library-search,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v267-book-search,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v270-book-search,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v270-inspector-search {
    background: var(--theme-input-bg) !important;
    background-image: none !important;
    color: var(--theme-input-text) !important;
    border: 1px solid var(--theme-input-border) !important;
    box-shadow: none !important;
    overflow: hidden;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search:focus-within,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v267-library-search:focus-within,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v267-book-search:focus-within,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v270-book-search:focus-within,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v270-inspector-search:focus-within {
    border-color: var(--theme-input-focus) !important;
    outline: 2px solid color-mix(in srgb, var(--theme-input-focus) 24%, transparent) !important;
    outline-offset: -2px;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search input,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v267-library-search input,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v267-book-search input,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v270-book-search input,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog label.vault-v270-inspector-search input {
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    color: var(--theme-input-text) !important;
    -webkit-text-fill-color: var(--theme-input-text) !important;
    border: 0 !important;
    outline: 0 !important;
    box-shadow: none !important;
    min-width: 0;
    flex: 1 1 auto;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search input:focus,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(label.vault-v267-library-search,label.vault-v267-book-search,label.vault-v270-book-search,label.vault-v270-inspector-search) input:focus {
    outline: 0 !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search svg,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(label.vault-v267-library-search,label.vault-v267-book-search,label.vault-v270-book-search) svg {
    color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search input::placeholder,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog :is(label.vault-v267-library-search,label.vault-v267-book-search,label.vault-v270-book-search,label.vault-v270-inspector-search) input::placeholder {
    color: var(--theme-input-placeholder) !important;
    -webkit-text-fill-color: var(--theme-input-placeholder) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search input::-webkit-search-decoration,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal label.vault-v267-quick-search input::-webkit-search-cancel-button,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog input[type="search"]::-webkit-search-decoration,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog input[type="search"]::-webkit-search-cancel-button {
    -webkit-appearance: none;
}

/* V66.2.91 — Armas, Armaduras e Escudos: sem contorno claro de pergaminho.
   Escopo exclusivo do modal de criação do Player. O Cofre do Mestre não muda.
   O mesmo CSS funciona em todos os temas por usar tokens da ficha. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) {
  --v265-ink: var(--theme-text-strong);
  --v265-muted: var(--theme-text-secondary);
  --v265-border: var(--theme-border);
  --v265-accent: var(--theme-accent);
  --v265-cream: var(--theme-surface-card, var(--theme-paper-strong));
  --v265-highlight: var(--theme-accent-soft);
  background: var(--theme-module-background) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  box-shadow: none !important;
  color: var(--theme-text-primary) !important;
}
/* O brilho branco original era um box-shadow inset, não uma borda.
   Remove-se apenas nas superfícies destes três editores. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .master-vault-v4-module-head,.master-vault-v4-module-body,.master-vault-v4-rule,
  .vault-v260-rule,.vault-v260-defense-result,.vault-v260-defense-enchant,
  .vault-v261-category-card,.vault-v261-property-card,.vault-v263-properties,
  .vault-v263-main-ability,.vault-v263-stealth-card,.vault-v263-strength-card,
  .vault-v263-extras,.vault-v263-formula,.vault-v263-property-layout,
  .vault-v250-damage-part,.vault-v250-damage-summary,.vault-v251-damage-line,
  .vault-v254-damage-line,.vault-v254-dice-panel,.vault-v254-type-panel,
  .vault-v259-modifier-picker,.vault-v256-config-field,.vault-v256-tracker,
  .vault-v19-option,.vault-v261-toggle-pill,.vault-v263-ability-choice,
  .vault-v263-mode-button,.vault-v263-stealth-switch,.vault-v263-extra-chip,
  .vault-v250-choice,.vault-v250-choice-indicator,.vault-v251-type-chip,
  .vault-v254-ability-chip,.vault-v259-mod-chip
) {
  background-image: none !important;
  box-shadow: none !important;
  border-color: var(--theme-border) !important;
}
/* Caixas principais (sem perder a hierarquia entre painel e conteúdo). */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .master-vault-v4-rule,.vault-v260-rule,.vault-v261-property-card,.vault-v263-properties,
  .vault-v263-main-ability,.vault-v263-stealth-card,.vault-v263-strength-card,
  .vault-v263-extras,.vault-v250-damage-part,.vault-v251-damage-line,
  .vault-v254-damage-line,.vault-v254-type-panel,.vault-v256-tracker,.vault-v256-config-field,
  .vault-v259-modifier-picker
) {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .vault-v260-defense-result,.vault-v263-formula,.vault-v250-damage-summary,
  .vault-v254-dice-panel,.vault-v254-bonus-detail,.vault-v256-progress
) {
  background: var(--theme-panel-soft) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  box-shadow: none !important;
  color: var(--theme-text-primary) !important;
}
/* Títulos de propriedades, fórmulas e dados herdavam vinho escuro. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .vault-v250-rule-heading,.vault-v250-damage-header,.vault-v260-row-heading,
  .vault-v261-property-head,.vault-v263-compact-heading,.vault-v263-strength-heading,
  .vault-v263-extras-label,.vault-v263-formula,.vault-v261-category-text,
  .vault-v250-damage-summary,.vault-v260-defense-result
) :is(h3,h4,strong,b) {
  color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .vault-v260-row-heading,.vault-v261-property-head,.vault-v263-compact-heading,
  .vault-v263-strength-heading,.vault-v261-category-text,.vault-v250-rule-heading,
  .vault-v250-damage-header,.vault-v260-defense-result,.vault-v263-formula
) :is(small,span,p,em) {
  color: var(--theme-text-secondary) !important;
}
/* Controles: superfícies consistentes, em vez de pastilhas cor de creme. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .vault-v19-option,.vault-v250-choice,.vault-v251-type-chip,.vault-v254-ability-chip,
  .vault-v259-mod-chip,.vault-v261-category-card,.vault-v261-toggle-pill,
  .vault-v263-ability-choice,.vault-v263-mode-button,.vault-v263-stealth-switch,
  .vault-v263-extra-chip,.vault-v250-count,.vault-v261-result-tag,
  .vault-v260-magic-tag,.vault-v263-extras-label small,.vault-v263-extras-chevron
) {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  background-image: none !important;
  border-color: var(--theme-border-strong) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(
  .vault-v19-option,.vault-v250-choice,.vault-v251-type-chip,.vault-v254-ability-chip,
  .vault-v259-mod-chip,.vault-v261-category-card,.vault-v261-toggle-pill,
  .vault-v263-ability-choice,.vault-v263-mode-button,.vault-v263-stealth-switch,
  .vault-v263-extra-chip
):is(:hover,.is-active,.is-selected,.is-disadvantage) {
  background: var(--theme-accent-soft) !important;
  background-image: none !important;
  border-color: var(--theme-accent) !important;
  color: var(--theme-text-strong) !important;
  box-shadow: none !important;
}
/* Armadura: a moldura do ícone e a gaveta Somar outros atributos. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor :is(
  .vault-v261-category-icon,.vault-v264-category-check,.vault-v263-extras-chevron,
  .vault-v263-stealth-indicator,.vault-v260-result-symbol
) {
  background: var(--theme-button-soft-bg) !important;
  background-image: none !important;
  color: var(--theme-button-soft-text) !important;
  border-color: var(--theme-border-strong) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon img {
  filter: grayscale(1) brightness(0) opacity(.73);
}
.inventory-vault-themed.character-sheet-theme[data-theme]:is([data-theme="midnight"],[data-theme="obsidian"]) .master-vault-v4-modal .vault-v261-armor-editor .vault-v261-category-icon img {
  filter: grayscale(1) brightness(0) invert(.82) opacity(.92);
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor :is(.vault-v263-extras,.vault-v263-extras>summary,.vault-v263-extras-options) {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras>summary:hover {
  background: var(--theme-hover) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-label,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-extras-label strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch strong {
  color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  color: var(--theme-text-primary) !important;
  border-color: var(--theme-border) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-switch.is-disadvantage {
  background: var(--theme-accent-soft) !important;
  border-color: var(--theme-accent) !important;
}
/* Escudo mágico: cabeçalho e controle sem texto vinho escuro. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v260-shield :is(.vault-v253-enchant-heading,.vault-v253-enchant-ability,.vault-v260-defense-enchant) {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v260-shield :is(.vault-v253-enchant-title,.vault-v260-row-heading strong,.vault-v260-defense-result strong) {
  color: var(--theme-text-strong) !important;
}
/* Badges e quadradinhos do construtor de armas (sem pontos claros espalhados). */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .vault-v250-choice-indicator,.vault-v250-damage-part-number,.vault-v254-ability-index,
  .vault-v254-ability-mark,.vault-v259-attack-icon,.vault-v250-count
) {
  background: var(--theme-button-soft-bg) !important;
  background-image: none !important;
  border-color: var(--theme-border-strong) !important;
  color: var(--theme-button-soft-text) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .vault-v250-choice.is-active .vault-v250-choice-indicator {
  background: var(--theme-accent-strong) !important;
  border-color: var(--theme-accent) !important;
  color: var(--theme-button-text) !important;
}
/* Para acessibilidade, preserva um foco visível colorido (não uma borda branca). */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v250-weapon,.vault-v261-armor-editor,.vault-v260-shield) :is(button,input,select,textarea):focus-visible {
  outline: 2px solid var(--theme-input-focus) !important;
  outline-offset: 2px;
}
/* Especificidade adicional: o seletor de estado compartilhado acima tem a
   mesma força que o botão original. Priorize a paleta efetiva da ficha. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-card .vault-v263-stealth-switch {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v261-armor-editor .vault-v263-stealth-card .vault-v263-stealth-switch.is-disadvantage {
  background: var(--theme-accent-soft) !important;
  border-color: var(--theme-accent) !important;
  color: var(--theme-text-strong) !important;
}

/* ==============================================================
   SPELLBOUND V66.2.92 — harmonização dos quatro editores.
   Apenas na criação do Inventário. Não altera o Cofre do Mestre.
   =============================================================== */

/* 1 — Propriedades e Maestrias: gavetas, títulos, contadores e descrições. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .master-vault-v10-8-selected,.master-vault-v10-8-drawer-list
) {
  background: transparent !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected-head :is(strong,span),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-title strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-title small {
  color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-selected-head span,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-title small {
  color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .master-vault-v10-8-drawer-head,.master-vault-v10-8-drawer-toggle,.master-vault-v10-8-drawer-remove
) {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-toggle:hover {
  background: var(--theme-hover) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .master-vault-v10-8-drawer-body,.master-vault-v10-8-drawer-body p,.master-vault-v10-8-drawer-body li,.master-vault-v10-8-drawer-body span,
  .master-vault-v10-8-drawer-body strong,.master-vault-v10-8-drawer-body b
) {
  color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-body {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  border-color: var(--theme-border) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .master-vault-v10-8-chevron,.master-vault-v10-8-drawer-remove svg
) { color: var(--theme-text-secondary) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon .master-vault-v10-8-drawer-remove:hover {
  background: var(--theme-hover) !important;
  color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .vault-v250-rule-heading,.vault-v250-rule-heading .master-vault-v4-rule-title,.master-vault-v4-rule-title,.master-vault-v4-rule-title strong
) { color: var(--theme-text-strong) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v250-weapon :is(
  .vault-v250-count,.vault-v250-choice--custom
) {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-secondary) !important;
}

/* 2 — Danos: linhas, resumo, botões de dados, tipos e atributos. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v251-damage,.vault-v254-damage,.vault-v255-damage,.vault-v250-damage-part,.vault-v251-damage-line,.vault-v254-damage-line
) {
  background: var(--theme-module-background) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v250-damage-summary,.vault-v254-dice-panel,.vault-v254-bonus-detail,.vault-v259-modifier-picker,.vault-v258-modifier-picker
) {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v250-damage-summary strong,.vault-v251-damage-line-head strong,.vault-v250-damage-part-header,.vault-v254-dice-d,
  .vault-v255-damage .vault-v254-dice-d,.vault-v259-picker-heading strong,.vault-v259-modifier-chips strong,
  .vault-v259-modifier-picker strong,.vault-v259-attack-icon
) { color: var(--theme-text-strong) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v250-damage-summary span,.vault-v252-field-label,.vault-v254-type-panel > span,.vault-v253-roll-preview,
  .vault-v259-picker-key,.vault-v254-dice-help,.vault-v254-bonus-detail label,.vault-v259-modifier-picker small
) { color: var(--theme-text-secondary) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v254-stepper,.vault-v258-modifier-trigger,.vault-v259-mod-chip,.vault-v258-mod-chip,
  .vault-v251-type-chip,.vault-v254-extra-toggle,.vault-v250-add-damage,.vault-v250-remove-damage,
  .vault-v254-custom-chip,.vault-v259-modifier-chips button
) {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v254-stepper button,.vault-v258-modifier-symbol,.vault-v258-modifier-chevron,
  .vault-v259-chip-state,.vault-v259-attack-icon,.vault-v259-mod-chip .vault-v259-attack-icon
) {
  background: var(--theme-button-soft-bg) !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-button-soft-text) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v254-stepper input,.vault-v254-stepper input:focus
) {
  background: var(--theme-input-bg) !important;
  border-color: var(--theme-input-border) !important;
  color: var(--theme-input-text) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v251-type-chip,.vault-v259-mod-chip,.vault-v258-mod-chip,.vault-v254-extra-toggle,.vault-v250-add-damage,.vault-v258-modifier-trigger
):is(:hover,.is-selected,.is-active,.is-on,.is-open) {
  background: var(--theme-accent-soft) !important;
  border-color: var(--theme-accent) !important;
  color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v258-modifier-label,.vault-v259-mod-chip strong,.vault-v251-type-chip.is-selected,
  .vault-v254-stepper input,.vault-v254-bonus-number input
) { color: var(--theme-text-strong) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v259-mod-chip.is-active .vault-v259-chip-state,.vault-v254-extra-toggle.is-active,
  .vault-v258-modifier-trigger.is-on .vault-v258-modifier-symbol
) { background: var(--theme-accent-strong) !important; color: var(--theme-button-text) !important; }

/* 3 — Habilidades: gavetas, encantamento e lado do rastreador. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .master-vault-v5-ability-drawer,.master-vault-v5-ability-drawer.is-open,.master-vault-v5-ability-content,
  .master-vault-v5-ability-content-grid,.master-vault-v5-ability-side,.vault-v256-ability-side,
  .vault-v253-enchant-ability,.vault-v253-enchant-preview,.vault-v253-enchant-detail
) {
  background: var(--theme-module-background) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .master-vault-v5-ability-bar,.master-vault-v5-ability-drawer.is-open .master-vault-v5-ability-bar,
  .vault-v253-enchant-heading,.vault-v256-tracker-head,.master-vault-v4-toggle-card
) {
  background: var(--theme-band-background) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .master-vault-v5-ability-title strong,.vault-v253-enchant-title > strong,.vault-v253-enchant-preview-heading strong,
  .vault-v256-tracker-head strong,.vault-v256-section-name,.vault-v256-config-field > span,
  .vault-v257-recovery-copy strong,.vault-v259-picker-heading strong,.vault-v256-build-header strong
) { color: var(--theme-text-strong) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .master-vault-v5-ability-title span,.vault-v253-enchant-overline,.vault-v253-enchant-title > small,
  .vault-v253-enchant-preview p,.vault-v253-enchant-preview-heading span,
  .vault-v256-tracker-head small,.vault-v256-mode-select small,.vault-v256-build p,
  .vault-v256-roll-description,.vault-v257-recovery-copy small,.vault-v256-single-note,
  .vault-v256-build-header strong span
) { color: var(--theme-text-secondary) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v253-enchant-preview,.vault-v256-tracker,.vault-v256-config-field,
  .vault-v257-build-recovery,.vault-v257-build-recovery.is-on,.vault-v257-custom-recovery,
  .vault-v256-spend,.vault-v256-build,.vault-v256-roll,.vault-v256-roll-result,
  .vault-v256-complete,.vault-v256-fixed,.vault-v256-progress
) {
  background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v253-enchant-levels button,.vault-v256-mode-select button,
  .vault-v256-start-choices > button,.vault-v257-random-option,
  .vault-v257-random-select,.vault-v256-roll-test,.vault-v256-help summary,
  .vault-v253-enchant-toggle,.vault-v256-mode-mark
) {
  background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
  background-image: none !important;
  border-color: var(--theme-border) !important;
  color: var(--theme-text-primary) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v253-enchant-levels button.is-selected,.vault-v256-mode-select button.is-active,
  .vault-v256-start-choices > button.is-active,.vault-v257-random-option.is-active,
  .vault-v253-enchant-toggle.is-on,.vault-v257-build-recovery.is-on
) {
  background: var(--theme-accent-soft) !important;
  border-color: var(--theme-accent) !important;
  color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v256-mode-select button:hover,.vault-v253-enchant-levels button:hover,
  .vault-v256-start-choices > button:hover,.vault-v256-roll-test:hover
) { background: var(--theme-hover) !important; border-color: var(--theme-accent) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v253-enchant-preview .vault-v253-enchant-preview-heading strong,
  .vault-v256-mode-select button strong,.vault-v256-complete strong,.vault-v256-build-header strong
) { color: var(--theme-text-strong) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v256-mode-select button small,.vault-v256-complete span:not(:first-child),.vault-v256-roll-description
) { color: var(--theme-text-secondary) !important; }
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-levels button.is-selected {
  background: var(--theme-button-soft-bg) !important;
  color: var(--theme-button-soft-text) !important;
  border-color: var(--theme-accent) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v256-mode-mark,.vault-v257-random-help summary,.vault-v256-help summary,
  .vault-v253-enchant-emblem,.master-vault-v5-ability-number
) {
  background: var(--theme-button-soft-bg) !important;
  color: var(--theme-button-soft-text) !important;
  border-color: var(--theme-border-strong) !important;
  box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-help-content,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-help-content::before {
  background: var(--theme-surface-raised, var(--theme-paper-strong)) !important;
  border-color: var(--theme-border-strong) !important;
  color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-help-content :is(strong,p) {
  color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v256-progress span {
  background: var(--theme-accent-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v257-random-option .vault-v257-random-select,.vault-v256-start-choices > button,
  .vault-v253-enchant-preview,.vault-v256-mode-select button
) { box-shadow: none !important; }
/* Foco visível, sem contorno branco do Cofre em todos os temas. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(
  .vault-v250-weapon,.vault-v255-damage,.master-vault-v5-ability-drawer,.vault-v256-ability-side
) :is(button,input,select,summary):focus-visible {
  outline: 2px solid var(--theme-input-focus) !important;
  outline-offset: 2px;
}


/* V66.2.93 — Encantamento: preserva o brilho mágico sem o pergaminho bege
   e aplica os tons legíveis da ficha, inclusive ao tooltip de ajuda. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-ability,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-ability.is-enchanted {
    background:var(--theme-surface-card-alt)!important;
    color:var(--theme-text-primary)!important;
    border:1px solid var(--theme-border-strong)!important;
    box-shadow:none!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-title>strong,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-edit-prompt strong {
    color:var(--theme-text-strong)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v253-enchant-overline,.vault-v253-enchant-bonuses-title) {
    color:var(--theme-label)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal :is(.vault-v253-enchant-title>small,.vault-v258-enchant-edit-prompt small) {
    color:var(--theme-text-secondary)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-edit-prompt {
    background:var(--theme-surface-card)!important;
    border:1px solid var(--theme-border)!important;
    border-radius:11px;
    box-shadow:none!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-edit-prompt button {
    background:var(--theme-accent-soft)!important;
    color:var(--theme-text-strong)!important;
    border-color:var(--theme-accent)!important;
    box-shadow:none!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-edit-prompt button:hover {
    background:var(--theme-hover)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-emblem {
    background:var(--theme-surface-card)!important;
    color:var(--theme-accent-strong)!important;
    border-color:var(--theme-border)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-toggle {
    background:var(--theme-surface-card)!important;
    color:var(--theme-text-primary)!important;
    border-color:var(--theme-border-strong)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-toggle.is-on {
    background:var(--theme-accent-soft)!important;
    color:var(--theme-text-strong)!important;
    border-color:var(--theme-accent)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-levels button {
    background:var(--theme-surface-card)!important;
    color:var(--theme-text-primary)!important;
    border-color:var(--theme-border)!important;
    box-shadow:none!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v253-enchant-levels button.is-selected {
    background:var(--theme-accent-soft)!important;
    color:var(--theme-text-strong)!important;
    border-color:var(--theme-accent)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-help {z-index:45;}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-help summary {
    display:grid;place-items:center;
    background:var(--theme-surface-card)!important;
    color:var(--theme-text-primary)!important;
    border:1px solid var(--theme-border-strong)!important;
    box-shadow:none!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-help summary:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-help[open] summary {
    background:var(--theme-accent-soft)!important;
    border-color:var(--theme-accent)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal .vault-v258-enchant-help-content {
    background:var(--theme-surface-raised,var(--theme-surface-card))!important;
    border:1px solid var(--theme-border-strong)!important;
    color:var(--theme-text-primary)!important;
    box-shadow:var(--theme-shadow-strong)!important;
    border-radius:10px;
    max-width:min(340px,80vw);
    line-height:1.55;
}
/* Biblioteca Arcana do Player: menos cabeçalho e mais área de leitura. */
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog {max-width:1060px;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head {
    min-height:0!important;
    padding:11px 17px!important;
    gap:11px;
    background:var(--theme-modal-header-background,var(--theme-band-background))!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title {gap:10px!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-emblem {width:34px!important;height:34px!important;flex:0 0 34px;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title h3 {font-size:18px!important;line-height:1.15!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title small {font-size:9px!important;line-height:1.1!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-head-title p {display:none!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-tabs {padding-block:5px!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v267-library-dialog .vault-v270-preview-action {
    display:flex!important;flex-direction:row!important;align-items:center!important;
    justify-content:flex-end!important;gap:10px!important;
    padding:10px 15px!important;
    border-top:1px solid var(--theme-border)!important;
    background:var(--theme-surface-card-alt)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-preview-action .vault-v293-preview-status {
    margin-right:auto;
    color:var(--theme-text-secondary)!important;
    font-size:10px;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-preview-action .vault-v267-link-spell {
    display:inline-flex!important;align-items:center;justify-content:center;gap:8px;
    width:auto!important;min-height:36px!important;
    border-radius:8px!important;padding:9px 14px!important;
    background:var(--theme-button-background)!important;
    color:var(--theme-button-text)!important;
    border:1px solid var(--theme-button-bg)!important;
    font-size:11px!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-preview-action .vault-v267-link-spell svg {width:16px;height:16px;flex:none;}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-paper {
    padding:20px 24px!important;
    background:var(--theme-surface-card)!important;
    color:var(--theme-text-primary)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-paper :is(h4,.vault-v272-title-line h4) {
    color:var(--theme-text-strong)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-paper .vault-v272-rich-copy,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-paper .spell-rich-copy {
    line-height:1.68!important;
    color:var(--theme-text-primary)!important;
}
/* Leitor modal efetivo de pergaminhos do Player (independente do Cofre). */
.vault-v293-layer.inventory-vault-themed.character-sheet-theme[data-theme] {
    position:fixed;inset:0;z-index:2147482000;
    display:flex;align-items:center;justify-content:center;
    padding:14px;color:var(--theme-text-primary);
    font-family:Arial,sans-serif;
}
.vault-v293-layer[x-cloak] {display:none!important;}
.vault-v293-layer .vault-v293-backdrop {
    position:absolute;inset:0;
    background:var(--theme-modal-backdrop,rgba(0,0,0,.78));
}
.vault-v293-layer .vault-v293-dialog {
    position:relative;display:flex;flex-direction:column;
    width:min(850px,100%);max-height:min(89dvh,930px);
    min-height:0;overflow:hidden;
    border:1px solid var(--theme-modal-border-strong,var(--theme-border-strong));
    border-radius:16px;
    background:var(--theme-modal-surface,var(--theme-paper-strong));
    color:var(--theme-text-primary);
    box-shadow:var(--theme-modal-shadow,var(--theme-shadow-strong));
}
.vault-v293-layer .vault-v293-head {
    display:flex;justify-content:space-between;align-items:flex-start;gap:16px;
    padding:22px 26px 18px;
    border-bottom:1px solid var(--theme-border);
    background:var(--theme-modal-header-background,var(--theme-band-background));
}
.vault-v293-layer .vault-v293-heading {display:grid;gap:5px;min-width:0;}
.vault-v293-layer .vault-v293-overline {color:var(--theme-label);font:800 10px/1.35 Arial,sans-serif;letter-spacing:.12em;}
.vault-v293-layer .vault-v293-heading h3 {
    margin:0;color:var(--theme-text-strong);
    font:700 clamp(21px,3vw,30px)/1.18 Georgia,serif;
    overflow-wrap:anywhere;
}
.vault-v293-layer .vault-v293-heading em {color:var(--theme-text-secondary);font:italic 600 14px/1.4 Georgia,serif;}
.vault-v293-layer .vault-v293-close {
    display:grid;place-items:center;flex:none;width:34px;height:34px;
    background:var(--theme-surface-card);color:var(--theme-text-primary);
    border:1px solid var(--theme-border);border-radius:9px;
}
.vault-v293-layer .vault-v293-close svg {width:17px;height:17px;}
.vault-v293-layer .vault-v293-close:hover {background:var(--theme-hover);border-color:var(--theme-accent);}
.vault-v293-layer .vault-v293-content {min-height:0;flex:1;overflow:auto;padding:24px 29px;overscroll-behavior:contain;}
.vault-v293-layer .vault-v293-facts {display:grid;gap:7px;margin:0 0 20px;font:13px/1.5 Arial,sans-serif;}
.vault-v293-layer .vault-v293-facts>div {display:flex;align-items:baseline;gap:7px;}
.vault-v293-layer .vault-v293-facts dt {flex:none;font-weight:800;color:var(--theme-text-strong);}
.vault-v293-layer .vault-v293-facts dd {margin:0;color:var(--theme-text-primary);overflow-wrap:anywhere;}
.vault-v293-layer .vault-v293-tags {display:flex;flex-wrap:wrap;gap:7px;margin:-4px 0 18px;}
.vault-v293-layer .vault-v293-tags span {
    border:1px solid var(--theme-border);background:var(--theme-accent-soft);
    color:var(--theme-text-strong);border-radius:999px;padding:5px 9px;
    font:700 10px Arial,sans-serif;
}
.vault-v293-layer .vault-v293-rich {
    font:15px/1.76 Georgia,'Times New Roman',serif;color:var(--theme-text-primary);
    overflow-wrap:anywhere;
}
.vault-v293-layer .vault-v293-rich :is(p,ul,ol,blockquote) {margin:0 0 13px;}
.vault-v293-layer .vault-v293-rich :is(strong,b,h4,h5) {color:var(--theme-text-strong);}
.vault-v293-layer .vault-v293-rich :is(ul,ol) {padding-left:23px;}
.vault-v293-layer .vault-v293-rich li {margin:0 0 5px;}
.vault-v293-layer .vault-v293-higher {padding-top:18px;margin-top:20px;border-top:1px solid var(--theme-border);}
.vault-v293-layer .vault-v293-higher h4 {margin:0 0 10px;color:var(--theme-text-strong);font:700 17px Georgia,serif;}
.vault-v293-layer .vault-v293-loading,.vault-v293-layer .vault-v293-empty {color:var(--theme-text-secondary);font:12px/1.6 Arial,sans-serif;}
.vault-v293-layer .vault-v293-error {
    padding:11px 13px;border:1px solid var(--theme-state-warning,var(--theme-border-strong));
    border-radius:10px;color:var(--theme-text-primary);font:12px/1.5 Arial,sans-serif;
}
.vault-v293-layer .vault-v293-error button {margin-left:10px;text-decoration:underline;color:var(--theme-text-strong);font-weight:700;}
@media(max-width:600px) {
    .vault-v293-layer {padding:5px!important;}
    .vault-v293-layer .vault-v293-dialog {max-height:calc(100dvh - 10px);border-radius:12px;}
    .vault-v293-layer .vault-v293-head {padding:16px;}
    .vault-v293-layer .vault-v293-content {padding:17px 16px;}
    .inventory-vault-themed.character-sheet-theme[data-theme] .vault-v270-spell-paper {padding:16px!important;}
}


/* ============================================================== 
   SPELLBOUND V66.2.94 — CLASSIFICAÇÃO DO INVENTÁRIO DO PLAYER
   Ponte visual com os oito temas reais do character-sheet.css.
   Escopo exclusivo do portal do Player; NÃO altera o Cofre do Mestre.
   Não altera Alpine, dados, eventos, regras nem persistência.
   ============================================================== */

/* Remove a linha clara herdada do editor do Cofre acima da Natureza. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v82-classification {
    border-top: 0 !important;
    margin-top: 15px;
    padding-top: 0;
}

/* Equipável e Sintonização: estados básicos ancorados na paleta da ficha. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal :is(.vault-v46-equip-toggle,.vault-v46-attunement-toggle) {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    border: 1px solid var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal :is(.vault-v46-equip-toggle,.vault-v46-attunement-toggle):hover {
    background: var(--theme-hover) !important;
    border-color: var(--theme-accent) !important;
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal :is(.vault-v46-equip-toggle.is-active,.vault-v46-attunement-toggle.is-on),
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal :is(.vault-v46-equip-toggle.is-active,.vault-v46-attunement-toggle.is-on):hover {
    background: var(--theme-accent-soft) !important;
    background-image: none !important;
    border-color: var(--theme-accent) !important;
    color: var(--theme-text-strong) !important;
    box-shadow: inset 0 0 0 1px var(--theme-border-soft) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal :is(.vault-v46-equip-toggle,.vault-v46-attunement-toggle):focus-visible {
    outline: 2px solid var(--theme-input-focus) !important;
    outline-offset: 2px;
}

/* A mão continua usando os ícones originais, agora com cores do tema. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v46-equip-toggle .vault-v45-hand-icon::before {
    background: var(--theme-surface-card, var(--theme-paper-strong)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v46-equip-toggle .vault-v45-hand-icon::after {
    background: currentColor !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v46-equip-toggle .vault-v46-equip-indicator {
    background: var(--theme-text-secondary) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v46-equip-toggle.is-active .vault-v46-equip-indicator {
    background: var(--theme-accent-strong) !important;
    box-shadow: 0 0 0 2px var(--theme-accent-soft) !important;
}

/* O cristal original mantém sua imagem e a animação de sintonia. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v46-attunement-toggle .vault-v46-attunement-mark {
    background: var(--theme-text-secondary) !important;
    border-color: var(--theme-border-strong) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v46-attunement-toggle.is-on .vault-v46-attunement-mark {
    background: var(--theme-accent-strong) !important;
    border-color: var(--theme-accent-soft) !important;
    box-shadow: 0 0 0 1px var(--theme-accent) !important;
}

/* Vestígio: destaque temático discreto, legível em temas claros/escuros. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    border: 1px solid var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .vault-v16-mode-switch__eyebrow {
    color: var(--theme-accent-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .vault-v16-mode-switch__text strong {
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .vault-v16-mode-switch__text p {
    color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .master-vault-v4-switch {
    background: var(--theme-button-soft-bg) !important;
    border: 1px solid var(--theme-border-strong) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .master-vault-v4-switch.is-on {
    background: var(--theme-accent-strong) !important;
    border-color: var(--theme-accent) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .master-vault-v4-switch::after {
    background: var(--theme-text-primary) !important;
    top: 1px;
    left: 1px;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v18-vestige .master-vault-v4-switch:focus-visible {
    outline: 2px solid var(--theme-input-focus) !important;
    outline-offset: 3px;
}


/* ================================================================
   SPELLBOUND V66.2.95 — ENQUADRAMENTO + VESTÍGIO DO PLAYER
   Portal do Inventário, com as variáveis dos oito temas da ficha.
   Exclusivamente visual: recorte, controles Alpine e dados preservados.
   ================================================================ */

/* 1. Enquadrar foto: janela mais confortável e consistente com o tema. */
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-overlay {
    z-index: 910 !important;
    padding: 16px !important;
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-backdrop {
    background: var(--theme-modal-backdrop) !important;
    backdrop-filter: blur(5px);
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-panel {
    width: min(700px, 100%) !important;
    max-height: min(92dvh, 820px) !important;
    overflow: auto;
    border: 1px solid var(--theme-modal-border-strong, var(--theme-border-strong)) !important;
    border-radius: 15px !important;
    background: var(--theme-modal-surface) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: var(--theme-modal-shadow) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header,
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer {
    background: var(--theme-modal-header-background) !important;
    border-color: var(--theme-modal-border, var(--theme-border)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header h3 {
    color: var(--theme-text-strong) !important;
    font-size: 21px !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header small {
    color: var(--theme-text-secondary) !important;
    font-size: 10px !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header > button {
    display: grid; place-items: center;
    flex: 0 0 32px; width: 32px; height: 32px;
    border: 1px solid var(--theme-border) !important;
    border-radius: 8px !important;
    background: var(--theme-surface-card-alt) !important;
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-header > button:hover {
    border-color: var(--theme-accent) !important;
    background: var(--theme-hover) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-layout {
    display: grid !important;
    grid-template-columns: minmax(0, 282px) minmax(0, 1fr) !important;
    align-items: center !important;
    gap: 24px !important;
    padding: 20px 22px !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-stage {
    box-sizing: border-box;
    width: 282px !important; height: 282px !important;
    max-width: 100% !important; min-width: 0;
    border: 1px solid var(--theme-border-strong) !important;
    border-radius: 13px !important;
    background-color: var(--theme-surface-inset, var(--theme-panel-soft)) !important;
    background-image: repeating-conic-gradient(
        color-mix(in srgb, var(--theme-text-secondary) 10%, transparent) 0% 25%,
        transparent 0% 50%
    ) !important;
    background-size: 20px 20px !important;
    box-shadow: inset 0 0 0 1px var(--theme-border-soft) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-guides {
    inset: 10px !important;
    border: 1px solid rgba(255,255,255,.62) !important;
    border-radius: 9px !important;
    box-shadow: 0 0 0 1px rgba(0,0,0,.34), 0 0 0 999px rgba(0,0,0,.08);
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-tools {
    display: flex !important;
    flex: initial !important;
    flex-direction: column !important;
    min-width: 0 !important;
    width: 100% !important;
    max-width: none !important;
    gap: 15px !important;
    background: transparent !important;
    border: 0 !important;
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    min-width: 0;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control label {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 9px !important;
    color: var(--theme-label, var(--theme-text-primary)) !important;
    font: 750 11px/1.3 system-ui, sans-serif !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control output {
    font: 750 11px/1.3 system-ui, sans-serif !important;
    color: var(--theme-accent-strong) !important;
    font-variant-numeric: tabular-nums;
}
/* Reset específico do range: impede a caixa branca do Zoom e mantém os sliders clicáveis. */
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"] {
    display: block !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    box-sizing: border-box;
    width: 100% !important;
    height: 20px !important;
    min-height: 20px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    accent-color: var(--theme-accent-strong);
    cursor: pointer;
    outline: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]::-webkit-slider-runnable-track {
    height: 6px !important;
    border: 1px solid var(--theme-border-strong) !important;
    border-radius: 999px !important;
    background: var(--theme-surface-inset, var(--theme-input-bg)) !important;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none !important;
    appearance: none !important;
    box-sizing: border-box;
    width: 16px !important; height: 16px !important;
    margin-top: -6px !important;
    border: 2px solid var(--theme-modal-surface) !important;
    border-radius: 50% !important;
    background: var(--theme-accent-strong) !important;
    box-shadow: 0 0 0 1px var(--theme-accent) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]::-moz-range-track {
    height: 6px !important;
    border: 1px solid var(--theme-border-strong) !important;
    border-radius: 999px !important;
    background: var(--theme-surface-inset, var(--theme-input-bg)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]::-moz-range-thumb {
    box-sizing: border-box;
    width: 16px !important; height: 16px !important;
    border: 2px solid var(--theme-modal-surface) !important;
    border-radius: 50% !important;
    background: var(--theme-accent-strong) !important;
    box-shadow: 0 0 0 1px var(--theme-accent) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]:focus-visible::-webkit-slider-thumb {
    box-shadow: 0 0 0 3px var(--theme-focus, var(--theme-accent-soft)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]:focus-visible::-moz-range-thumb {
    box-shadow: 0 0 0 3px var(--theme-focus, var(--theme-accent-soft)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v40-crop-control input[type="range"]:disabled {
    cursor: not-allowed;
    opacity: .5;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
:is(.vault-v40-align-row, .vault-v40-crop-file-actions) button {
    background: var(--theme-surface-card) !important;
    border: 1px solid var(--theme-border) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: none !important;
    text-align: center !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
:is(.vault-v40-align-row, .vault-v40-crop-file-actions) button:hover {
    background: var(--theme-hover) !important;
    border-color: var(--theme-accent) !important;
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer {
    gap: 10px !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer button:not(.vault-v39-crop-apply) {
    background: var(--theme-surface-card-alt) !important;
    border: 1px solid var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer button:not(.vault-v39-crop-apply):hover {
    background: var(--theme-hover) !important;
    border-color: var(--theme-accent) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer .vault-v39-crop-apply {
    background: var(--theme-button-background) !important;
    border: 1px solid var(--theme-button-bg) !important;
    color: var(--theme-button-text) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-footer .vault-v39-crop-apply:hover:not(:disabled) {
    background: var(--theme-button-hover-background) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-overlay button:focus-visible {
    outline: 2px solid var(--theme-input-focus) !important;
    outline-offset: 2px !important;
}

/* 2. Vestígio: ouro em todas as etapas, adequado aos temas claros e escuros. */
.inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) {
    --iv-vestige-ink: #90611f;
    --iv-vestige-sub: #805f34;
    --iv-vestige-metal: #b88d48;
    --iv-vestige-fill: #a47632;
    --iv-vestige-button: #966722;
    --iv-vestige-button-hover: #7e5216;
    --iv-vestige-button-text: #fff9ee;
}
.inventory-vault-themed.character-sheet-theme:is([data-theme="midnight"],[data-theme="obsidian"])
.vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) {
    --iv-vestige-ink: #f0d399;
    --iv-vestige-sub: #d0ad73;
    --iv-vestige-metal: #cba464;
    --iv-vestige-fill: #dbb477;
    --iv-vestige-button: #c9a062;
    --iv-vestige-button-hover: #e4bb7c;
    --iv-vestige-button-text: #282117;
}
/* Cabeçalho dourado apenas quando o item está em modo Vestígio. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) .master-vault-v5-abilities-head {
    background: color-mix(in srgb, var(--theme-band-background) 87%, var(--iv-vestige-metal)) !important;
    border-bottom: 1px solid color-mix(in srgb, var(--iv-vestige-metal) 48%, var(--theme-border)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) .master-vault-v5-abilities-head-main > div > span,
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) .master-vault-v5-abilities-head h3 {
    color: var(--iv-vestige-ink) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) .master-vault-v5-abilities-mark {
    background: color-mix(in srgb, var(--theme-surface-card-alt) 78%, var(--iv-vestige-metal)) !important;
    color: var(--iv-vestige-ink) !important;
    border-color: color-mix(in srgb, var(--iv-vestige-metal) 55%, var(--theme-border)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-abilities:has(.vault-v19-stage-group.is-vestige) .vault-v82-ability-subtitle {
    color: var(--theme-text-secondary) !important;
}
/* A gaveta de cada etapa: dourado contido, sem saturar a superfície. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige {
    background: color-mix(in srgb, var(--theme-module-background) 94%, var(--iv-vestige-fill)) !important;
    border: 1px solid color-mix(in srgb, var(--iv-vestige-metal) 52%, var(--theme-border)) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige.is-open {
    border-color: var(--iv-vestige-metal) !important;
    box-shadow: 0 0 0 1px color-mix(in srgb, var(--iv-vestige-metal) 15%, transparent) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-bar {
    background: color-mix(in srgb, var(--theme-band-background) 90%, var(--iv-vestige-fill)) !important;
    border-bottom-color: transparent !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige.is-open .vault-v19-stage-bar {
    border-bottom-color: color-mix(in srgb, var(--iv-vestige-metal) 43%, var(--theme-border)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-toggle {
    background: transparent !important;
    color: var(--iv-vestige-ink) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-symbol {
    background: color-mix(in srgb, var(--theme-surface-card-alt) 74%, var(--iv-vestige-fill)) !important;
    border: 1px solid var(--iv-vestige-metal) !important;
    color: var(--iv-vestige-ink) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-text strong {
    color: var(--iv-vestige-ink) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-text small {
    color: var(--theme-text-secondary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-count {
    color: var(--iv-vestige-sub) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-count span:first-child {
    background: color-mix(in srgb, var(--theme-surface-card) 83%, var(--iv-vestige-metal)) !important;
    border: 1px solid color-mix(in srgb, var(--iv-vestige-metal) 65%, var(--theme-border)) !important;
    color: var(--iv-vestige-ink) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-chevron {
    color: var(--iv-vestige-metal) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-add {
    background: var(--iv-vestige-button) !important;
    border: 1px solid var(--iv-vestige-metal) !important;
    color: var(--iv-vestige-button-text) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-add:hover {
    background: var(--iv-vestige-button-hover) !important;
    border-color: var(--iv-vestige-button-hover) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-add:focus-visible,
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-toggle:focus-visible {
    outline: 2px solid var(--iv-vestige-metal) !important;
    outline-offset: 3px !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-content {
    background: var(--theme-module-alt-background) !important;
    border-color: color-mix(in srgb, var(--iv-vestige-metal) 35%, var(--theme-border)) !important;
    color: var(--theme-text-primary) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v19-stage-group.is-vestige .vault-v19-stage-lore {
    border-bottom-color: color-mix(in srgb, var(--iv-vestige-metal) 30%, var(--theme-border)) !important;
}

@media (max-width: 690px) {
    .inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-layout {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 18px !important;
        padding: 16px !important;
    }
    .inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-stage {
        align-self: center;
        flex: 0 0 auto !important;
        width: min(282px, 100%) !important;
        height: auto !important;
        aspect-ratio: 1 / 1;
    }
    .inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-tools {
        align-self: stretch;
        width: 100% !important;
    }
}
@media (prefers-reduced-motion: reduce) {
    .inventory-vault-themed.character-sheet-theme[data-theme] .vault-v39-crop-panel *,
    .inventory-vault-themed.character-sheet-theme[data-theme] .vault-v19-stage-group.is-vestige * {
        transition-duration: 0s !important;
    }
}

/* ================================================================
   SPELLBOUND V66.2.97 — NEGRITO EM TEXTO RICO DO INVENTARIO
   O negrito reforca apenas o peso: a cor vem do proprio texto.
   Funciona nos oito temas, sem alterar titulos/etapas douradas.
   O Cofre do Mestre nao e afetado.
   ================================================================ */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v31-rich-field
:is(.vault-v31-prosemirror, .vault-v31-fallback-input, .vault-v31-rich-surface)
:is(strong, b) {
    color: inherit !important;
    font-weight: 800;
}

/* Copias renderizadas da descricao de feiticos dentro do editor. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal :is(.vault-v272-rich-copy, .spell-rich-copy) :is(strong, b),
.vault-v293-layer.inventory-vault-themed.character-sheet-theme[data-theme]
.vault-v293-rich :is(strong, b) {
    color: inherit !important;
}


/* ================================================================
   SPELLBOUND V66.2.98 — PERGAMINHO / FEITICO, TODOS OS TEMAS
   Evita caixas claras herdadas do editor original do Cofre.
   Escopo exclusivo do modal do Inventario do Player.
   ================================================================ */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal {
    --iv-scroll-surface:var(--theme-surface-card, var(--theme-paper-strong));
    --iv-scroll-inset:var(--theme-surface-card-alt, var(--theme-paper-alt));
    --iv-scroll-border:var(--theme-border-strong, var(--theme-border));
    --iv-scroll-text:var(--theme-text-primary);
    --iv-scroll-muted:var(--theme-text-secondary);
}
/* Quatro caixas: Raridade / CD / Ataque / Usos. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings-grid, .vault-v281-settings-values) > :is(div,article,section,label,span),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-stat,.vault-v281-stat-card,.vault-v281-settings-stat,.vault-v281-settings-card,
    .vault-v281-fact,.vault-v281-fact-card,.vault-v281-metric,.vault-v281-metric-card,
    .vault-v281-summary-card,.vault-v281-settings-item,.vault-v281-settings-preview) {
    background:var(--iv-scroll-inset)!important;
    background-image:none!important;
    border-color:var(--iv-scroll-border)!important;
    color:var(--iv-scroll-text)!important;
    box-shadow:none!important;
}
/* Feitico vinculado: evita a faixa branca dentro da habilidade. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-current-spell,.vault-v281-feature-reader,.vault-v281-current-card,
    .vault-v281-spell-card,.vault-v281-spell-preview,.vault-v281-current-preview,
    .vault-v281-current-detail,.vault-v281-current-body,.vault-v281-current-panel,
    .vault-v282-title-body,.vault-v282-title-panel) {
    background:var(--iv-scroll-surface)!important;
    background-image:none!important;
    border-color:var(--iv-scroll-border)!important;
    color:var(--iv-scroll-text)!important;
    box-shadow:none!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings-grid,.vault-v281-settings-values,.vault-v281-current-spell,
    .vault-v281-feature-reader,.vault-v282-title-body)
:is(strong,b,h3,h4) {color:var(--theme-text-strong)!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings-grid,.vault-v281-settings-values,.vault-v281-current-spell,
    .vault-v281-feature-reader,.vault-v282-title-body)
:is(small,em,.vault-v281-settings-label,.vault-v281-settings-hint,.vault-v281-current-copy small)
{color:var(--iv-scroll-muted)!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings-grid,.vault-v281-settings-values,.vault-v281-current-spell,
    .vault-v281-feature-reader,.vault-v282-title-body) :is(p,span,label)
{color:var(--iv-scroll-text)!important;}
/* A ordem de especificidade preserva o destaque dos botoes e das tags especiais. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-scroll-builder,.vault-v281-settings,.vault-v282-title-panel)
:is(input,select,textarea) {
    background:var(--theme-input-bg)!important;
    border-color:var(--theme-input-border)!important;
    color:var(--theme-input-text)!important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-scroll-builder,.vault-v281-settings,.vault-v282-title-panel) input::placeholder
{color:var(--theme-input-placeholder)!important;}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings,.vault-v281-current-spell,.vault-v281-feature-reader,
    .vault-v282-title-panel,.vault-v282-title-body) :is(button,a):focus-visible {
    outline:2px solid var(--theme-input-focus)!important;
    outline-offset:2px;
}


/* ================================================================
   SPELLBOUND V66.2.99 — Pergaminho: estados de hover e etiquetas
   O resumo de ajustes não deve herdar o fundo branco do Cofre.
   As superfícies seguem o tema da ficha (inclui temas claros).
   ================================================================ */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(details.vault-v281-settings,details.vault-v274-scroll-stats) > summary,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(details.vault-v281-settings,details.vault-v274-scroll-stats) > summary:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(details.vault-v281-settings,details.vault-v274-scroll-stats)[open] > summary {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    color: var(--theme-text-primary) !important;
    border-color: var(--theme-border-strong) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(details.vault-v281-settings,details.vault-v274-scroll-stats) > summary:hover,
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(details.vault-v281-settings,details.vault-v274-scroll-stats) > summary:focus-visible {
    background: color-mix(in srgb, var(--theme-accent-strong) 10%, var(--theme-surface-card-alt, var(--theme-paper-alt))) !important;
    border-color: var(--theme-border-strong) !important;
}
/* Regras antigas podem aplicar hover na própria gaveta (não só no summary). */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v281-settings:is(:hover,[open]) {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    color: var(--theme-text-primary) !important;
    border-color: var(--theme-border-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v281-settings summary:is(:hover,:focus-visible) {
    background: color-mix(in srgb, var(--theme-accent-strong) 10%, var(--theme-surface-card-alt, var(--theme-paper-alt))) !important;
    background-image: none !important;
    color: var(--theme-text-primary) !important;
}
/* As quatro informações do resumo devem ser texto, não blocos pretos. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v281-settings > summary :is(.vault-v281-settings-values,.vault-v281-settings-meta,.vault-v281-summary-values),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v281-settings > summary :is(.vault-v281-settings-values,.vault-v281-settings-meta,.vault-v281-summary-values) :is(span,b,strong,small,em,mark,kbd),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v274-scroll-stats > summary :is(span,b,strong,small,em,mark,kbd) {
    background: transparent !important;
    background-image: none !important;
    color: var(--theme-text-secondary) !important;
    box-shadow: none !important;
    text-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v281-settings > summary :is(.vault-v281-settings-values,.vault-v281-settings-meta,.vault-v281-summary-values) :is(span,b,strong,small,em,mark,kbd):hover {
    background: transparent !important;
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.vault-v281-settings > summary :is(svg, .vault-v281-settings-chevron) {
    color: var(--theme-text-secondary) !important;
}
/* Legibilidade dos cards de dados e das habilidades dos feitiços. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings-grid,.vault-v281-settings-content,.vault-v281-feature-reader,.vault-v281-current-spell)
:is(input,select,textarea) {
    background: var(--theme-input-bg) !important;
    border-color: var(--theme-input-border) !important;
    color: var(--theme-input-text) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
:is(.vault-v281-settings-grid,.vault-v281-settings-content,.vault-v281-feature-reader)
:is(.vault-v281-stat,.vault-v281-settings-stat,.vault-v281-settings-card, .vault-v281-settings-preview):hover {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    border-color: var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
}


/* V66.2.100 — Prévia de feitiço dentro da HABILIDADE de pergaminho.
   A paleta deve vir da ficha, nunca da superfície de pergaminho bege.
   A classe da gaveta é aplicada apenas à habilidade spell_scroll_cast. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="spell"],
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="scroll"],
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="linked"],
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="preview"],
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="reader"] {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    border-color: var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="spell"] :is(strong,b,h3,h4),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="scroll"] :is(strong,b,h3,h4) {
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="spell"] :is(em,small),
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .master-vault-v5-ability-content
:is(div,section,article,aside)[class*="scroll"] :is(em,small) {
    color: var(--theme-text-secondary) !important;
}
/* Coloração da área de prévia independente do nome da classe na revisão
   do Cofre: o bloco que contém o botão Ler feitiço recebe uma classe só.
   Evita alterar cards de outros tipos de habilidade. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .inventory-vault-spell-preview-surface {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    border-color: var(--theme-border-strong) !important;
    color: var(--theme-text-primary) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .inventory-vault-spell-preview-surface :is(strong,b,h2,h3,h4) {
    color: var(--theme-text-strong) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v5-ability-drawer.is-player-scroll-cast .inventory-vault-spell-preview-surface :is(em,small) {
    color: var(--theme-text-secondary) !important;
}
/* Maldição criada no inventário: remove qualquer acabamento claro herdado. */
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v4-secret .master-vault-v11-curse-panel {
    background: var(--theme-surface-card-alt, var(--theme-paper-alt)) !important;
    background-image: none !important;
    color: var(--theme-text-primary) !important;
    border-color: var(--theme-border) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme] .master-vault-v4-modal
.master-vault-v4-secret .master-vault-v11-curse-panel :is(strong,small,span,label) {
    color: var(--theme-text-primary) !important;
}


/* SPELLBOUND V66.2.109 — Interruptor Força mínima no editor do Player.
   O Cofre do Mestre não é alterado; os valores vêm dos oito temas. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    column-gap: 12px;
    row-gap: 7px;
    min-width: 0;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
    gap: 8px !important;
    width: auto !important;
    min-width: 116px;
    max-width: 100%;
    min-height: 32px;
    margin: 0 0 0 auto;
    padding: 5px 10px !important;
    box-sizing: border-box;
    appearance: none;
    border: 1px solid var(--theme-border-strong, var(--iv-border)) !important;
    border-radius: 999px !important;
    background: var(--theme-surface-card-alt, var(--iv-card-alt)) !important;
    color: var(--theme-text-primary, var(--iv-text)) !important;
    font: 700 11px/1.2 Arial, sans-serif;
    letter-spacing: 0;
    white-space: nowrap;
    text-align: left;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease, color .16s ease;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch:is(:hover,:focus-visible) {
    border-color: var(--theme-accent, var(--iv-accent)) !important;
    background: var(--theme-accent-soft, var(--iv-selected)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch.is-on {
    border-color: var(--theme-accent, var(--iv-accent)) !important;
    background: var(--theme-accent-soft, var(--iv-selected)) !important;
    color: var(--theme-text-strong, var(--iv-title)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch > .vault-v263-enable-track {
    display: inline-flex !important;
    align-items: center !important;
    flex: 0 0 34px !important;
    width: 34px !important;
    min-width: 34px !important;
    height: 20px !important;
    padding: 3px !important;
    box-sizing: border-box;
    border-radius: 999px;
    background: var(--theme-border-strong, var(--iv-strong-border)) !important;
    box-shadow: none !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch.is-on > .vault-v263-enable-track {
    background: var(--theme-accent-strong, var(--iv-accent)) !important;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch > .vault-v263-enable-track > i {
    display: block;
    flex: 0 0 14px !important;
    width: 14px !important;
    height: 14px !important;
    margin: 0 !important;
    border-radius: 50%;
    background: #fff !important;
    transform: translateX(0) !important;
    transition: transform .16s ease;
}
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch.is-on > .vault-v263-enable-track > i {
    transform: translateX(14px) !important;
}
/* Evita que o seletor global de textos sobrescreva a legenda do botão. */
.inventory-vault-themed.character-sheet-theme[data-theme]
.master-vault-v4-modal .vault-v261-armor-editor .vault-v263-strength-heading > .vault-v263-enable-switch > span:last-child {
    display: inline-block !important;
    flex: 0 0 auto !important;
    width: auto !important;
    max-width: none !important;
    overflow: visible !important;
    margin: 0 !important;
    padding: 0 !important;
    color: inherit !important;
    font: inherit !important;
    line-height: 1.2 !important;
    opacity: 1 !important;
    white-space: nowrap;
}

</style>
@endpush
    @once
    <script>
        // V66.1.33: Tiptap permanece prioritário; modo compatível visual se Vite falhar.
        // Nenhum editor/DOM de terceiros é guardado diretamente no Alpine reativo.
        window.vaultTiptapField = (modelRef, field, maxLength = 30000, placeholder = '', owner = null) => ({
            modelRef, field, maxLength, placeholder, owner,
            richEditor: null,
            mounted: false,
            fallbackInitialized: false,
            changed: 0,
            readyListener: null,

            // Recebe uma função para recuperar o formulário atual. O modal Blade
            // permanece montado entre aberturas: guardar `form` por valor ficava obsoleto.
            activeModel() {
                return typeof this.modelRef === 'function' ? this.modelRef() : this.modelRef;
            },

            resetForCurrentItem() {
                this.unmount();
                this.fallbackInitialized = false;
                this.prepareFallback();
                this.mount();
            },

            init() {
                this.readyListener = () => this.$nextTick(() => this.mount());
                window.addEventListener('spellbound:vault-tiptap-ready', this.readyListener);
                this.$watch('editorOpen', open => {
                    if (open) this.$nextTick(() => this.resetForCurrentItem());
                    else this.unmount();
                });
                if (this.owner) {
                    this.$watch('owner.editor_open', open => {
                        if (open) this.$nextTick(() => this.mount());
                        else this.unmount();
                    });
                }
                this.$nextTick(() => {
                    if (this.editorOpen) this.resetForCurrentItem();
                });
            },

            // O fallback também é contenteditable: os atalhos formatam visualmente,
            // nunca acrescentam ** ou * ao texto do usuário.
            fallbackElement() {
                return this.$refs.fallbackHost?.querySelector('[data-vault-fallback]') ?? null;
            },

            // Conversão e sanitização em lista de tags permitidas (inclusive texto legado).
            safeEditorHtml(raw) {
                if (window.SpellboundVaultRichText?.toEditorHtml) {
                    return window.SpellboundVaultRichText.toEditorHtml(raw);
                }
                const source = String(raw ?? '');
                const escape = text => String(text).replace(/[&<>"']/g, c => ({
                    '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;',
                })[c]);
                const markdown = text => escape(text)
                    .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/(^|\n)\*\*([^*\n]+)(?=\n|$)/g, '$1<strong>$2</strong>')
                    .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
                if (!/<(?:p|br|strong|b|em|i|ul|ol|li|blockquote|h[1-6]|s|del|div|hr)\b/i.test(source)) {
                    const result = [];
                    let paragraph = [];
                    const flush = () => {if (paragraph.length) {result.push('<p>' + paragraph.join('<br>') + '</p>'); paragraph = [];}};
                    for (const line of markdown(source.replace(/\r\n?/g, '\n')).split('\n')) {
                        if (/^\s*(?:-{3,}|\*{3,}|_{3,})\s*$/.test(line)) {flush(); result.push('<hr>');}
                        else if (!line.trim()) flush();
                        else paragraph.push(line);
                    }
                    flush();
                    return result.join('');
                }
                return this.sanitizeFallbackHtml(source);
            },

            sanitizeFallbackHtml(source) {
                const doc = new DOMParser().parseFromString('<body>' + String(source ?? '') + '</body>', 'text/html');
                const allowed = new Set(['p','br','strong','em','s','ul','ol','li','blockquote','h3','code','hr']);
                const blocked = new Set(['script','style','iframe','svg','math','object','embed','form','template']);
                const escape = text => String(text).replace(/[&<>"']/g, c => ({
                    '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;',
                })[c]);
                const clean = node => {
                    if (node.nodeType === 3) return escape(node.textContent)
                        .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/^\*\*([^*\n]+)$/g, '<strong>$1</strong>')
                        .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
                    if (node.nodeType !== 1) return '';
                    const tag = node.tagName.toLowerCase();
                    if (blocked.has(tag)) return '';
                    if (tag === 'hr' || ((tag === 'p' || tag === 'div') && /^(?:-{3,}|\*{3,}|_{3,})$/.test(node.textContent.trim()))) return '<hr>';
                    const body = Array.from(node.childNodes).map(clean).join('');
                    const name = ({b:'strong', i:'em', del:'s', strike:'s', div:'p'})[tag] ?? tag;
                    return allowed.has(name) ? '<' + name + '>' + body + '</' + name + '>' : body;
                };
                return Array.from(doc.body.childNodes).map(clean).join('');
            },

            prepareFallback() {
                if (this.fallbackInitialized) return;
                const editable = this.fallbackElement();
                if (!editable) return;
                editable.setAttribute('data-placeholder', this.placeholder);
                editable.innerHTML = this.safeEditorHtml(this.activeModel()?.[this.field] ?? '');
                this.fallbackInitialized = true;
            },

            syncFallback() {
                if (this.mounted) return;
                const editable = this.fallbackElement();
                const model = this.activeModel();
                if (!editable || !model) return;
                const safe = this.sanitizeFallbackHtml(editable.innerHTML);
                model[this.field] = (safe === '<p><br></p>' || safe === '<br>') ? '' : safe;
                this.changed++;
            },

            pasteFallback(event) {
                event.preventDefault();
                const text = event.clipboardData?.getData('text/plain') ?? '';
                document.execCommand('insertText', false, text);
                this.syncFallback();
            },

            fallbackDividerFromText(event) {
                if (event.key !== 'Enter' && event.key !== ' ') return false;
                const editable = this.fallbackElement();
                const selection = window.getSelection();
                const anchor = selection?.anchorNode;
                if (!editable || !anchor || !editable.contains(anchor)) return false;
                const parent = anchor.nodeType === Node.ELEMENT_NODE ? anchor : anchor.parentElement;
                const block = parent?.closest('p,div,blockquote,li');
                const candidate = block && block !== editable && editable.contains(block)
                    ? block
                    : (anchor.nodeType === Node.TEXT_NODE && anchor.parentElement === editable ? anchor : editable);
                if (!/^(?:-{3,}|\*{3,}|_{3,})$/.test((candidate.textContent ?? '').trim())) return false;
                event.preventDefault();
                const hr = document.createElement('hr');
                const paragraph = document.createElement('p');
                paragraph.appendChild(document.createElement('br'));
                if (candidate === editable) editable.replaceChildren(hr, paragraph);
                else candidate.replaceWith(hr, paragraph);
                const range = document.createRange();
                range.selectNodeContents(paragraph);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
                this.syncFallback();
                return true;
            },

            fallbackHotkey(event) {
                if (this.fallbackDividerFromText(event)) return;
                if (!(event.ctrlKey || event.metaKey) || event.altKey) return;
                const shortcuts = { b:'bold', i:'italic', z:event.shiftKey ? 'redo' : 'undo', y:'redo' };
                const command = shortcuts[String(event.key).toLowerCase()];
                if (!command) return;
                event.preventDefault();
                this.action(command);
            },

            mount() {
                if (this.richEditor || !this.editorOpen || !this.activeModel() || (this.owner && !this.owner.editor_open)) return;
                const creator = window.SpellboundVaultRichText?.createEditor;
                const surface = this.$refs.editorSurface?.querySelector('[data-vault-tiptap-mount]');
                if (!creator || !surface || !surface.isConnected) return;
                this.prepareFallback();
                // Preserva o texto digitado antes da inicialização tardia do módulo.
                this.syncFallback();
                try {
                    this.richEditor = creator({
                        element: surface,
                        content: this.activeModel()?.[this.field] ?? '',
                        placeholder: this.placeholder,
                        onUpdate: html => {
                            const model = this.activeModel();
                            if (model) model[this.field] = html;
                            this.changed++;
                        },
                        onSelectionUpdate: () => { this.changed++; },
                    });
                    this.mounted = true;
                    this.changed++;
                } catch (error) {
                    console.error('[Spellbound] Inicialização do Tiptap falhou:', error);
                    this.richEditor = null;
                    this.mounted = false;
                }
            },

            unmount() {
                if (this.richEditor) {
                    try { this.richEditor.destroy(); } catch (error) { console.warn(error); }
                }
                this.richEditor = null;
                this.mounted = false;
            },

            destroy() {
                if (this.readyListener) {
                    window.removeEventListener('spellbound:vault-tiptap-ready', this.readyListener);
                }
                this.unmount();
            },

            action(command) {
                if (!this.richEditor) this.mount();
                if (this.richEditor) {
                    this.richEditor.execute(command);
                    this.changed++;
                    return;
                }
                // Fallback rich-text de verdade: HTML formatado, não Markdown.
                this.prepareFallback();
                const editable = this.fallbackElement();
                if (!editable) return;
                editable.focus();
                const options = {
                    horizontalRule:'insertHorizontalRule', bold:'bold', italic:'italic', strike:'strikeThrough',
                    bulletList:'insertUnorderedList', orderedList:'insertOrderedList',
                    blockquote:'formatBlock', heading:'formatBlock',
                    undo:'undo', redo:'redo', clear:'removeFormat',
                };
                const cmd = options[command];
                if (!cmd) return;
                const param = command === 'heading' ? 'h3' : (command === 'blockquote' ? 'blockquote' : null);
                document.execCommand(cmd, false, param);
                this.syncFallback();
            },

            active(command) {
                this.changed;
                if (this.richEditor) return this.richEditor.isActive(command);
                const state = {bold:'bold',italic:'italic',strike:'strikeThrough',
                    bulletList:'insertUnorderedList',orderedList:'insertOrderedList'}[command];
                return state && document.activeElement === this.fallbackElement()
                    ? !!document.queryCommandState(state) : false;
            },

            get contentLength() {
                this.changed;
                return String(this.activeModel()?.[this.field] ?? '').length;
            },
        });
    </script>
    @endonce

    {{-- O editor continua utilizável se o bundle Vite não estiver disponível. --}}
    @vite('resources/js/master-vault-richtext.js')


@once
<script>
    // Uma única implementação de interface e regras: o mesmo conteúdo do Cofre.
    // A diferença fica no destino do POST e na emissão de um evento ao Inventário.
    window.characterVaultCreator = (
        initialItems, initialFolders, weaponTraitDefinitions, masteryDefinitions,
        abilityLabels, recoveryLabels, damageTypeDefinitions, urls, folderConfig,
        initialSpellCatalog, initialSpellCampaigns, initialSpellSourceNames
    ) => ({
        initialPlayerInventory: true,
        playerCurseTouched: false,
        _playerSpellPreviewObserver: null,
        @include('master.vault.partials.comportamento')
        // V66.2.98: ao clicar em Editar no detalhe do Inventario, o
        // modal antigo nao e aberto. O mesmo editor do Cofre recebe o item.
        installPlayerEditBridge() {
            if (window.__spellboundInventoryVaultEditBridge) return;
            window.__spellboundInventoryVaultEditBridge = true;
            document.addEventListener('click', (event) => {
                const button = event.target?.closest?.('button');
                if (!button?.closest?.('.inventory-v35-detail-modal')) return;
                const clickExpression = button.getAttribute('@click')
                    ?? button.getAttribute('x-on:click') ?? '';
                if (clickExpression.trim() !== 'openEdit(detailItem)') return;
                const legacy = window.Alpine?.$data?.(button);
                const item = legacy?.detailItem;
                if (!item?.id) return;
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                legacy.closeDetail?.();
                window.dispatchEvent(new CustomEvent('inventory-vault-edit-item', {
                    detail: {item},
                }));
            }, true);
        },
        // Garante que as duas habilidades geradas pelo pergaminho apareçam
        // imediatamente depois de vincular ou trocar a magia. O Cofre continua
        // sendo a fonte da lógica; este complemento só reativa a lista no Player.
        installPlayerScrollWatcher() {
            const resync = () => this.$nextTick(() => {
                this.ensurePlayerScrollAbilities();
                this.$nextTick(() => this.stylePlayerSpellPreview());
            });
            this.$watch('form?.properties?.spell_scroll?.spell_name', resync);
            this.$watch('form?.properties?.spell_scroll?.spell_level', resync);
            this.$watch('editorOpen', open => { if (open) resync(); });
            // A prévia é criada pelo x-for quando a gaveta é aberta.
            // Observa apenas o conteúdo das habilidades e só reage à entrada
            // de novos nós, evitando percorrer o inventário inteiro.
            this.$nextTick(() => {
                const root = this.$refs?.vaultEditorAbilities;
                if (!root || this._playerSpellPreviewObserver) return;
                this._playerSpellPreviewObserver = new MutationObserver(() => {
                    if (this.editorOpen) this.stylePlayerSpellPreview();
                });
                this._playerSpellPreviewObserver.observe(root, {childList:true, subtree:true});
            });
        },
        stylePlayerSpellPreview() {
            const root = this.$refs?.vaultEditorAbilities;
            if (!root) return;
            for (const drawer of root.querySelectorAll('.master-vault-v5-ability-drawer.is-player-scroll-cast')) {
                for (const btn of drawer.querySelectorAll('button,a')) {
                    if (!/ler feitiço/i.test((btn.textContent || '').trim())) continue;
                    let node = btn.parentElement;
                    for (let steps = 0; steps < 5 && node && node !== drawer; steps++, node = node.parentElement) {
                        if (node.classList.contains('master-vault-v5-ability-content')) break;
                        const background = getComputedStyle(node).backgroundColor;
                        const values = background.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/);
                        if (values && Number(values[1]) > 210 && Number(values[2]) > 210 && Number(values[3]) > 190) {
                            node.classList.add('inventory-vault-spell-preview-surface');
                            break;
                        }
                    }
                }
            }
        },
        ensurePlayerScrollAbilities() {
            const properties = this.form?.properties;
            const scroll = properties?.spell_scroll;
            if (!scroll || !String(scroll.spell_name || '').trim()) return;
            if (!Array.isArray(properties.features)) properties.features = [];

            // O método compartilhado do Vault gera as habilidades de regras e
            // de conjuração, preservando descrições editadas e rastreadores.
            if (typeof this.ensureScrollCastFeature === 'function') {
                this.ensureScrollCastFeature(properties);
            }
            const entries = properties.features;
            const findKey = key => entries.find(feature => feature?.system_key === key);
            // Compatibilidade com itens antigos que perderam system_key no banco.
            const rulesLabel = 'Uso e Conjuração de Pergaminhos';
            const oldRules = entries.find(f => String(f?.title || '').trim() === rulesLabel);
            if (oldRules && !findKey('spell_scroll_rules')) oldRules.system_key = 'spell_scroll_rules';
            const castName = typeof this.scrollCastDefaultTitle === 'function'
                ? this.scrollCastDefaultTitle(scroll) : String(scroll.spell_name);
            const oldCast = entries.find(f => !f?.system_key && (
                String(f?.title || '').trim() === castName
                || String(f?.title || '').trim() === 'Conjurar Feitiço'
            ));
            if (oldCast && !findKey('spell_scroll_cast')) oldCast.system_key = 'spell_scroll_cast';

            // Fallback somente quando a geração compartilhada ainda não
            // produziu uma das duas habilidades (em versões antigas do Vault).
            const makeFeature = (key, title, description, usage) => {
                const feature = typeof this.blankFeature === 'function'
                    ? this.blankFeature() : {title:'',description:'',usage:{enabled:false}};
                feature.system_key = key;
                feature.title = title;
                feature.description = description;
                feature.usage = usage;
                feature.editor_open = false;
                return feature;
            };
            if (!findKey('spell_scroll_rules')) {
                const desc = typeof this.scrollRulesDefaultDescription === 'function'
                    ? this.scrollRulesDefaultDescription() : '<p>Regras de uso e conjuração do pergaminho.</p>';
                const usage = typeof this.scrollCastDefaultUsage === 'function'
                    ? this.scrollCastDefaultUsage() : {enabled:false};
                entries.unshift(makeFeature('spell_scroll_rules', rulesLabel, desc, usage));
            }
            if (!findKey('spell_scroll_cast')) {
                const desc = typeof this.scrollCastDefaultDescription === 'function'
                    ? this.scrollCastDefaultDescription(scroll) : '<p>Feitiço vinculado ao pergaminho.</p>';
                entries.splice(Math.min(1,entries.length),0,
                    makeFeature('spell_scroll_cast',castName,desc,{enabled:false}));
            }
            // Substituir a referência é necessário para re-renderizar os
            // x-for existentes em algumas versões do Alpine/Blade.
            properties.features = [...entries];
        },
        openPlayerEdit(item) {
            if (!item?.id || this.saving) return;
            this.playerCurseTouched = false;
            this.openEdit(item);
            // A edicao de um item existente nao pode alterar maldicoes
            // ocultas do Mestre nem campos administrativos privados.
            this.playerEditingOriginalId = Number(item.id);
        },
        async deleteItem() {
            if (this.editingId === null || this.editingId === undefined || this.saving) return;
            if (!window.confirm('Excluir este item do inventário permanentemente?')) return;
            this.saving = true;
            this.saveError = null;
            try {
                const id = Number(this.editingId);
                const response = await fetch(this.urls.destroy.replace('__ITEM__', String(id)), {
                    method: 'DELETE', headers: this.headers(),
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data?.message || 'Não foi possível excluir o item.');
                const inventoryRoot = document.querySelector('.inventory-v10[x-data]');
                const inventory = inventoryRoot ? window.Alpine?.$data?.(inventoryRoot) : null;
                if (inventory) {
                    inventory.items = inventory.items.filter(entry => Number(entry.id) !== id);
                    if (Number(inventory.detailItem?.id) === id) inventory.closeDetail?.();
                    inventory.showToast?.('Item excluído do inventário.');
                }
                window.dispatchEvent(new CustomEvent('inventory-vault-item-deleted', {
                    detail: {item: {id, deleted: true}},
                }));
                window.dispatchEvent(new CustomEvent('character-equipment-updated', {
                    detail: {item: {id, deleted: true}},
                }));
                this.saving = false;
                this.closeEditor();
            } catch (error) {
                this.saveError = error?.message || 'Não foi possível excluir o item.';
                this.saving = false;
            }
        },
        async saveItem() {
            if (this.saving || !this.form) return;
            if (!this.form.name?.trim()) {
                this.saveError = 'Informe o nome do item.';
                return;
            }
            const weapon = this.form?.properties?.weapon;
            if (this.itemCategory() === 'weapon' && weapon) {
                if (!Array.isArray(weapon.allowed_abilities) || weapon.allowed_abilities.length === 0) {
                    this.saveError = 'Selecione pelo menos um atributo da arma.';
                    return;
                }
                if ([weapon.damage, ...(weapon.extra_damage || [])].some(d => !this.damageExpressionValid(d))) {
                    this.saveError = 'Confira os dados de dano da arma.';
                    return;
                }
            }
            const groups = [this.form?.properties?.features || []];
            if (this.form?.properties?.vestige?.enabled) {
                for (const stage of ['dormant','awakened','exalted']) {
                    groups.push(this.form.properties.vestige.stages?.[stage]?.features || []);
                }
            }
            if (groups.some(group => group.some(feature =>
                feature?.usage?.enabled && feature.usage.counter_mode !== 'build' &&
                feature.usage.initial_mode === 'roll' &&
                !this.featureRollFormulaValid(feature.usage.initial_formula)
            ))) {
                this.saveError = 'Confira a rolagem inicial do rastreador (ex.: 2d6+1).';
                return;
            }
            this.saveError = null;
            this.saving = true;
            try {
                this.ensurePlayerScrollAbilities();
                const payload = this.buildPayload();
                const editing = this.editingId !== null && this.editingId !== undefined;
                // O jogador so cria novas maldicoes visiveis. Em itens ja
                // existentes os campos de maldicao nao sao enviados: isso
                // protege segredos e respeita a validacao do servidor.
                delete payload.curse_revealed;
                delete payload.notes;
                delete payload.tags;
                // Só envia a maldição em edição quando foi criada pelo Player
                // (marcada pelo servidor) ou quando o usuário clicou em Adicionar.
                // Não altera maldições ocultas do Mestre durante uma edição comum.
                if (editing && !this.playerCurseTouched && !this.form?.properties?._player_curse_created) {
                    delete payload.is_cursed;
                    delete payload.curse_name;
                    delete payload.curse_description;
                } else if (payload.is_cursed && !String(payload.curse_name || '').trim()) {
                    this.saveError = 'Informe o nome da maldição.';
                    this.saving = false;
                    return;
                }
                const formData = this.buildFormData(payload);
                if (editing) formData.append('_method', 'PATCH');
                const url = editing
                    ? this.urls.update.replace('__ITEM__', String(this.editingId))
                    : this.urls.store;
                const response = await fetch(url, {
                    method: 'POST', headers: this.headers(), body: formData,
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data?.item) {
                    const errors = data?.errors ? Object.values(data.errors).flat().filter(Boolean) : [];
                    throw new Error(errors.join(' ') || data?.message || 'Não foi possível criar o item.');
                }
                // Confirmar que o servidor preservou as opções avançadas.
                const before = payload.properties || {};
                const after = data.item.properties || {};
                const warnings = [];
                if ((before?.weapon?.allowed_abilities || []).includes('none')
                    && !(after?.weapon?.allowed_abilities || []).includes('none')) {
                    warnings.push('O servidor não preservou Sem Atributo.');
                }
                const featuresBefore = before?.features || [];
                const featuresAfter = after?.features || [];
                if (featuresBefore.length !== featuresAfter.length) {
                    warnings.push('Algumas habilidades não foram preservadas.');
                }
                // O POST já foi concluído: nunca informar falha nesse ponto,
                // senão o jogador poderá repetir e criar o item em duplicidade.
                if (editing) {
                    // Atualiza a mesma colecao reativa usada pelos cards e
                    // pela janela de detalhes; nunca cria um item duplicado.
                    const inventoryRoot = document.querySelector('.inventory-v10[x-data]');
                    const inventory = inventoryRoot ? window.Alpine?.$data?.(inventoryRoot) : null;
                    if (inventory?.upsertItem) inventory.upsertItem(data.item);
                    window.dispatchEvent(new CustomEvent('inventory-vault-item-updated', {
                        detail: {item: data.item, warning: warnings.join(' ')}
                    }));
                    window.dispatchEvent(new CustomEvent('character-equipment-updated', {
                        detail: {item: data.item}
                    }));
                } else {
                    window.dispatchEvent(new CustomEvent('inventory-vault-item-created', {
                        detail: {item: data.item, warning: warnings.join(' ')}
                    }));
                }
                this.saving = false;
                this.closeEditor();
            } catch(error) {
                this.saveError = error?.message || 'Não foi possível criar o item.';
                this.saving = false;
            }
        },
    });
</script>
@endonce
{{-- O componente não deve participar do layout da ficha. O modal do editor
     usa x-teleport="body" e é exibido somente quando o jogador clica Novo Item. --}}
<div class="inventory-vault-creator" style="display:contents"
    x-data="characterVaultCreator(
        [], [], @js($weaponTraitDefinitions), @js($masteryDefinitions),
        @js($abilityLabels), @js($recoveryLabels), @js($damageTypeDefinitions),
        {store: @js(route('characters.items.store', ['character' => $character])),
         spellDetail: @js(route('characters.items.sync', ['character' => $character])),
         update: @js(route('characters.items.update', ['character' => $character, 'item' => '__ITEM__'])),
         destroy: @js(route('characters.items.destroy', ['character' => $character, 'item' => '__ITEM__']))},
        {ready: false, scopeId: null, userId: @js(auth()->id())},
        @js($vaultSpellCatalog), @js($vaultSpellCampaigns), @js($vaultSpellSourceNames)
    )"
    x-init="folderSystemReady = false; installPlayerEditBridge(); installPlayerScrollWatcher()"
    @inventory-vault-new-item.window="playerCurseTouched = false; openCreate()"
    @inventory-vault-edit-item.window="openPlayerEdit($event.detail?.item)"
    @keydown.escape.window="scrollReaderOpen ? closeScrollReader() : (scrollBookModalOpen ? closeScrollLibrary() : (editorOpen ? closeEditor() : null))">
    {{-- NÃO incluir master.vault.partials.biblioteca aqui: esse partial é a
         listagem COMPLETA do Cofre (cards, pesquisa e Novo Item), não a
         biblioteca arcana. A biblioteca de feitiços já faz parte do editor. --}}
    {{-- V66.2.96: personalização somente do HTML RENDERIZADO do editor do Player.
         O Blade compartilhado do Mestre permanece intacto. --}}
    @php
        $inventoryVaultCreator = true;
        ob_start();
    @endphp
    @include('master.vault.partials.editor')
    @php
        $playerEditorHtml = ob_get_clean();
        // O backend do Player preserva maldições já existentes ao editar.
        // Exibimos essa seção somente ao criar itens novos para não
        // oferecer alterações que o servidor recusaria.
        $playerEditorHtml = str_replace(
            '<section class="master-vault-v4-secret" x-ref="vaultEditorSecrets">',
            '<section class="master-vault-v4-secret" x-ref="vaultEditorSecrets" x-show="!editingId || !form?.is_cursed || !!form?.properties?._player_curse_created" x-cloak>',
            $playerEditorHtml
        );
        // Editar uma maldição nova deve registrar uma interação explícita,
        // sem enviar campos de maldição no salvamento de um item comum.
        $playerEditorHtml = str_replace(
            '@click="form.is_cursed = true"',
            '@click="form.is_cursed = true; playerCurseTouched = true"',
            $playerEditorHtml
        );
        $playerEditorHtml = str_replace(
            "form.is_cursed = false; form.curse_name = ''; form.curse_description = ''",
            "form.is_cursed = false; form.curse_name = ''; form.curse_description = ''; playerCurseTouched = true",
            $playerEditorHtml
        );
        // Identifica precisamente a gaveta de conjuração para não aplicar
        // o tema do feitiço nas habilidades de armas/armaduras.
        $playerEditorHtml = preg_replace(
            '~(:class="\{\s*)(\'is-open\': feature\.editor_open)~',
            '$1\'is-player-scroll-cast\': feature.system_key === \'spell_scroll_cast\', $2',
            $playerEditorHtml,
            1
        );
        $playerEditorHtml = str_replace('Excluir do Vault', 'Excluir do Inventário', $playerEditorHtml);
        $playerEditorHtml = str_replace(
            [
                '<span>Informações Privadas</span>',
                '<h3>Camada do Mestre</h3>',
                'Oculta do Player até a revelação.',
                '<span>Descrição Secreta da Maldição</span>',
                'placeholder="Somente o Mestre conhece esta descrição até a revelação."',
            ],
            [
                '<span>CARACTERÍSTICAS ESPECIAIS</span>',
                '<h3>Maldição do Item</h3>',
                'Descreva os efeitos da maldição que você criou para este item.',
                '<span>Descrição da Maldição</span>',
                'placeholder="Descreva como a maldição afeta o portador, suas condições e consequências."',
            ],
            $playerEditorHtml
        );
        // O Player não possui a área administrativa de anotações secretas.
        $playerEditorHtml = preg_replace(
            '~\s*<label class="master-vault-v4-field">\s*<span>Notas Privadas do Mestre</span>.*?</label>~s',
            '',
            $playerEditorHtml,
            1
        );
    @endphp
    {!! $playerEditorHtml !!}

    {{-- V66.2.93: o botão Ler feitiço requer um modal DENTRO do x-data do
         criador. A página do Cofre tem um leitor próprio, o Player não tinha. --}}
    <template x-teleport="body">
        <div class="vault-v293-layer inventory-vault-themed character-sheet-theme"
             data-theme="{{ $characterTheme ?? 'spellbound' }}"
             :data-theme="document.querySelector('.character-sheet-theme[data-theme]:not(.inventory-vault-themed)')?.dataset.theme || @js($characterTheme ?? 'spellbound')"
             x-show="scrollReaderOpen" x-cloak
             role="presentation" @keydown.escape.stop.prevent="closeScrollReader()">
            <div class="vault-v293-backdrop" @click="closeScrollReader()"></div>
            <section class="vault-v293-dialog" role="dialog" aria-modal="true"
                     aria-label="Ler o feitiço do pergaminho" tabindex="-1">
                <header class="vault-v293-head">
                    <div class="vault-v293-heading">
                        <span class="vault-v293-overline">PERGAMINHO · GRIMÓRIO</span>
                        <h3 x-text="scrollSpellReaderData(scrollReaderItem).name"></h3>
                        <em x-text="scrollSpellReaderData(scrollReaderItem).subtitle"></em>
                    </div>
                    <button type="button" class="vault-v293-close" aria-label="Fechar leitura do feitiço"
                            @click="closeScrollReader()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 5 19 19M19 5 5 19" stroke-linecap="round"/></svg>
                    </button>
                </header>
                <main class="vault-v293-content">
                    <dl class="vault-v293-facts">
                        <div><dt>Conjuração:</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).castingTime"></dd></div>
                        <div><dt>Alcance:</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).range"></dd></div>
                        <div><dt>Componentes:</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).components"></dd></div>
                        <div><dt>Duração:</dt><dd x-text="scrollSpellReaderData(scrollReaderItem).duration"></dd></div>
                    </dl>
                    <div class="vault-v293-tags" x-show="scrollSpellReaderData(scrollReaderItem).ritual || scrollSpellReaderData(scrollReaderItem).concentration" x-cloak>
                        <span x-show="scrollSpellReaderData(scrollReaderItem).ritual">Ritual</span>
                        <span x-show="scrollSpellReaderData(scrollReaderItem).concentration">Concentração</span>
                    </div>
                    <p class="vault-v293-loading" x-show="scrollSpellReaderData(scrollReaderItem).loading" x-cloak>Carregando o feitiço do Grimório...</p>
                    <div class="vault-v293-error" x-show="scrollSpellReaderData(scrollReaderItem).error" x-cloak>
                        Não foi possível carregar o verbete. Verifique o vínculo com o Grimório.
                        <button type="button" @click="requestScrollSpellReader(scrollReaderItem,true)">Tentar novamente</button>
                    </div>
                    <div class="vault-v293-rich spell-reader-copy spell-rich-copy"
                         x-show="!!scrollSpellReaderHtml(scrollReaderItem,'description')" x-cloak
                         x-html="scrollSpellReaderHtml(scrollReaderItem,'description')"></div>
                    <div class="vault-v293-higher" x-show="!!scrollSpellReaderHtml(scrollReaderItem,'higher')" x-cloak>
                        <h4 x-text="scrollSpellReaderData(scrollReaderItem).higherLabel"></h4>
                        <div class="vault-v293-rich spell-reader-copy spell-rich-copy" x-html="scrollSpellReaderHtml(scrollReaderItem,'higher')"></div>
                    </div>
                    <p class="vault-v293-empty"
                       x-show="!scrollSpellReaderData(scrollReaderItem).loading && !scrollSpellReaderData(scrollReaderItem).error && !scrollSpellReaderHtml(scrollReaderItem,'description')"
                       x-cloak>O verbete deste feitiço ainda não possui descrição disponível no Grimório.</p>
                </main>
            </section>
        </div>
    </template>
</div>
