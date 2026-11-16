{{--
    V66.0.40.1 — NPCs aliados integrados ao módulo de Aliados da gaveta Party.
    Lê o campaignId do Alpine pai e só consulta com a gaveta aberta.
    Depende do endpoint characters.party.combat-allies (V66.0.40).
--}}
@once
    @push('styles')
        <style>
            .party-v4 .sb-party-npc-allies {
                margin-top: 17px;
                padding-top: 14px;
                border-top: 1px solid var(--theme-divider, var(--theme-border, #d8c6b3));
                min-width: 0;
            }
            .party-v4 .sb-party-npc-allies__heading {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 6px 12px;
                margin: 0 0 10px;
            }
            .party-v4 .sb-party-npc-allies__title {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                color: var(--theme-text-strong, #53150f);
                font: 800 11px/1.25 system-ui, sans-serif;
                letter-spacing: .09em;
                text-transform: uppercase;
            }
            .party-v4 .sb-party-npc-allies__title::before {
                content: '';
                display: inline-block;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #159d7d;
                box-shadow: 0 0 0 3px rgba(21, 157, 125, .12);
            }
            .party-v4 .sb-party-npc-allies__encounter {
                font: 600 9px/1.3 system-ui, sans-serif;
                color: var(--theme-text-secondary, #8c6239);
                max-width: 240px;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
            .party-v4 .sb-party-npc-allies__list {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(min(100%, 225px), 1fr));
                gap: 9px;
            }
            .party-v4 .sb-party-npc-allies__item {
                display: flex;
                gap: 10px;
                align-items: center;
                min-width: 0;
                padding: 8px;
                border-radius: 12px;
                border: 1px solid var(--theme-border, #ddcbb5);
                background: var(--theme-surface-card, #fffaf2);
                color: var(--theme-text-primary, #4a2e24);
                box-shadow: 0 2px 8px rgba(0,0,0,.035);
                transition: background-color .2s, border-color .2s;
            }
            .party-v4 .sb-party-npc-allies__item:hover {
                background: var(--theme-card-hover, var(--theme-surface-card, #fffaf2));
                border-color: var(--theme-border-strong, var(--theme-border, #ddcbb5));
            }
            .party-v4 .sb-party-npc-allies__portrait {
                position: relative;
                width: 54px;
                height: 60px;
                flex: 0 0 54px;
                display: grid;
                place-items: center;
                overflow: hidden;
                border-radius: 8px;
                border: 1px solid var(--theme-border, #d8c6b3);
                background: var(--theme-surface-inset, #ece0cf);
                color: var(--theme-text-strong, #53150f);
                font: 900 22px/1 Georgia, serif;
            }
            .party-v4 .sb-party-npc-allies__portrait img {
                width: 100%; height: 100%; object-fit: cover; object-position: center top;
                display: block;
            }
            .party-v4 .sb-party-npc-allies__item--down .sb-party-npc-allies__portrait img {
                filter: grayscale(.85) brightness(.78);
            }
            .party-v4 .sb-party-npc-allies__details { min-width: 0; flex: 1; }
            .party-v4 .sb-party-npc-allies__name {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                font: 800 12px/1.4 Georgia, serif;
                color: var(--theme-text-strong, #53150f);
            }
            .party-v4 .sb-party-npc-allies__meta {
                display: flex;
                align-items: baseline;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 3px;
                margin-top: 5px;
                color: var(--theme-text-secondary, #8c6239);
                font: 700 10px/1.2 system-ui, sans-serif;
            }
            .party-v4 .sb-party-npc-allies__meta b {
                color: var(--theme-text-primary, #51392d);
                font-variant-numeric: tabular-nums;
                font-weight: 800;
            }
            .party-v4 .sb-party-npc-allies__hp {
                height: 6px;
                border-radius: 999px;
                background: color-mix(in srgb, var(--theme-text-secondary, #8c6239) 17%, transparent);
                overflow: hidden;
                margin-top: 7px;
            }
            .party-v4 .sb-party-npc-allies__hp-fill {
                height: 100%;
                border-radius: inherit;
                background: #159d7d;
                transition: width .35s ease-out, background-color .25s ease-out;
            }
            .party-v4 .sb-party-npc-allies__hp-fill--wounded { background: #da9a3b; }
            .party-v4 .sb-party-npc-allies__hp-fill--critical { background: #c64c46; }
            .party-v4 .sb-party-npc-allies__sub {
                display: block;
                margin-top: 5px;
                color: var(--theme-text-secondary, #8c6239);
                font: 600 9px/1.2 system-ui, sans-serif;
            }
            .party-v4 .sb-party-npc-allies__empty {
                margin: 8px 0 2px;
                font: 600 10px/1.5 system-ui, sans-serif;
                color: var(--theme-text-secondary, #8c6239);
            }
            @media (prefers-reduced-motion: reduce) {
                .party-v4 .sb-party-npc-allies__hp-fill { transition: none; }
            }
        </style>
    @endpush
@endonce

<section
    class="sb-party-npc-allies"
    x-data="partyCombatAlliesV401()"
    data-url="{{ route('characters.party.combat-allies', $character) }}"
    x-effect="syncCampaign(drawer === 'party' && loaded ? campaignId : null)"
    x-cloak
    x-show="selectedCampaignId && (allies.length > 0 || (combatId && !loading))"
    aria-label="NPCs aliados no combate"
>
    <div class="sb-party-npc-allies__heading">
        <h3 class="sb-party-npc-allies__title">NPCs aliados</h3>
        <span class="sb-party-npc-allies__encounter" x-show="combatName" :title="combatName" x-text="combatName"></span>
    </div>

    <div class="sb-party-npc-allies__list" x-show="allies.length > 0">
        <template x-for="ally in allies" :key="ally.id">
            <article class="sb-party-npc-allies__item" :class="{'sb-party-npc-allies__item--down': ally.is_dead || Number(ally.current_hp) <= 0}">
                <div class="sb-party-npc-allies__portrait">
                    <template x-if="ally.image_url">
                        <img :src="ally.image_url" :alt="ally.name" x-on:error="ally.image_url = null">
                    </template>
                    <template x-if="!ally.image_url">
                        <span x-text="String(ally.name || '?').charAt(0).toUpperCase()"></span>
                    </template>
                </div>
                <div class="sb-party-npc-allies__details">
                    <p class="sb-party-npc-allies__name" :title="ally.name" x-text="ally.name"></p>
                    <div class="sb-party-npc-allies__meta">
                        <span>Pontos de vida</span>
                        <b x-text="`${ally.current_hp} / ${ally.max_hp}`"></b>
                    </div>
                    <div class="sb-party-npc-allies__hp" role="meter" :aria-label="`Pontos de vida de ${ally.name}`" :aria-valuemin="0" :aria-valuemax="ally.max_hp" :aria-valuenow="ally.current_hp">
                        <div class="sb-party-npc-allies__hp-fill" :class="{'sb-party-npc-allies__hp-fill--wounded': ally.hp_percent <= 50 && ally.hp_percent > 25, 'sb-party-npc-allies__hp-fill--critical': ally.hp_percent <= 25}" :style="`width: ${Math.max(0, Math.min(100, Number(ally.hp_percent) || 0))}%`"></div>
                    </div>
                    <span class="sb-party-npc-allies__sub" x-show="Number(ally.temporary_hp) > 0" x-text="`Escudo: ${ally.temporary_hp} PV temporários`"></span>
                    <span class="sb-party-npc-allies__sub" x-show="ally.is_dead || Number(ally.current_hp) <= 0">Fora de combate</span>
                </div>
            </article>
        </template>
    </div>
    <p class="sb-party-npc-allies__empty" x-show="combatId && !loading && allies.length === 0">Nenhum NPC aliado neste encontro.</p>
</section>

@once
    <script>
        function partyCombatAlliesV401() {
            return {
                allies: [],
                combatId: null,
                combatName: '',
                selectedCampaignId: null,
                loading: false,
                timer: null,
                requestGeneration: 0,
                init() {
                    this.timer = setInterval(() => {
                        if (document.visibilityState === 'visible' && this.selectedCampaignId && !this.loading) {
                            void this.refresh();
                        }
                    }, 4000);
                },
                destroy() {
                    if (this.timer) clearInterval(this.timer);
                    this.requestGeneration++;
                },
                syncCampaign(value) {
                    const next = Number(value) || null;
                    if (next === this.selectedCampaignId) return;
                    this.selectedCampaignId = next;
                    this.allies = [];
                    this.combatId = null;
                    this.combatName = '';
                    this.loading = false;
                    this.requestGeneration++;
                    if (next) void this.refresh();
                },
                async refresh() {
                    const requestedCampaign = this.selectedCampaignId;
                    if (!requestedCampaign || this.loading) return;
                    const generation = ++this.requestGeneration;
                    this.loading = true;
                    try {
                        const url = new URL(this.$el.dataset.url, window.location.origin);
                        url.searchParams.set('campaign_id', String(requestedCampaign));
                        const response = await fetch(url.toString(), {
                            credentials: 'same-origin',
                            cache: 'no-store',
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!response.ok) throw new Error('Não foi possível carregar os aliados do encontro.');
                        const data = await response.json();
                        if (generation === this.requestGeneration && requestedCampaign === this.selectedCampaignId) {
                            this.allies = Array.isArray(data.allies) ? data.allies : [];
                            this.combatId = data.combat_id || null;
                            this.combatName = data.combat_name || '';
                        }
                    } catch (error) {
                        console.warn('Party: NPCs aliados indisponíveis:', error);
                    } finally {
                        if (generation === this.requestGeneration) this.loading = false;
                    }
                },
            };
        }
    </script>
@endonce
