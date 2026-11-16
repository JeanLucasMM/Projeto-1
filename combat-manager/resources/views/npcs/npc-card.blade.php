@php
    $npcImageZoom = max(1, min(3, (float) ($npc->image_zoom ?? 1)));
    $npcImageHorizontal = max(0, min(100, (int) ($npc->image_horizontal ?? 50)));
    $npcImagePosition = in_array(($npc->image_position ?? 'auto'), ['auto','top','center','bottom'], true) ? $npc->image_position : 'auto';
    $isDeceased = !is_null($npc->deceased_at);
    $deceasedDate = $npc->deceased_at
        ? \Carbon\Carbon::parse($npc->deceased_at)->format('d/m/Y')
        : '';

    // Tradução apenas visual: preserva os termos originais do JSON importado.
    $npcSizeLabels = [
        'tiny' => 'Minúsculo', 'small' => 'Pequeno', 'medium' => 'Médio',
        'large' => 'Grande', 'huge' => 'Enorme', 'gargantuan' => 'Colossal',
    ];
    $npcTypeLabels = [
        'aberration' => 'Aberração', 'beast' => 'Fera', 'celestial' => 'Celestial',
        'construct' => 'Constructo', 'dragon' => 'Dragão', 'elemental' => 'Elemental',
        'fey' => 'Feérico', 'fiend' => 'Ínfero', 'giant' => 'Gigante',
        'humanoid' => 'Humanoide', 'monstrosity' => 'Monstruosidade',
        'ooze' => 'Limo', 'plant' => 'Planta', 'undead' => 'Morto-vivo',
        'demon' => 'Demônio', 'devil' => 'Diabo', 'human' => 'Humano',
        'elf' => 'Elfo', 'dwarf' => 'Anão', 'orc' => 'Orc',
        'goblinoid' => 'Goblinóide', 'shapechanger' => 'Metamorfo',
        'titan' => 'Titã', 'any race' => 'Qualquer raça',
    ];
    $rawSize = trim((string) ($npc->size ?? ''));
    $rawType = trim((string) ($npc->creature_type ?? ''));
    $translatedSize = $npcSizeLabels[strtolower($rawSize)] ?? $rawSize;
    $translatedType = preg_replace_callback(
        '/[\p{L}][\p{L}\s-]*/u',
        static function (array $match) use ($npcTypeLabels): string {
            $original = $match[0];
            $trimmed = trim($original);
            $key = strtolower($trimmed);
            return $key !== '' && isset($npcTypeLabels[$key])
                ? str_replace($trimmed, $npcTypeLabels[$key], $original)
                : $original;
        },
        $rawType
    ) ?? $rawType;
@endphp

<div
    id="npc-card-{{ $npc->id }}"
    class="npc-card npc-vault-card group relative flex w-full"
    draggable="true"
    data-npc="{{ $npc->id }}"
    data-npc-name="{{ $npc->name }}"
    data-current-folder="{{ $npc->folder_id ?? '' }}"
