@props(['character'])

@once
    @push('styles')
        <style>
            @keyframes partyWoundedPulseV3 {
                0%, 100% {
                    box-shadow: 0 0 0 2px rgba(185, 28, 28, .18);
                }

                50% {
                    box-shadow:
                        0 0 0 4px rgba(185, 28, 28, .58),
                        0 0 24px rgba(185, 28, 28, .28);
                }
            }

            @keyframes partyCriticalPulseV3 {
                0%, 100% {
                    box-shadow:
                        0 0 0 3px rgba(153, 27, 27, .34),
                        0 0 10px rgba(153, 27, 27, .18);
                }

                50% {
                    box-shadow:
                        0 0 0 5px rgba(220, 38, 38, .76),
                        0 0 30px rgba(220, 38, 38, .44);
                }
            }

            .party-v3-portrait--wounded {
                animation: partyWoundedPulseV3 1.75s ease-in-out infinite;
            }

            .party-v3-portrait--critical,
            .party-v3-portrait--down {
                animation: partyCriticalPulseV3 1.05s ease-in-out infinite;
            }

            .party-v3-portrait--down img {
                filter: grayscale(.72) saturate(.45);
            }

            @media (prefers-reduced-motion: reduce) {
                .party-v3-portrait--wounded,
                .party-v3-portrait--critical,
                .party-v3-portrait--down {
                    animation: none !important;
                }
            }
        </style>
    @endpush
@endonce

