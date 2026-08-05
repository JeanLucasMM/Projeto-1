<span
    x-data="{
        current: {{ $tracker->current }},
        max: {{ $tracker->max }},
        npcId: '{{ $combatNpc->id }}',
        featureTitle: '{{ addslashes($tracker->resourceKey) }}',

        async saveData() {
            try {
                await fetch('/combat/npc/update-resource', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        combat_npc_id: this.npcId,
                        feature_title: this.featureTitle,
                        current_uses: this.current
                    }),
                    keepalive: true
                });
            } catch (e) {
                console.error(e);
            }
        }
    }"

    x-init="
        $watch('current', () => saveData())
    "

    @visibilitychange.window="
        if(document.visibilityState === 'hidden'){
            saveData();
        }
    "

    @beforeunload.window="saveData()"

    class="inline-flex
           items-center
           ml-1
           px-1
           py-[1px]
           rounded-sm
           bg-[#5a1810]
           text-[#f4f1e8]
           border
           border-black/20
           font-sans
           text-[9px]
           leading-none
           font-bold
           whitespace-nowrap
           select-none"
>

    <button
        type="button"
        class="px-0.5 hover:text-white/70 transition focus:outline-none cursor-pointer"
        @click="if(current > 0) current--"
    >
        −
    </button>

    <span
        class="min-w-[20px] text-center mx-0.5"
        x-text="current + '/' + max"
    ></span>

    <button
        type="button"
        class="px-0.5 hover:text-white/70 transition focus:outline-none cursor-pointer"
        @click="if(current < max) current++"
    >
        +
    </button>

</span>