>
    <div class="npc-card-top-ornament" aria-hidden="true"></div>
    {{-- ============================================================
         OVERLAY "FALECIDO"
         ============================================================ --}}
    <div
        id="falecido-overlay-{{ $npc->id }}"
        class="npc-deceased-overlay absolute inset-0 z-10 flex flex-col items-center justify-center bg-gradient-to-t from-black/95 via-black/80 to-black/60 backdrop-grayscale-[0.3] backdrop-blur-[2px] transition-all duration-700 {{ $isDeceased ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}"
    >
        <svg
            id="tomb-icon-{{ $npc->id }}"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="w-12 h-12 text-gray-400 drop-shadow-[0_0_5px_rgba(0,0,0,0.8)] transform transition-transform duration-700 ease-out {{ $isDeceased ? 'translate-y-0' : 'translate-y-6' }}"
        >
            <path d="M7 22V10a5 5 0 0 1 10 0v12"/>
            <path d="M5 22h14"/>
            <path d="M12 7v5"/>
            <path d="M10 9h4"/>
        </svg>

        <span
            id="tomb-text-{{ $npc->id }}"
            class="mt-3 text-lg font-serif font-black text-red-600 uppercase tracking-[0.25em] drop-shadow-[0_0_8px_rgba(220,38,38,0.5)] transform transition-all duration-700 delay-150 {{ $isDeceased ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0' }}"
        >
            Falecido
        </span>

        <div
            id="tomb-details-{{ $npc->id }}"
            class="text-center transform transition-all duration-700 delay-200 {{ $isDeceased ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0' }}"
        >
            <a
                href="{{ route('npcs.show', $npc->id) }}"
                class="block text-sm font-serif font-bold text-gray-200 mt-2 line-clamp-1 px-4 hover:text-white hover:underline hover:scale-105 transition-all cursor-pointer drop-shadow-md"
                title="Abrir ficha de {{ $npc->name }}"
            >
                {{ $npc->name }}
            </a>

            <p
                id="tomb-date-{{ $npc->id }}"
                class="text-[11px] font-mono text-gray-400 mt-1"
            >
                {{ $deceasedDate }}
            </p>
        </div>
    </div>


    {{-- ============================================================
         MENU DE 3 PONTINHOS
         ============================================================ --}}
    <div class="absolute top-3 right-3 z-20 npc-card-actions">

        <button
            type="button"
            class="npc-menu-btn w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#cdbb9f]/40 text-[#6b1d14] transition-all bg-white/60 backdrop-blur-md shadow-sm hover:shadow"
            data-target="npc-menu-{{ $npc->id }}"
            aria-label="Opções de {{ $npc->name }}"
            aria-controls="npc-menu-{{ $npc->id }}"
            aria-expanded="false"
        >
            <svg
                class="w-4 h-4 pointer-events-none"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                ></path>
            </svg>
        </button>


        {{-- ========================================================
             DROPDOWN DO MENU
             ======================================================== --}}
        <div
            id="npc-menu-{{ $npc->id }}"
            class="npc-dropdown-menu hidden absolute right-0 mt-2 w-48 rounded-xl bg-[#fdfbf7] border border-[#cdbb9f]/60 shadow-xl z-30"
        >

            <button
                type="button"
                class="npc-move-folder-btn w-full text-left px-3 py-2.5 text-xs font-serif font-bold text-[#6b1d14] hover:bg-[#efe9dc] transition-colors flex items-center gap-2 border-b border-[#cdbb9f]/30"
                data-npc-id="{{ $npc->id }}"
                data-npc-name="{{ $npc->name }}"
                data-current-folder="{{ $npc->folder_id ?? '' }}"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14h6m-3-3 3 3-3 3"/></svg>
                Enviar para pasta
            </button>

            <button type="button"
                class="npc-edit-btn w-full text-left px-3 py-2.5 text-xs font-serif font-bold text-[#6b1d14] hover:bg-[#efe9dc] transition-colors flex items-center gap-2 border-b border-[#cdbb9f]/30"
                data-npc-id="{{ $npc->id }}"
                data-npc-name="{{ $npc->name }}"
                data-npc-image="{{ $npc->image_path ? asset('storage/'.$npc->image_path) : '' }}"
                data-image-zoom="{{ $npcImageZoom }}"
                data-image-position="{{ $npcImagePosition }}"
                data-image-horizontal="{{ $npcImageHorizontal }}"
            >
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M15 5l4 4M4 20l4.5-1 10.7-10.7a2 2 0 00-2.8-2.8L5.7 16.2 4 20Z"/></svg>
                Editar NPC
            </button>

            {{-- ----------------------------------------------------
                 TIRAR DA PASTA
                 ---------------------------------------------------- --}}
            @if($npc->folder_id)

                <form
                    method="POST"
                    action="{{ route('npcs.remove-folder', $npc) }}"
                    class="m-0 border-b border-[#cdbb9f]/20"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="w-full text-left px-3 py-2.5 hover:bg-amber-50/80 text-xs font-serif font-bold text-amber-700 transition-colors flex items-center gap-2"
                    >
                        <svg
                            class="w-3.5 h-3.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                            ></path>
                        </svg>

                        Tirar da Pasta
                    </button>
                </form>

            @endif


            {{-- ----------------------------------------------------
                 BAIXAR JSON
                 ---------------------------------------------------- --}}
            <a
                href="{{ route('npcs.download-json', $npc->id) }}"
                class="w-full text-left px-3 py-2.5 hover:bg-emerald-50/80 text-xs font-serif font-bold text-emerald-700 transition-colors flex items-center gap-2 border-b border-[#cdbb9f]/20"
            >
                <svg
                    class="w-3.5 h-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"
                    />
                </svg>

                Baixar JSON
            </a>


            {{-- ----------------------------------------------------
                 ALTERNAR FALECIDO
                 ---------------------------------------------------- --}}
            <button
                type="button"
                onclick="toggleFalecido('{{ $npc->id }}')"
                class="w-full text-left px-3 py-2.5 hover:bg-slate-100/80 text-xs font-serif font-bold text-slate-700 transition-colors flex items-center gap-2 border-b border-[#cdbb9f]/20"
            >
                <svg
                    class="w-3.5 h-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m-4-4h8"
                    ></path>
                </svg>

                Falecido
            </button>


            {{-- ----------------------------------------------------
                 EXCLUIR NPC
                 ---------------------------------------------------- --}}
            <form
                method="POST"
                action="{{ route('npcs.destroy', $npc->id) }}"
                onsubmit="return confirm('Deseja mesmo banir este NPC do seu grimório?')"
                class="m-0"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="w-full text-left px-3 py-2.5 hover:bg-red-50 text-xs font-serif font-bold text-red-700 transition-colors flex items-center gap-2"
                >
                    <svg
                        class="w-3.5 h-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a2 2 0 01-1 1v3M4 7h16"
                        />
                    </svg>

                    Excluir
                </button>
            </form>

        </div>
    </div>


    {{-- Carta ilustrada: arte à esquerda e informações à direita. --}}
    <div class="npc-card-art">
        <div class="npc-portrait-frame">
            @if($npc->image_path)
                <img
                    src="{{ asset('storage/'.$npc->image_path) }}"
                    alt="Retrato de {{ $npc->name }}"
                    class="npc-portrait-image"
                    data-npc-portrait="true"
                    data-image-position="{{ $npcImagePosition }}"
                    data-image-horizontal="{{ $npcImageHorizontal }}"
                    style="--npc-portrait-zoom: {{ $npcImageZoom }}; object-position: {{ $npcImageHorizontal }}% {{ $npcImagePosition === 'top' ? '0%' : ($npcImagePosition === 'bottom' ? '100%' : '50%') }}; transform-origin: {{ $npcImageHorizontal }}% {{ $npcImagePosition === 'top' ? '0%' : ($npcImagePosition === 'bottom' ? '100%' : '50%') }};"
                    loading="lazy"
                >
            @else
                <span class="npc-initials npc-card-art__initials" aria-label="Sem retrato">
                    {{ str($npc->name)->substr(0,2)->upper() }}
                </span>
            @endif
        </div>
        
    </div>

    <div class="npc-card-detail">
        <div class="npc-card-detail__eyebrow" aria-hidden="true">✦</div>
        <h2 class="npc-card-detail__name" title="{{ $npc->name }}">
            {{ $npc->name }}
        </h2>
        <p class="npc-card-creature" title="{{ $translatedSize }} {{ $translatedType }}">
            {{ $translatedSize }} · {{ $translatedType }}
        </p>

        <div class="npc-card-stats" aria-label="Atributos de {{ $npc->name }}">
            <div class="npc-card-stat">
                <span class="npc-card-stat__label">Vida</span>
                <strong class="npc-card-stat__value">{{ $npc->calculated_hp }}</strong>
            </div>
            <div class="npc-card-stat">
                <span class="npc-card-stat__label">CA</span>
                <strong class="npc-card-stat__value npc-card-stat__value--ac">{{ $npc->calculated_ac }}</strong>
            </div>
            <div class="npc-card-stat">
                <span class="npc-card-stat__label">ND</span>
                <strong class="npc-card-stat__value">{{ rtrim(rtrim(number_format($npc->challenge_rating ?? 0, 2, '.', ''), '0'), '.') }}</strong>
            </div>
        </div>

        <a class="npc-view-button" href="{{ route('npcs.show', $npc->id) }}">
            <span>Ver ficha</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" />
            </svg>
        </a>
    </div>

    {{-- Mantido para compatibilidade com o fluxo de arrastar e soltar. --}}
    <input type="hidden" class="npc-avatar" value="{{ $npc->image_path ? asset('storage/'.$npc->image_path) : '' }}">

