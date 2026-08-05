<x-app-layout>

    <style>
        main:has(.combats-index-page-scope),
        .py-12:has(.combats-index-page-scope),
        .max-w-7xl:has(.combats-index-page-scope),
        .sm\:px-6:has(.combats-index-page-scope),
        .lg\:px-8:has(.combats-index-page-scope) {
            padding: 0 !important;
            max-width: 100% !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        aside, .sidebar, body {
            overflow-x: hidden !important;
        }
        
        .transition-polished {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <div class="combats-index-page-scope min-h-screen bg-gradient-to-b from-[#efe9dc]/50 to-[#e8e2d2]/30 font-serif pb-16 selection:bg-[#6b1d14] selection:text-[#f4f1e8]">
        
        {{-- Cabeçalho Fixo do Grimório --}}
        <div class="sticky top-0 z-40 bg-[#efe9dc]/95 backdrop-blur-sm border-b border-[#cdbb9f]/60 shadow-[0_2px_10px_-3px_rgba(107,29,20,0.05)] transition-polished">
            <div class="w-full mx-auto px-6 py-4 sm:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-3 shrink-0 self-start sm:self-auto">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#6b1d14] to-[#53150f] flex items-center justify-center shadow-md border border-[#8c6239]/40">
                            <svg class="w-4 h-4 text-[#f4f1e8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg lg:text-xl font-serif font-black text-[#53150f] tracking-wide">
                                Biblioteca de Combates
                            </h1>
                            <p class="text-[11px] font-serif text-[#8c6239] italic">Gerencie seus encontros e sessões ativas</p>
                        </div>
                    </div>

                    <a href="{{ route('combats.create') }}"
                       class="w-full sm:w-auto px-5 h-10 rounded-xl bg-gradient-to-r from-[#6b1d14] to-[#8a2519] hover:from-[#53150f] hover:to-[#6b1d14] text-[#f4f1e8] font-serif font-bold text-[11px] uppercase tracking-widest transition-polished shadow-md hover:shadow-lg flex items-center justify-center gap-2 shrink-0 transform hover:-translate-y-0.5 focus:outline-none border border-[#53150f]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Combate
                    </a>
                    
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">

            @if(session('success'))
                <div class="mb-8 flex items-center p-4 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm text-emerald-800 animate-fade-in-down">
                    <svg class="w-5 h-5 mr-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-serif text-xs font-semibold leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid gap-6">
                @forelse($combats as $combat)

                    <div class="group bg-gradient-to-br from-[#f4f1e8] via-[#f7f4ed] to-[#efe9dc] border border-[#cdbb9f]/70 hover:border-[#8c6239] rounded-2xl shadow-[0_4px_20px_rgb(107,29,20,0.06)] hover:shadow-[0_8px_30px_rgb(107,29,20,0.14)] transition-polished overflow-hidden transform hover:-translate-y-1 relative">
                        
                        {{-- Detalhe Decorativo Lateral --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#8c6239] via-[#6b1d14] to-[#53150f] opacity-80 group-hover:opacity-100 transition-polished"></div>

                        <div class="p-6 sm:p-7 flex flex-col lg:flex-row justify-between gap-6 ml-1.5">
                            
                            {{-- Lado Esquerdo: Informações do Encontro --}}
                            <div class="flex-1 space-y-5 w-full">
                                
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-xl sm:text-2xl font-serif font-black text-[#53150f] leading-tight capitalize group-hover:text-[#6b1d14] transition-colors">
                                            {{ $combat->name }}
                                        </h2>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-[#8c6239]/15 text-[#8c6239] border border-[#8c6239]/30">
                                                Criado Em:
                                            </span>
                                            <span class="text-[11px] text-[#8c6239]/80 font-mono font-semibold flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-[#8c6239]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $combat->created_at->format('d/m/Y \à\s H:i') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Painel de Métricas / Status --}}
                                    <div class="flex bg-white/70 border border-[#cdbb9f]/60 rounded-xl p-2 shadow-inner">
                                        <div class="px-4 flex flex-col items-center justify-center border-r border-[#cdbb9f]/50">
                                            <span class="text-[9px] font-serif font-bold text-amber-900/70 uppercase tracking-widest mb-0.5">Rodada</span>
                                            <span class="text-base font-mono font-black text-amber-950 leading-none">{{ $combat->current_round ?? 0 }}</span>
                                        </div>
                                        <div class="px-4 flex flex-col items-center justify-center">
                                            <span class="text-[9px] font-serif font-bold text-[#8c6239]/80 uppercase tracking-widest mb-0.5">Status</span>
                                            @if($combat->is_active)
                                                <span class="text-[10px] font-serif font-bold text-emerald-700 leading-none flex items-center gap-1.5 mt-0.5">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Iniciado
                                                </span>
                                            @else
                                                <span class="text-[10px] font-serif font-bold text-amber-800 leading-none flex items-center gap-1.5 mt-0.5">
                                                    <span class="w-2 h-2 rounded-full bg-amber-600"></span> Não Iniciado
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="h-[1px] bg-gradient-to-r from-[#cdbb9f]/70 via-[#cdbb9f]/30 to-transparent w-full"></div>

                                {{-- Participantes --}}
                                <div>
                                    <h3 class="text-[10px] font-serif font-bold text-[#8c6239] uppercase tracking-widest mb-2.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#6b1d14]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Combatentes em Campo
                                    </h3>
                                    
                                    @php
                                        $participants = $combat->npcs ?? collect();
                                    @endphp

                                    @if($participants->count() > 0)
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($participants as $combatNpc)
                                                @php
                                                    $npc = $combatNpc->npc;
                                                @endphp
                                                @if($npc)
                                                    <div class="group/npc flex flex-col items-center p-3 bg-gradient-to-b from-[#fbf9f4] to-[#f4f0e6] border border-[#cdbb9f]/80 hover:border-[#8c6239] rounded-2xl shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5">
                                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gradient-to-br from-[#8c6239] to-[#53150f] flex items-center justify-center shadow-inner mb-2 border border-[#8c6239]/40 group-hover/npc:border-[#8c6239] transition-colors">
                                                            @if($npc->image_path)
                                                                <img
                                                                    src="{{ asset('storage/' . $npc->image_path) }}"
                                                                    alt="{{ $npc->name }}"
                                                                    class="w-full h-full object-cover object-top"
                                                                >
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#8c6239] to-[#53150f]">
                                                                    <span class="text-[#f4f1e8] font-bold text-xs">
                                                                        {{ strtoupper(substr($npc->name, 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <a href="{{ route('npcs.show', $npc) }}" class="text-xs font-serif font-bold text-[#53150f] hover:text-[#6b1d14] truncate max-w-[110px] text-center transition-colors" title="{{ $npc->name }}">
                                                            {{ $npc->name }}
                                                        </a>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-[#efe9dc]/70 border border-dashed border-[#cdbb9f] rounded-xl">
                                            <svg class="w-4 h-4 text-[#8c6239]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                            <p class="text-xs font-serif text-[#8c6239] italic">
                                                Nenhum combatente adicionado a este encontro.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- Lado Direito: Ações --}}
                            <div class="flex sm:flex-row lg:flex-col items-center justify-center gap-3 shrink-0 w-full lg:w-44 self-stretch lg:self-center pt-4 lg:pt-0 lg:pl-6 lg:border-l border-[#cdbb9f]/50">
                                
                                <a href="{{ route('combats.show', $combat) }}"
                                   class="flex-1 lg:flex-none w-full px-4 py-3 rounded-xl bg-gradient-to-r from-[#6b1d14] to-[#53150f] hover:from-[#53150f] hover:to-[#41100b] text-[#f4f1e8] text-center font-serif font-bold text-[11px] uppercase tracking-widest transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 border border-[#8c6239]/50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                    Abrir Combate
                                </a>

                                <form action="{{ route('combats.destroy', $combat) }}"
                                      method="POST"
                                      class="m-0 flex-1 lg:flex-none w-full"
                                      onsubmit="return confirm('Deseja realmente apagar os registros desta batalha?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl border border-red-300/70 text-red-800 bg-red-50/40 hover:bg-red-100/70 text-[10px] font-serif font-bold uppercase tracking-widest transition-polished flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Excluir
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>
                @empty
                    
                    <div class="bg-gradient-to-br from-[#f4f1e8] to-[#efe9dc] border-2 border-dashed border-[#cdbb9f] rounded-2xl p-14 flex flex-col items-center justify-center text-center shadow-sm">
                        <div class="w-16 h-16 bg-[#efe9dc] rounded-2xl flex items-center justify-center mb-4 border border-[#cdbb9f]/60 shadow-inner">
                            <svg class="w-8 h-8 text-[#8c6239]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-serif font-black text-[#53150f] mb-2">
                            Nenhum combate registrado
                        </h2>
                        <p class="text-sm text-[#8c6239] italic mb-6 max-w-md">
                            Os ventos da guerra estão calmos. Crie seu primeiro encontro para organizar iniciativas e comandar o campo de batalha.
                        </p>
                        <a href="{{ route('combats.create') }}"
                           class="inline-flex items-center gap-2 bg-[#6b1d14] hover:bg-[#53150f] text-[#f4f1e8] font-serif font-bold text-xs uppercase tracking-widest px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition-all border border-[#8c6239] hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Preparar Batalha
                        </a>
                    </div>

                @endforelse
            </div>

        </div>
    </div>

</x-app-layout>