<div
    x-data="{
        bootstrapUrl: @js(route('characters.party.index', $character)),
        statesUrl: @js(route('characters.party.states', $character)),
        partyBaseUrl: @js(url('/characters/' . $character->id . '/party')),
        csrf: @js(csrf_token()),

        loaded: false,
        loading: false,

        campaigns: [],
        campaign: null,
        campaignId: null,

        members: [],
        memberMenuId: null,

        polling: false,
        pollTimer: null,

        pages: [
            {
                id: 'page-1',
                content: '',
            },
        ],
        diaryPageIndex: 0,
        diaryState: 'idle',
        diaryTimer: null,

        items: [],
        itemSearch: '',
        giveItemOpen: false,
        selectedMemberId: null,
        transferItemId: null,
        transferQuantity: 1,
        transferring: false,

        actionMessage: null,
        actionError: null,

        get selectedMember() {
            return this.members.find(
                member =>
                    Number(member.id)
                    === Number(this.selectedMemberId)
            ) ?? null;
        },

        get selectedItem() {
            return this.items.find(
                item =>
                    Number(item.id)
                    === Number(this.transferItemId)
            ) ?? null;
        },

        get filteredItems() {
            const term = String(this.itemSearch ?? '')
                .trim()
                .toLowerCase();

            if (!term) {
                return this.items;
            }

            return this.items.filter(
                item =>
                    String(item.name ?? '')
                        .toLowerCase()
                        .includes(term)
                    ||
                    String(item.type ?? '')
                        .toLowerCase()
                        .includes(term)
            );
        },

        get filteredMundaneItems() {
            return this.filteredItems.filter(
                item =>
                    String(item.type ?? '').toLowerCase()
                    === 'mundane'
            );
        },

        get filteredWonderfulItems() {
            return this.filteredItems.filter(
                item =>
                    String(item.type ?? '').toLowerCase()
                    === 'wonderful'
            );
        },

        get filteredTechnologicalItems() {
            return this.filteredItems.filter(
                item =>
                    String(item.type ?? '').toLowerCase()
                    === 'technological'
            );
        },

        async ensureLoaded() {
            if (!this.loaded && !this.loading) {
                await this.loadParty();
            }

            this.startPolling();
        },

        async loadParty(campaignId = null) {
            this.loading = true;
            this.actionError = null;

            try {
                const url = new URL(
                    this.bootstrapUrl,
                    window.location.origin
                );

                if (campaignId) {
                    url.searchParams.set(
                        'campaign_id',
                        campaignId
                    );
                }

                const response = await fetch(
                    url.toString(),
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível carregar a Party.'
                    );
                }

                this.campaigns = Array.isArray(data?.campaigns)
                    ? data.campaigns
                    : [];

                this.campaign = data?.campaign ?? null;
                this.campaignId = this.campaign?.id ?? null;

                this.members = Array.isArray(data?.members)
                    ? data.members
                    : [];

                this.pages = Array.isArray(data?.pages)
                    && data.pages.length > 0
                        ? data.pages
                        : [
                            {
                                id: 'page-1',
                                content: '',
                            },
                        ];

                this.diaryPageIndex = Math.min(
                    this.diaryPageIndex,
                    this.pages.length - 1
                );

                this.items = Array.isArray(data?.items)
                    ? data.items
                    : [];

                this.loaded = true;
            } catch (error) {
                console.error(
                    'Erro ao carregar Party:',
                    error
                );

                this.actionError = error?.message
                    ?? 'Não foi possível carregar a Party.';
            } finally {
                this.loading = false;
            }
        },

        async changeCampaign() {
            this.stopPolling();

            this.memberMenuId = null;
            this.giveItemOpen = false;
            this.selectedMemberId = null;
            this.transferItemId = null;
            this.transferQuantity = 1;
            this.diaryPageIndex = 0;

            await this.loadParty(
                this.campaignId
            );

            this.startPolling();
        },

        startPolling() {
            if (this.pollTimer || !this.campaignId) {
                return;
            }

            this.refreshStates();

            this.pollTimer = setInterval(
                () => {
                    this.refreshStates();
                },
                1500
            );
        },

        stopPolling() {
            if (!this.pollTimer) {
                return;
            }

            clearInterval(
                this.pollTimer
            );

            this.pollTimer = null;
        },

        async refreshStates() {
            if (
                this.polling
                || !this.campaignId
                || document.hidden
            ) {
                return;
            }

            this.polling = true;

            try {
                const url = new URL(
                    this.statesUrl,
                    window.location.origin
                );

                url.searchParams.set(
                    'campaign_id',
                    this.campaignId
                );

                const response = await fetch(
                    url.toString(),
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        cache: 'no-store',
                    }
                );

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                const states = data?.members ?? {};

                this.members = this.members.map(
                    member => {
                        const state = states?.[
                            String(member.id)
                        ];

                        if (!state) {
                            return member;
                        }

                        return {
                            ...member,
                            ...state,
                        };
                    }
                );
            } finally {
                this.polling = false;
            }
        },

        toggleMemberMenu(memberId) {
            this.memberMenuId =
                Number(this.memberMenuId)
                === Number(memberId)
                    ? null
                    : memberId;
        },

        openGiveItem(member) {
            this.memberMenuId = null;
            this.selectedMemberId = member.id;
            this.giveItemOpen = true;
            this.transferItemId = null;
            this.transferQuantity = 1;
            this.itemSearch = '';
            this.actionMessage = null;
            this.actionError = null;
        },

        closeGiveItem() {
            this.giveItemOpen = false;
            this.selectedMemberId = null;
            this.transferItemId = null;
            this.transferQuantity = 1;
            this.itemSearch = '';
        },

        async pokeMember(member, emoji) {
            if (!this.campaignId || !member) {
                return;
            }

            this.memberMenuId = null;
            this.actionMessage = null;
            this.actionError = null;

            try {
                const response = await fetch(
                    `${this.partyBaseUrl}/${this.campaignId}/poke`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            recipient_character_id: member.id,
                            emoji: emoji,
                        }),
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível cutucar.'
                    );
                }

                this.actionMessage = data?.message
                    ?? `${member.name} foi cutucado.`;

                window.setTimeout(
                    () => {
                        this.actionMessage = null;
                    },
                    2200
                );
            } catch (error) {
                this.actionError = error?.message
                    ?? 'Não foi possível cutucar.';
            }
        },

        chooseItem(item) {
            this.transferItemId = item.id;
            this.transferQuantity = 1;
            this.actionMessage = null;
            this.actionError = null;
        },

        normalizeTransferQuantity() {
            const item = this.selectedItem;

            if (!item) {
                this.transferQuantity = 1;
                return;
            }

            const maximum = Math.max(
                1,
                Number(item.quantity) || 1
            );

            this.transferQuantity = Math.max(
                1,
                Math.min(
                    maximum,
                    Number(this.transferQuantity) || 1
                )
            );
        },

        async transferItem() {
            if (
                this.transferring
                || !this.campaignId
                || !this.selectedMember
                || !this.selectedItem
            ) {
                return;
            }

            this.normalizeTransferQuantity();

            const item = this.selectedItem;
            const member = this.selectedMember;

            const confirmed = window.confirm(
                `Entregar ${this.transferQuantity}× ${item.name} para ${member.name}?`
            );

            if (!confirmed) {
                return;
            }

            this.transferring = true;
            this.actionMessage = null;
            this.actionError = null;

            try {
                const response = await fetch(
                    `${this.partyBaseUrl}/${this.campaignId}/transfer-item`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            recipient_character_id: member.id,
                            item_id: item.id,
                            quantity: this.transferQuantity,
                        }),
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? Object
                            .values(data?.errors ?? {})
                            .flat()
                            .join(' ')
                        ?? 'Não foi possível entregar o item.'
                    );
                }

                this.items = Array.isArray(data?.items)
                    ? data.items
                    : [];

                this.actionMessage = data?.message
                    ?? 'Item entregue.';

                this.closeGiveItem();
            } catch (error) {
                this.actionError = error?.message
                    ?? 'Não foi possível entregar o item.';
            } finally {
                this.transferring = false;
            }
        },

        newPageId() {
            if (
                window.crypto
                && typeof window.crypto.randomUUID === 'function'
            ) {
                return window.crypto.randomUUID();
            }

            return `page-${Date.now()}-${Math.random().toString(16).slice(2)}`;
        },

        addDiaryPage() {
            if (this.pages.length >= 50) {
                return;
            }

            this.pages.push({
                id: this.newPageId(),
                content: '',
            });

            this.diaryPageIndex =
                this.pages.length - 1;

            this.queueDiarySave();
        },

        deleteDiaryPage() {
            if (this.pages.length <= 1) {
                return;
            }

            if (!window.confirm('Excluir esta página do Diário?')) {
                return;
            }

            this.pages.splice(
                this.diaryPageIndex,
                1
            );

            this.diaryPageIndex = Math.max(
                0,
                Math.min(
                    this.diaryPageIndex,
                    this.pages.length - 1
                )
            );

            this.queueDiarySave();
        },

        queueDiarySave() {
            this.diaryState = 'typing';

            if (this.diaryTimer) {
                clearTimeout(
                    this.diaryTimer
                );
            }

            this.diaryTimer = setTimeout(
                () => {
                    this.saveDiary();
                },
                700
            );
        },

        async saveDiary() {
            if (!this.campaignId) {
                return;
            }

            this.diaryState = 'saving';

            try {
                const response = await fetch(
                    `${this.partyBaseUrl}/${this.campaignId}/notes`,
                    {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            pages: this.pages,
                        }),
                    }
                );

                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(
                        data?.message
                        ?? 'Não foi possível salvar o Diário.'
                    );
                }

                if (
                    Array.isArray(data?.pages)
                    && data.pages.length > 0
                ) {
                    this.pages = data.pages;
                }

                this.diaryState = 'saved';

                window.setTimeout(
                    () => {
                        if (this.diaryState === 'saved') {
                            this.diaryState = 'idle';
                        }
                    },
                    1600
                );
            } catch (error) {
                this.diaryState = 'error';
            }
        },

        portraitClass(member) {
            return {
                wounded: 'party-v3-portrait--wounded',
                critical: 'party-v3-portrait--critical',
                down: 'party-v3-portrait--down',
            }[member?.health_state] ?? '';
        },

        stateLabel(member) {
            return {
                healthy: 'Estável',
                wounded: 'Ferido',
                critical: 'Crítico',
                down: '0 PV',
                unknown: 'Sem dados',
            }[member?.health_state] ?? '—';
        },

        stateClass(member) {
            return {
                healthy: 'text-emerald-700',
                wounded: 'text-red-700',
                critical: 'text-red-800',
                down: 'text-red-900',
                unknown: 'text-stone-500',
            }[member?.health_state] ?? 'text-stone-500';
        },
    }"

    x-effect="
        if (drawer === 'party') {
            ensureLoaded()
        } else {
            stopPolling()
        }
    "

    class="flex h-full min-h-0 flex-col"