</div>


{{-- ================================================================
     SCRIPT — STATUS DE FALECIMENTO
     ================================================================ --}}
<script>
async function toggleFalecido(id) {

    const overlay = document.getElementById(`falecido-overlay-${id}`);
    const tombIcon = document.getElementById(`tomb-icon-${id}`);
    const tombText = document.getElementById(`tomb-text-${id}`);
    const tombDetails = document.getElementById(`tomb-details-${id}`);
    const tombDate = document.getElementById(`tomb-date-${id}`);
    const menu = document.getElementById(`npc-menu-${id}`);

    if (menu) {
        menu.classList.add('hidden');
    }

    try {

        const response = await fetch(`/npcs/${id}/toggle-deceased`, {
            method: 'PATCH',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Erro ao salvar no banco');
        }

        const data = await response.json();


        {{-- ========================================================
             NPC VIVO
             ======================================================== --}}
        if (!data.is_deceased) {

            tombIcon.classList.add('translate-y-6');
            tombIcon.classList.remove('translate-y-0');

            tombText.classList.add(
                'translate-y-4',
                'opacity-0'
            );

            tombText.classList.remove(
                'translate-y-0',
                'opacity-100'
            );

            tombDetails.classList.add(
                'translate-y-4',
                'opacity-0'
            );

            tombDetails.classList.remove(
                'translate-y-0',
                'opacity-100'
            );

            setTimeout(() => {

                overlay.classList.add(
                    'opacity-0',
                    'pointer-events-none'
                );

                overlay.classList.remove(
                    'opacity-100',
                    'pointer-events-auto'
                );

            }, 300);


        {{-- ========================================================
             NPC FALECIDO
             ======================================================== --}}
        } else {

            if (tombDate) {
                tombDate.textContent = data.deceased_at;
            }

            overlay.classList.remove(
                'opacity-0',
                'pointer-events-none'
            );

            overlay.classList.add(
                'opacity-100',
                'pointer-events-auto'
            );

            setTimeout(() => {

                tombIcon.classList.remove('translate-y-6');
                tombIcon.classList.add('translate-y-0');

                tombText.classList.remove(
                    'translate-y-4',
                    'opacity-0'
                );

                tombText.classList.add(
                    'translate-y-0',
                    'opacity-100'
                );

                tombDetails.classList.remove(
                    'translate-y-4',
                    'opacity-0'
                );

                tombDetails.classList.add(
                    'translate-y-0',
                    'opacity-100'
                );

            }, 50);
        }

    } catch (error) {

        console.error(error);

        alert(
            'Não foi possível atualizar o status de falecimento.'
        );
    }
}
</script>