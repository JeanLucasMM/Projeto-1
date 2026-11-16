<template x-teleport="body">

    <div

        x-show="catalogOpen"

        x-cloak

        class="character-spellbook-v4 sb-modal-backdrop fixed inset-0 z-[120] flex items-stretch justify-center p-2 backdrop-blur-sm sm:p-4"

        :style="spellbookThemeStyle"

        @click.self="closeCatalog()"

    >

        <div

            x-data="{ filtersOpen: false }"

            class="sb-modal-shell flex h-full w-full max-w-[1480px] flex-col overflow-hidden rounded-[24px] border"

        >

            {{-- =========================================================

                 CABEÇALHO COMPACTO

            ========================================================== --}}

            <header class="sb-paper shrink-0 border-b sb-border-soft px-4 py-3 sm:px-5">

                <div class="flex flex-col gap-3 xl:flex-row xl:items-center">

                    <div class="flex min-w-0 shrink-0 items-center gap-3 xl:min-w-[300px]">

                        <span

                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border sb-border-soft"

                            style="background: var(--spellbook-accent-soft); color: var(--spellbook-accent);"

                        >

                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">

                                <path d="M5 5h14v14H5z"></path>

                                <path d="M8 8h8M8 12h8M8 16h5"></path>

                            </svg>

                        </span>

                        <div class="min-w-0">

                            <p class="sb-label text-[7px] font-black uppercase tracking-[0.14em]">Catálogo do Grimório</p>

                            <div class="mt-0.5 flex flex-wrap items-center gap-2">

                                <h3 class="sb-title font-serif text-[24px] font-black leading-none">Escolher feitiços</h3>

                                <span

                                    class="rounded-full border sb-border-soft px-2 py-0.5 text-[6px] font-black uppercase tracking-[0.08em]"

                                    style="background: var(--spellbook-accent-soft); color: var(--spellbook-accent);"

                                >

                                    XPHB preferencial

                                </span>

                            </div>

                        </div>

                    </div>

                    <label class="relative min-w-0 flex-1">

                        <svg class="sb-muted pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                            <circle cx="11" cy="11" r="7"></circle>

                            <path d="m20 20-3.5-3.5"></path>

                        </svg>

                        <input

                            type="search"

                            x-model="catalogSearch"

                            @input.debounce.350ms="loadCatalog(1)"

                            placeholder="Buscar por nome, escola, fonte ou descrição..."

                            class="sb-input h-10 w-full rounded-xl border pl-10 pr-3 text-[11px] font-medium focus:ring-2"

                        >

                    </label>

                    <div class="flex shrink-0 items-center gap-2">

                        <span class="sb-muted hidden text-[9px] font-bold sm:inline" x-text="`${catalogPage.total} resultado(s)`"></span>

                        <button

                            type="button"

                            @click="filtersOpen = !filtersOpen"

                            class="sb-button-soft inline-flex h-10 items-center gap-2 rounded-xl border px-3 text-[8px] font-black uppercase tracking-[0.08em]"

                        >

                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z"></path>

                            </svg>

                            Filtros

                        </button>

                        <button

                            type="button"

                            @click="closeCatalog()"

                            class="sb-button-soft flex h-10 w-10 items-center justify-center rounded-xl border text-lg"

                            aria-label="Fechar catálogo"

                        >

                            ×

                        </button>

                    </div>

                </div>

                <div

                    x-show="filtersOpen"

                    x-collapse

                    class="sb-panel mt-3 rounded-2xl border sb-border-soft p-3"

                >

                    <div x-show="catalogMeta.lists.length > 0">

                        <div class="flex items-center justify-between gap-3">

                            <span class="sb-label text-[7px] font-black uppercase tracking-[0.12em]">Listas de feitiço</span>

                            <button type="button" @click="clearCatalogLists()" class="sb-accent text-[7px] font-black uppercase tracking-[0.08em] hover:underline">Mostrar todas</button>

                        </div>

                        <div class="mt-2 flex flex-wrap gap-1.5">

                            <template x-for="list in catalogMeta.lists" :key="list.code">

                                <button

                                    type="button"

                                    @click="toggleCatalogList(list.code)"

                                    class="rounded-full border px-2.5 py-1.5 text-[8px] font-black transition"

                                    :style="listSelected(list.code)

                                        ? `border-color: var(--spellbook-accent); background: var(--spellbook-accent); color: var(--spellbook-button-text)`

                                        : `border-color: var(--spellbook-border); background: var(--spellbook-paper-strong); color: var(--spellbook-muted)`"

                                    x-text="list.label"

                                ></button>

                            </template>

                        </div>

                    </div>

                    <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-4">

                        <select x-model="catalogLevel" @change="loadCatalog(1)" class="sb-input rounded-xl border px-3 py-2.5 text-[10px] font-bold">

                            <option value="">Todos os círculos</option>

                            <option value="0">Truques</option>

                            <template x-for="n in 9" :key="n"><option :value="n" x-text="`${n}º círculo`"></option></template>

                        </select>

                        <select x-model="catalogSchool" @change="loadCatalog(1)" class="sb-input rounded-xl border px-3 py-2.5 text-[10px] font-bold">

                            <option value="">Todas as escolas</option>

                            <template x-for="school in catalogMeta.schools" :key="school"><option :value="school" x-text="school"></option></template>

                        </select>

                        <select x-model="catalogProvider" @change="loadCatalog(1)" class="sb-input rounded-xl border px-3 py-2.5 text-[10px] font-bold">

                            <option value="">Todas as origens</option>

                            <option value="5etools">Oficial</option>

                            <option value="5etools-homebrew">Homebrew autorizado</option>

                            <option value="custom">Personalizado</option>

                        </select>

                        <select x-model="catalogSource" @change="loadCatalog(1)" class="sb-input rounded-xl border px-3 py-2.5 text-[10px] font-bold">

                            <option value="">Todas as fontes</option>

                            <template x-for="source in catalogMeta.sources" :key="`${source.provider}:${source.code}`">

                                <option :value="source.code" x-text="source.code + (source.kind === 'homebrew' ? ' · HB' : '')"></option>

                            </template>

                        </select>

                    </div>

                    <div class="mt-2 flex justify-end">

                        <button type="button" @click="resetCatalogFilters()" class="sb-accent text-[7px] font-black uppercase tracking-[0.09em] hover:underline">Limpar filtros</button>

                    </div>

                </div>

            </header>

            {{-- =========================================================

                 CORPO

            ========================================================== --}}

            <div class="grid min-h-0 flex-1 lg:grid-cols-[390px_minmax(0,1fr)]">

                {{-- LISTA DE RESULTADOS --}}

                <section class="sb-paper-alt flex min-h-0 flex-col border-r sb-border-soft">

                    <div class="flex shrink-0 items-center justify-between border-b sb-border-soft px-3 py-2.5">

                        <span class="sb-label text-[7px] font-black uppercase tracking-[0.13em]">Resultados</span>

                        <span class="sb-muted text-[9px] font-bold" x-text="catalogPage.total"></span>

                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto p-2.5">

                        <div x-show="catalogLoading" class="sb-muted flex min-h-[200px] items-center justify-center text-[9px] font-black uppercase tracking-[0.13em]">Carregando...</div>

                        <div x-show="!catalogLoading" class="space-y-1.5">

                            <template x-for="spell in catalog" :key="spell.id">

                                <button

                                    type="button"

                                    @click="catalogSelected = spell"

                                    class="group flex w-full items-center gap-3 rounded-2xl border px-3 py-2.5 text-left transition"

                                    :style="Number(catalogSelected?.id) === Number(spell.id)

                                        ? `border-color: color-mix(in srgb, var(--spellbook-accent) 45%, transparent); background: var(--spellbook-accent-soft); box-shadow: var(--spellbook-shadow)`

                                        : `border-color: var(--spellbook-border-soft); background: color-mix(in srgb, var(--spellbook-paper-strong) 88%, var(--spellbook-panel) 12%)`"

                                >

                                    <div class="relative h-11 w-11 shrink-0">

                                        <span

                                            class="absolute inset-[4px] rotate-45 rounded-[11px] border"

                                            style="border-color: var(--spellbook-border-strong); background: var(--spellbook-panel);"

                                        ></span>

                                        <span

                                            class="absolute inset-[5px] flex items-center justify-center rounded-full border font-serif text-[13px] font-black"

                                            style="border-color: var(--spellbook-paper-strong); background: var(--spellbook-paper-strong); color: var(--spellbook-accent);"

                                            x-text="Number(spell.level) === 0 ? 'T' : `${spell.level}º`"

                                        ></span>

                                    </div>

                                    <div class="min-w-0 flex-1">

                                        <div class="flex min-w-0 flex-wrap items-center gap-1.5">

                                            <strong class="sb-title truncate font-serif text-[14px] font-black" x-text="spell.name"></strong>

                                            <span x-show="spell.in_spellbook" class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[6px] font-black uppercase text-emerald-900">No Grimório</span>

                                            <span x-show="spell.ritual" class="shrink-0 rounded-full bg-sky-100 px-2 py-0.5 text-[6px] font-black uppercase text-sky-900">Ritual</span>

                                        </div>

                                        <p class="sb-muted mt-0.5 truncate text-[10.5px] font-semibold" x-text="spell.subtitle_pt || spell.subtitle"></p>

                                        <p class="sb-label mt-0.5 truncate text-[8px] font-bold" x-text="spell.source_label"></p>

                                    </div>

                                    <svg class="sb-muted h-4 w-4 shrink-0 transition group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">

                                        <path d="m7 5 5 5-5 5"></path>

                                    </svg>

                                </button>

                            </template>

                            <div x-show="catalog.length === 0" class="sb-paper rounded-2xl border border-dashed sb-border px-4 py-10 text-center">

                                <p class="sb-muted text-[10px]">Nenhum feitiço encontrado com esses filtros.</p>

                            </div>

                        </div>

                    </div>

                    <footer class="sb-panel flex shrink-0 items-center justify-between gap-3 border-t sb-border-soft px-3 py-2.5">

                        <button type="button" @click="loadCatalog(catalogPage.page - 1)" :disabled="!catalogPage.has_previous || catalogLoading" class="sb-button-soft rounded-lg border px-3 py-2 text-[7px] font-black uppercase disabled:opacity-35">← Anterior</button>

                        <span class="sb-muted text-[9px] font-bold"><span x-text="catalogPage.page"></span> / <span x-text="catalogPage.last_page"></span></span>

                        <button type="button" @click="loadCatalog(catalogPage.page + 1)" :disabled="!catalogPage.has_next || catalogLoading" class="sb-button-soft rounded-lg border px-3 py-2 text-[7px] font-black uppercase disabled:opacity-35">Próxima →</button>

                    </footer>

                </section>

                {{-- =====================================================

                     LEITOR DO FEITIÇO — MESMO PADRÃO DO DETALHE

                ====================================================== --}}

                <section class="sb-paper min-h-0 overflow-y-auto">

                    <div x-show="!catalogSelected" class="flex min-h-full items-center justify-center p-8 text-center">

                        <div>

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl" style="background: var(--spellbook-accent-soft); color: var(--spellbook-accent);">✦</div>

                            <p class="sb-muted mt-3 text-[11px] font-bold">Escolha um feitiço na lista.</p>

                        </div>

                    </div>

                    <article x-show="catalogSelected" x-cloak class="mx-auto w-full max-w-[900px] px-7 py-7 sm:px-10 sm:py-9">

                        {{-- IDENTIDADE --}}

                        <div class="pb-5">

                            <div class="flex items-start justify-between gap-6">

                                <div class="min-w-0">

                                    <h4
                                        class="sb-title font-serif text-[31px] font-black leading-[1.04] sm:text-[34px]"
                                        x-text="catalogSelected?.name"
                                    ></h4>

                                    <p
                                        class="sb-muted mt-1 text-[13px] font-semibold italic sm:text-[14px]"
                                        x-text="catalogSelected?.subtitle_pt || catalogSelected?.subtitle"
                                    ></p>

                                </div>

                                <strong
                                    class="sb-label mt-1 shrink-0 text-[8px] font-black uppercase tracking-[0.08em]"
                                    x-text="catalogSelected?.source_label"
                                ></strong>

                            </div>

                            <dl class="mt-5 space-y-1 text-[13px] leading-[1.55] sm:text-[14px]">

                                <div>
                                    <dt class="sb-title inline font-black">Conjuração:</dt>
                                    <dd class="ml-1 inline" x-text="catalogSelected?.casting_time_display || catalogSelected?.casting_time || '—'"></dd>
                                </div>

                                <div>
                                    <dt class="sb-title inline font-black">Alcance:</dt>
                                    <dd class="ml-1 inline" x-text="catalogSelected?.range_display || catalogSelected?.range || '—'"></dd>
                                </div>

                                <div>
                                    <dt class="sb-title inline font-black">Componentes:</dt>
                                    <dd class="ml-1 inline" x-text="catalogSelected?.components || '—'"></dd>
                                </div>

                                <div>
                                    <dt class="sb-title inline font-black">Duração:</dt>
                                    <dd class="ml-1 inline">
                                        <span x-text="catalogSelected?.duration_display || catalogSelected?.duration || '—'"></span>
                                        <span x-show="catalogSelected?.concentration" class="font-semibold italic"> (Concentração)</span>
                                    </dd>
                                </div>

                                <div x-show="catalogSelected?.ritual">
                                    <dt class="sb-title inline font-black">Ritual:</dt>
                                    <dd class="ml-1 inline">Sim</dd>
                                </div>

                            </dl>

                        </div>

                        {{-- TEXTO PRINCIPAL --}}

                        <div class="border-t sb-border-soft pt-5">

                            <div class="spell-rich-copy text-[14px] leading-[1.9] sm:text-[15px]" x-html="catalogSelected?.description_html || ''"></div>

                        </div>

                        {{-- NÍVEIS SUPERIORES / TRUQUE --}}

                        <section

                            x-show="catalogSelected?.higher_level_html || (catalogSelected?.scaling_level_dice?.length ?? 0) > 0"

                            class="mt-5 border-t sb-border-soft pt-4"

                        >

                            <div x-show="catalogSelected?.higher_level_html" class="text-[13px] leading-[1.8]">

                                <strong class="sb-title font-serif text-[15px] font-black" x-text="(catalogSelected?.higher_level_label || 'Em Níveis Superiores') + ':'"></strong>

                                <span class="spell-rich-copy ml-1 italic" x-html="catalogSelected?.higher_level_html || ''"></span>

                            </div>

                            <div

                                x-show="Array.isArray(catalogSelected?.scaling_level_dice) && catalogSelected.scaling_level_dice.length > 0"

                                class="mt-3 space-y-2"

                            >

                                <template x-for="progression in catalogSelected?.scaling_level_dice || []" :key="progression.label">

                                    <div class="border-l-2 pl-3 py-1" style="border-color: color-mix(in srgb, var(--spellbook-accent) 38%, transparent);">

                                        <strong class="sb-accent text-[9px] font-black" x-text="progression.label"></strong>

                                        <p class="sb-muted mt-1 text-[10px] leading-5">

                                            <template x-for="(row, index) in progression.levels" :key="row.level">

                                                <span>

                                                    <span x-show="index > 0" class="mx-1.5">·</span>

                                                    <span x-text="`Nv. ${row.level} — ${row.value}`"></span>

                                                </span>

                                            </template>

                                        </p>

                                    </div>

                                </template>

                            </div>

                        </section>

                        {{-- LISTAS --}}

                        <p x-show="spellListLabels(catalogSelected).length > 0" class="sb-muted mt-5 border-t sb-border-soft pt-4 text-[10.5px] leading-5">

                            <strong class="sb-title mr-1 font-black">Listas:</strong>

                            <span x-text="spellListLabels(catalogSelected).join(', ')"></span>

                        </p>

                        {{-- ADICIONAR / REMOVER --}}

                        <section class="mt-5 border-t sb-border-soft pt-4">

                            <div x-show="!catalogSelected?.in_spellbook" class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">

                                <label class="block">

                                    <span class="sb-label block text-[7px] font-black uppercase tracking-[0.1em]">Adicionar para</span>

                                    <select x-model="catalogProfileId" class="sb-input mt-1 w-full rounded-xl border px-3 py-2.5 text-[10px] font-bold focus:ring-2">

                                        <option value="">Sem perfil / outra origem</option>

                                        <template x-for="profile in profiles" :key="profile.id"><option :value="String(profile.id)" x-text="`${profile.class} · Nv. ${profile.caster_level}`"></option></template>

                                    </select>

                                </label>

                                <button

                                    type="button"

                                    @click="addCatalogSelected()"

                                    :disabled="busy"

                                    class="sb-button-primary h-[42px] rounded-xl px-5 text-[8px] font-black uppercase tracking-[0.09em] shadow-md transition disabled:opacity-40"

                                    x-text="busy ? 'Adicionando...' : 'Adicionar ao Grimório'"

                                ></button>

                            </div>

                            <div x-show="catalogSelected?.in_spellbook" class="flex flex-col gap-2 border-l-2 pl-3 py-1 sm:flex-row sm:items-center sm:justify-between" style="border-color: color-mix(in srgb, var(--spellbook-accent) 35%, transparent);">

                                <div>

                                    <strong class="sb-title block text-[11px]">Este feitiço já está no seu Grimório.</strong>

                                    <p class="sb-muted mt-0.5 text-[9px]">Você pode removê-lo daqui sem fechar o catálogo.</p>

                                </div>

                                <button

                                    type="button"

                                    @click="removeCatalogSelected()"

                                    :disabled="busy"

                                    class="rounded-xl border border-red-900/25 bg-red-50 px-4 py-2.5 text-[8px] font-black uppercase tracking-[0.08em] text-red-800 transition hover:bg-red-100 disabled:opacity-40"

                                >

                                    Remover do Grimório

                                </button>

                            </div>

                        </section>

                    </article>

                </section>

            </div>

        </div>

    </div>

</template>