>
    <div class="min-h-0 flex-1 overflow-y-auto px-6 py-7 sm:px-8">
        <div
            x-show="loading && !loaded"
            class="py-16 text-center text-[10px] font-black uppercase tracking-[0.20em] text-[#8c6239]"
        >
            Reunindo a Party...
        </div>

        <template x-if="loaded && campaigns.length === 0">
            <div class="py-16 text-center">
                <h3 class="font-serif text-xl font-black text-[#53150f]">
                    Nenhuma Party
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-[11px] leading-relaxed text-[#8c6239]">
                    Esta ficha ainda não está compartilhada em uma campanha.
                </p>
            </div>
        </template>

        <template x-if="loaded && campaigns.length > 0">
            <div class="space-y-10">
                <section>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#8c6239]">
                                Campanha —
                                <span
                                    class="text-[#53150f]"
                                    x-text="campaign?.name ?? 'Campanha'"
                                ></span>
                            </p>

                            <h2 class="mt-2 font-serif text-[30px] font-black leading-none text-[#53150f]">
                                Party
                            </h2>
                        </div>

                        <select
                            x-show="campaigns.length > 1"
                            x-model.number="campaignId"
                            x-on:change="changeCampaign()"
                            class="rounded-lg border border-[#cdbb9f] bg-[#fffdf8] px-3 py-2 text-xs font-bold text-[#53150f] focus:border-[#6b1d14] focus:ring-1 focus:ring-[#6b1d14]/15"
                        >
                            <template x-for="option in campaigns" :key="option.id">
                                <option :value="option.id" x-text="option.name"></option>
                            </template>
                        </select>
                    </div>

                    <div
                        x-show="members.length === 0"
                        class="mt-8 rounded-xl border border-dashed border-[#cdbb9f] px-5 py-10 text-center text-[10px] text-[#8c6239]"
                    >
                        Ainda não há outros personagens compartilhados nesta Party.
                    </div>

                    <div
                        x-show="members.length > 0"
                        class="mt-8 flex flex-wrap gap-x-10 gap-y-12"
                    >
                        <template x-for="member in members" :key="member.id">
                            <article class="relative w-[200px] text-center">
                                <div
                                    class="absolute right-0 top-0 z-20"
                                    x-on:click.outside="
                                        if (Number(memberMenuId) === Number(member.id)) {
                                            memberMenuId = null
                                        }
                                    "
                                >
                                    <button
                                        type="button"
                                        x-on:click="toggleMemberMenu(member.id)"
                                        class="flex h-9 w-9 items-center justify-center rounded-full text-[#8c6239] transition hover:bg-[#eadbc8]/55 hover:text-[#53150f]"
                                        title="Opções"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="5" cy="12" r="1.6" />
                                            <circle cx="12" cy="12" r="1.6" />
                                            <circle cx="19" cy="12" r="1.6" />
                                        </svg>
                                    </button>

                                    <div
                                        x-show="Number(memberMenuId) === Number(member.id)"
                                        x-cloak
                                        x-transition
                                        class="absolute right-0 top-10 w-44 overflow-hidden rounded-xl border border-[#cdbb9f] bg-[#fffdf8] py-1 text-left shadow-xl"
                                    >
                                        <button
                                            type="button"
                                            x-on:click="openGiveItem(member)"
                                            class="w-full px-3 py-2.5 text-left text-[9px] font-black uppercase tracking-wider text-[#53150f] transition hover:bg-[#f3eadf]"
                                        >
                                            Entregar Item
                                        </button>

                                        <div
                                            class="
                                                border-t
                                                border-[#cdbb9f]/55
                                                px-3
                                                pb-3
                                                pt-2.5
                                            "
                                        >
                                            <p
                                                class="
                                                    mb-2
                                                    text-[8px]
                                                    font-black
                                                    uppercase
                                                    tracking-[0.14em]
                                                    text-[#8c6239]
                                                "
                                            >
                                                Cutucar
                                            </p>

                                            <div
                                                class="
                                                    grid
                                                    grid-cols-6
                                                    gap-1
                                                "
                                            >
                                                <template
                                                    x-for="
                                                        poke
                                                        in
                                                        [
                                                            { emoji: '👉', label: 'Ei!' },
                                                            { emoji: '👀', label: 'Olho' },
                                                            { emoji: '💩', label: 'Merda' },
                                                            { emoji: '💀', label: 'Caveira' },
                                                            { emoji: '❤️', label: 'Coração' },
                                                            { emoji: '🔥', label: 'Fogo' },
                                                        ]
                                                    "
                                                    :key="
                                                        poke.emoji
                                                    "
                                                >
                                                    <button
                                                        type="button"
                                                        x-on:click="
                                                            pokeMember(
                                                                member,
                                                                poke.emoji
                                                            )
                                                        "
                                                        class="
                                                            flex
                                                            h-8
                                                            w-8
                                                            items-center
                                                            justify-center
                                                            rounded-lg
                                                            text-lg
                                                            transition
                                                            hover:bg-[#eadbc8]
                                                            hover:scale-110
                                                        "
                                                        :title="
                                                            poke.label
                                                        "
                                                        x-text="
                                                            poke.emoji
                                                        "
                                                    ></button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="relative mx-auto h-32 w-32 rounded-full border-[3px] border-[#cdbb9f] bg-[#eadbc8] p-1"
                                    :class="portraitClass(member)"
                                >
                                    <div class="h-full w-full overflow-hidden rounded-full bg-[#f4f1e8]">
                                        <template x-if="member.image_url">
                                            <img
                                                :src="member.image_url"
                                                x-on:error="member.image_url = null"
                                                :alt="member.name"
                                                class="h-full w-full object-cover object-top"
                                            >
                                        </template>

                                        <template x-if="!member.image_url">
                                            <div
                                                class="flex h-full w-full items-center justify-center font-serif text-4xl font-black text-[#53150f]"
                                                x-text="String(member.name ?? '?').slice(0, 1).toUpperCase()"
                                            ></div>
                                        </template>
                                    </div>

                                    <span
                                        x-show="member.exhaustion_enabled && Number(member.exhaustion) > 0"
                                        x-cloak
                                        class="absolute -right-1 top-2 rounded-full border border-[#b88920] bg-[#f6e4a8] px-2 py-1 text-[8px] font-black text-[#6b4a12] shadow-sm"
                                        x-text="`EX ${member.exhaustion}`"
                                    ></span>
                                </div>

                                <h3
                                    class="mt-4 truncate font-serif text-[15px] font-black text-[#53150f]"
                                    x-text="member.name"
                                ></h3>

                                <div class="mx-auto mt-3 h-2 w-[160px] overflow-hidden rounded-full bg-[#ded6c9]">
                                    <div
                                        class="h-full rounded-full bg-emerald-600 transition-all duration-500"
                                        :style="`width: ${member.hp_percent ?? 0}%`"
                                    ></div>
                                </div>

                                <p
                                    class="mt-2 text-[8px] font-black uppercase tracking-[0.14em]"
                                    :class="stateClass(member)"
                                    x-text="stateLabel(member)"
                                ></p>
                            </article>
                        </template>
                    </div>
                </section>

                <div
                    x-show="actionMessage || actionError"
                    x-cloak
                    class="rounded-xl border px-4 py-3 text-[10px] font-bold"
                    :class="
                        actionError
                            ? 'border-red-200 bg-red-50 text-red-700'
                            : 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    "
                    x-text="actionError ?? actionMessage"
                ></div>

                <section class="border-t border-[#cdbb9f]/65 pt-7">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="font-serif text-2xl font-black text-[#53150f]">
                            Diário
                        </h2>

                        <span
                            class="text-[8px] font-black uppercase tracking-wider"
                            :class="{
                                'text-[#8c6239]': diaryState === 'idle' || diaryState === 'typing',
                                'text-amber-700': diaryState === 'saving',
                                'text-emerald-700': diaryState === 'saved',
                                'text-red-700': diaryState === 'error',
                            }"
                            x-text="{
                                idle: '',
                                typing: 'Alterado',
                                saving: 'Salvando...',
                                saved: 'Salvo',
                                error: 'Erro ao salvar',
                            }[diaryState]"
                        ></span>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <template x-for="(page, index) in pages" :key="page.id">
                            <button
                                type="button"
                                x-on:click="diaryPageIndex = index"
                                class="flex h-8 min-w-8 items-center justify-center rounded-lg border px-2.5 text-[9px] font-black transition"
                                :class="
                                    diaryPageIndex === index
                                        ? 'border-[#6b1d14] bg-[#6b1d14] text-[#fffaf2]'
                                        : 'border-[#cdbb9f] bg-[#fffdf8] text-[#8c6239] hover:border-[#8c6239]'
                                "
                                x-text="index + 1"
                            ></button>
                        </template>

                        <button
                            type="button"
                            x-on:click="addDiaryPage()"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-dashed border-[#b99d79] text-base font-bold text-[#8c6239] transition hover:border-[#6b1d14] hover:text-[#53150f]"
                            title="Adicionar página"
                        >
                            +
                        </button>

                        <button
                            x-show="pages.length > 1"
                            x-cloak
                            type="button"
                            x-on:click="deleteDiaryPage()"
                            class="ml-auto text-[8px] font-black uppercase tracking-wider text-[#9b6f5c] transition hover:text-red-700"
                        >
                            Excluir página
                        </button>
                    </div>

                    <div class="mt-3 rounded-2xl border border-[#cdbb9f]/65 bg-[#fffdf8] p-1">
                        <textarea
                            x-model="pages[diaryPageIndex].content"
                            x-on:input="queueDiarySave()"
                            rows="12"
                            maxlength="20000"
                            placeholder="Escreva..."
                            class="min-h-[300px] w-full resize-y border-0 bg-transparent px-4 py-4 font-serif text-[15px] leading-7 text-[#432c21] placeholder:text-[#8c6239]/35 focus:ring-0"
                        ></textarea>
                    </div>
                </section>
            </div>
        </template>
    </div>

    {{-- ================================================================
         ENTREGAR ITEM — MODAL
         Abre sobre a Party e fecha clicando fora.
    ================================================================= --}}
    <div
        x-show="giveItemOpen && selectedMember"
        x-cloak
        x-transition.opacity
        class="
            fixed
            inset-0
            z-[120]
            flex
            items-center
            justify-center
            p-4
            sm:p-6
        "
    >
        <div
            class="
                absolute
                inset-0
                bg-black/90
                backdrop-blur-[2px]
            "
            style="
                background-color:
                    rgba(8, 6, 5, 0.88);
            "
            x-on:click="
                closeGiveItem()
            "
        ></div>

        <section
            x-on:click.stop
            class="
                relative
                z-10
                flex
                max-h-[78vh]
                w-full
                max-w-lg
                flex-col
                overflow-hidden
                rounded-2xl
                border
                border-[#cdbb9f]
                bg-[#f7f0e6]
                shadow-2xl
            "
        >
            <header
                class="
                    border-b
                    border-[#cdbb9f]/65
                    bg-[#eadbc8]
                    px-5
                    py-4
                "
            >
                <p
                    class="
                        text-[8px]
                        font-black
                        uppercase
                        tracking-[0.18em]
                        text-[#8c6239]
                    "
                >
                    Entregar item
                </p>

                <h3
                    class="
                        mt-1
                        truncate
                        font-serif
                        text-xl
                        font-black
                        text-[#53150f]
                    "
                    x-text="
                        selectedMember?.name
                        ?? ''
                    "
                ></h3>
            </header>

            <div
                class="
                    min-h-0
                    flex-1
                    overflow-y-auto
                    p-5
                "
            >
                <input
                    type="search"
                    x-model.debounce.200ms="
                        itemSearch
                    "
                    placeholder="Buscar item..."
                    class="
                        w-full
                        rounded-xl
                        border
                        border-[#cdbb9f]
                        bg-[#fffdf8]
                        px-3
                        py-2.5
                        text-xs
                        text-[#432c21]
                        placeholder:text-[#8c6239]/55
                        focus:border-[#6b1d14]
                        focus:ring-1
                        focus:ring-[#6b1d14]/15
                    "
                >

                <div
                    x-show="
                        filteredItems.length
                        ===
                        0
                    "
                    class="
                        mt-4
                        rounded-xl
                        border
                        border-dashed
                        border-[#cdbb9f]
                        px-4
                        py-8
                        text-center
                        text-[10px]
                        text-[#8c6239]
                    "
                >
                    Nenhum item disponível.
                </div>

                <div
                    x-show="
                        filteredMundaneItems.length
                        >
                        0
                    "
                    class="
                        mt-5
                    "
                >
                    <div
                        class="
                            mb-2
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <h4
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#53150f]
                            "
                        >
                            Itens Mundanos
                        </h4>

                        <span
                            class="
                                h-px
                                flex-1
                                bg-[#cdbb9f]/65
                            "
                        ></span>

                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#8c6239]
                            "
                            x-text="
                                filteredMundaneItems.length
                            "
                        ></span>
                    </div>

                    <div
                        class="
                            space-y-2
                        "
                    >
                        <template
                            x-for="
                                item
                                in
                                filteredMundaneItems
                            "
                            :key="
                                item.id
                            "
                        >

                            <button
                                type="button"
                                x-on:click="
                                    chooseItem(
                                        item
                                    )
                                "
                                class="
                                    flex
                                    w-full
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    bg-[#fffdf8]
                                    p-3
                                    text-left
                                    transition
                                    hover:border-[#8c6239]
                                "
                                :class="
                                    Number(
                                        transferItemId
                                    )
                                    ===
                                    Number(
                                        item.id
                                    )
                                        ? 'border-[#6b1d14] ring-2 ring-[#6b1d14]/10'
                                        : 'border-[#cdbb9f]/55'
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-11
                                        w-11
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]/45
                                    "
                                >
                                    <template
                                        x-if="
                                            item.image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                item.image_url
                                            "
                                            :alt="
                                                item.name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !item.image_url
                                        "
                                    >
                                        <svg
                                            class="
                                                h-5
                                                w-5
                                                text-[#8c6239]
                                            "
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M5 8.5h14v10H5v-10Z"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />

                                            <path
                                                d="M8 8.5V6.8A2.8 2.8 0 0 1 10.8 4h2.4A2.8 2.8 0 0 1 16 6.8v1.7"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                </div>

                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            text-[11px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            item.name
                                        "
                                    ></p>

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            `×${item.quantity}${item.rarity_label ? ` · ${item.rarity_label}` : ''}`
                                        "
                                    ></p>
                                </div>

                                <div
                                    x-show="
                                        Number(
                                            transferItemId
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        h-6
                                        w-6
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#6b1d14]
                                        text-[#fffaf2]
                                    "
                                >
                                    ✓
                                </div>
                            </button>

                        </template>
                    </div>
                </div>


                <div
                    x-show="
                        filteredWonderfulItems.length
                        >
                        0
                    "
                    class="
                        mt-6
                    "
                >
                    <div
                        class="
                            mb-2
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <h4
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#53150f]
                            "
                        >
                            Itens Maravilhosos
                        </h4>

                        <span
                            class="
                                h-px
                                flex-1
                                bg-[#cdbb9f]/65
                            "
                        ></span>

                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#8c6239]
                            "
                            x-text="
                                filteredWonderfulItems.length
                            "
                        ></span>
                    </div>

                    <div
                        class="
                            space-y-2
                        "
                    >
                        <template
                            x-for="
                                item
                                in
                                filteredWonderfulItems
                            "
                            :key="
                                item.id
                            "
                        >

                            <button
                                type="button"
                                x-on:click="
                                    chooseItem(
                                        item
                                    )
                                "
                                class="
                                    flex
                                    w-full
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    bg-[#fffdf8]
                                    p-3
                                    text-left
                                    transition
                                    hover:border-[#8c6239]
                                "
                                :class="
                                    Number(
                                        transferItemId
                                    )
                                    ===
                                    Number(
                                        item.id
                                    )
                                        ? 'border-[#6b1d14] ring-2 ring-[#6b1d14]/10'
                                        : 'border-[#cdbb9f]/55'
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-11
                                        w-11
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]/45
                                    "
                                >
                                    <template
                                        x-if="
                                            item.image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                item.image_url
                                            "
                                            :alt="
                                                item.name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !item.image_url
                                        "
                                    >
                                        <svg
                                            class="
                                                h-5
                                                w-5
                                                text-[#8c6239]
                                            "
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M5 8.5h14v10H5v-10Z"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />

                                            <path
                                                d="M8 8.5V6.8A2.8 2.8 0 0 1 10.8 4h2.4A2.8 2.8 0 0 1 16 6.8v1.7"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                </div>

                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            text-[11px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            item.name
                                        "
                                    ></p>

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            `×${item.quantity}${item.rarity_label ? ` · ${item.rarity_label}` : ''}`
                                        "
                                    ></p>
                                </div>

                                <div
                                    x-show="
                                        Number(
                                            transferItemId
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        h-6
                                        w-6
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#6b1d14]
                                        text-[#fffaf2]
                                    "
                                >
                                    ✓
                                </div>
                            </button>

                        </template>
                    </div>
                </div>


                <div
                    x-show="
                        filteredTechnologicalItems.length
                        >
                        0
                    "
                    class="
                        mt-6
                    "
                >
                    <div
                        class="
                            mb-2
                            flex
                            items-center
                            gap-2
                        "
                    >
                        <h4
                            class="
                                text-[9px]
                                font-black
                                uppercase
                                tracking-[0.16em]
                                text-[#53150f]
                            "
                        >
                            Itens Tecnológicos
                        </h4>

                        <span
                            class="
                                h-px
                                flex-1
                                bg-[#cdbb9f]/65
                            "
                        ></span>

                        <span
                            class="
                                text-[8px]
                                font-bold
                                text-[#8c6239]
                            "
                            x-text="
                                filteredTechnologicalItems.length
                            "
                        ></span>
                    </div>

                    <div
                        class="
                            space-y-2
                        "
                    >
                        <template
                            x-for="
                                item
                                in
                                filteredTechnologicalItems
                            "
                            :key="
                                item.id
                            "
                        >

                            <button
                                type="button"
                                x-on:click="
                                    chooseItem(
                                        item
                                    )
                                "
                                class="
                                    flex
                                    w-full
                                    items-center
                                    gap-3
                                    rounded-xl
                                    border
                                    bg-[#fffdf8]
                                    p-3
                                    text-left
                                    transition
                                    hover:border-[#8c6239]
                                "
                                :class="
                                    Number(
                                        transferItemId
                                    )
                                    ===
                                    Number(
                                        item.id
                                    )
                                        ? 'border-[#6b1d14] ring-2 ring-[#6b1d14]/10'
                                        : 'border-[#cdbb9f]/55'
                                "
                            >
                                <div
                                    class="
                                        flex
                                        h-11
                                        w-11
                                        shrink-0
                                        items-center
                                        justify-center
                                        overflow-hidden
                                        rounded-lg
                                        border
                                        border-[#cdbb9f]/55
                                        bg-[#eadbc8]/45
                                    "
                                >
                                    <template
                                        x-if="
                                            item.image_url
                                        "
                                    >
                                        <img
                                            :src="
                                                item.image_url
                                            "
                                            :alt="
                                                item.name
                                            "
                                            class="
                                                h-full
                                                w-full
                                                object-cover
                                            "
                                        >
                                    </template>

                                    <template
                                        x-if="
                                            !item.image_url
                                        "
                                    >
                                        <svg
                                            class="
                                                h-5
                                                w-5
                                                text-[#8c6239]
                                            "
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                d="M5 8.5h14v10H5v-10Z"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />

                                            <path
                                                d="M8 8.5V6.8A2.8 2.8 0 0 1 10.8 4h2.4A2.8 2.8 0 0 1 16 6.8v1.7"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </template>
                                </div>

                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >
                                    <p
                                        class="
                                            truncate
                                            text-[11px]
                                            font-black
                                            text-[#53150f]
                                        "
                                        x-text="
                                            item.name
                                        "
                                    ></p>

                                    <p
                                        class="
                                            mt-1
                                            text-[8px]
                                            font-black
                                            uppercase
                                            tracking-wider
                                            text-[#8c6239]
                                        "
                                        x-text="
                                            `×${item.quantity}${item.rarity_label ? ` · ${item.rarity_label}` : ''}`
                                        "
                                    ></p>
                                </div>

                                <div
                                    x-show="
                                        Number(
                                            transferItemId
                                        )
                                        ===
                                        Number(
                                            item.id
                                        )
                                    "
                                    x-cloak
                                    class="
                                        flex
                                        h-6
                                        w-6
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-[#6b1d14]
                                        text-[#fffaf2]
                                    "
                                >
                                    ✓
                                </div>
                            </button>

                        </template>
                    </div>
                </div>

            </div>

            <footer
                class="
                    border-t
                    border-[#cdbb9f]/65
                    bg-[#fffdf8]
                    px-5
                    py-4
                "
            >
                <div
                    x-show="
                        selectedItem
                    "
                    x-cloak
                    class="
                        flex
                        items-end
                        gap-3
                    "
                >
                    <label
                        class="
                            shrink-0
                        "
                    >
                        <span
                            class="
                                mb-1
                                block
                                text-[8px]
                                font-black
                                uppercase
                                tracking-wider
                                text-[#8c6239]
                            "
                        >
                            Qtd.
                        </span>

                        <input
                            type="number"
                            min="1"
                            :max="
                                selectedItem?.quantity
                                ?? 1
                            "
                            x-model.number="
                                transferQuantity
                            "
                            x-on:change="
                                normalizeTransferQuantity()
                            "
                            class="
                                w-20
                                rounded-lg
                                border
                                border-[#cdbb9f]
                                bg-[#fffdf8]
                                px-3
                                py-2.5
                                text-center
                                text-sm
                                font-black
                                text-[#53150f]
                                focus:border-[#6b1d14]
                                focus:ring-1
                                focus:ring-[#6b1d14]/15
                            "
                        >
                    </label>

                    <button
                        type="button"
                        x-on:click="
                            transferItem()
                        "
                        :disabled="
                            transferring
                        "
                        class="
                            flex-1
                            rounded-lg
                            bg-[#6b1d14]
                            px-5
                            py-2.5
                            text-[9px]
                            font-black
                            uppercase
                            tracking-widest
                            text-[#fffaf2]
                            transition
                            hover:bg-[#53150f]
                            disabled:opacity-50
                        "
                    >
                        <span
                            x-show="
                                !transferring
                            "
                        >
                            Entregar
                        </span>

                        <span
                            x-show="
                                transferring
                            "
                            x-cloak
                        >
                            Entregando...
                        </span>
                    </button>
                </div>

                <p
                    x-show="
                        !selectedItem
                    "
                    class="
                        text-center
                        text-[9px]
                        text-[#8c6239]
                    "
                >
                    Selecione um item.
                </p>
            </footer>
        </section>
    </div>
</div>