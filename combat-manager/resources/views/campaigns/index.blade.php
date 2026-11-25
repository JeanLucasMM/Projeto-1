<x-app-layout>
    @php
        $isMasterMode = session('dashboard_mode', 'player') === 'master';
        $isPlayerMode = !$isMasterMode;
        $viewerId = (int) auth()->id();

        // Carregamento em lote: evita consultas individuais por card.
        if ($isPlayerMode && method_exists($joinedCampaigns, 'loadMissing')) {
            $joinedCampaigns->loadMissing([
                'owner',
                'members.user',
                'characters.user',
                'characters.classes',
                'characters.combat',
            ]);
        }

        if ($isMasterMode && method_exists($ownedCampaigns, 'loadMissing')) {
            $ownedCampaigns->loadMissing(['owner', 'members.user']);
        }
    @endphp

    <style>
        .sb-campaign-index{--camp-ink:#4b231d;--camp-wine:#6c241c;--camp-bronze:#a17b56;--camp-muted:#856b56;--camp-line:#d8c5ae;color:var(--camp-ink)}
        .sb-campaign-heading{border-bottom:1px solid rgba(151,114,78,.25);padding-bottom:20px;margin-bottom:24px}
        .sb-campaign-title{font:900 clamp(25px,3vw,36px)/1.05 Georgia,'Times New Roman',serif;color:#5b1b14}
        .sb-campaign-subtitle{font:600 11px/1.6 system-ui,sans-serif;color:var(--camp-muted);margin-top:7px}
        .sb-campaign-section-title{font:900 20px/1.15 Georgia,serif;color:#5b211b}
        .sb-campaign-btn{display:inline-flex;justify-content:center;align-items:center;min-height:39px;gap:8px;border:1px solid #72251d;border-radius:11px;background:#72251d;color:#fff8ef;padding:10px 17px;font:900 10px/1 system-ui,sans-serif;letter-spacing:.08em;text-decoration:none;transition:filter .16s,transform .16s}
        .sb-campaign-btn:hover{filter:brightness(1.1);transform:translateY(-1px)}
        .sb-campaign-btn.secondary{background:#fffdf9;border-color:#d5bea5;color:#673229}
        .sb-campaign-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
        .sb-campaign-card{position:relative;isolation:isolate;overflow:hidden;background:linear-gradient(180deg,#fffdf9,#f4eadc);border:1px solid rgba(149,108,75,.34);border-radius:22px;box-shadow:0 12px 32px rgba(63,35,24,.09),inset 0 1px rgba(255,255,255,.84);transition:transform .23s,border-color .23s,box-shadow .23s}
        .sb-campaign-card:hover{transform:translateY(-3px);border-color:rgba(142,96,62,.57);box-shadow:0 18px 44px rgba(63,35,24,.13)}
        .sb-campaign-cover{position:relative;height:190px;overflow:hidden;background:radial-gradient(ellipse at 80% 25%,#b99c77 0%,transparent 40%),linear-gradient(135deg,#4e302a,#83604c 57%,#b28c64)}
        .sb-campaign-cover::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(23,12,10,.08),rgba(33,18,15,.28) 40%,rgba(30,15,12,.83));pointer-events:none}
        .sb-campaign-cover-photo{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;filter:saturate(.9);transition:transform .6s ease}
        .sb-campaign-card:hover .sb-campaign-cover-photo{transform:scale(1.035)}
        .sb-campaign-cover-engraving{position:absolute;right:-34px;top:-45px;width:220px;height:220px;border:1px solid rgba(244,219,181,.15);border-radius:50%;box-shadow:0 0 0 25px rgba(242,206,168,.045),0 0 0 55px rgba(242,206,168,.03);pointer-events:none}
        .sb-campaign-cover-upload{position:absolute;z-index:4;right:14px;top:13px;display:flex;align-items:center;gap:6px}
        .sb-campaign-cover-upload label,.sb-campaign-cover-upload button{cursor:pointer;display:inline-flex;align-items:center;justify-content:center;min-height:27px;border:1px solid rgba(255,245,225,.3);border-radius:8px;padding:5px 9px;background:rgba(32,17,15,.65);color:#ffefdc;font:900 9px/1 system-ui,sans-serif;backdrop-filter:blur(5px)}
        .sb-campaign-cover-upload input[type=file]{position:absolute;width:1px;height:1px;opacity:0;overflow:hidden}
        .sb-campaign-cover-label{position:absolute;top:15px;left:17px;z-index:2;color:#fff0d6;font:900 8px/1 system-ui,sans-serif;letter-spacing:.2em;text-transform:uppercase;background:rgba(39,21,19,.45);border:1px solid rgba(244,221,190,.18);border-radius:999px;padding:8px 11px;backdrop-filter:blur(5px)}
        .sb-campaign-cover-name{position:absolute;z-index:2;left:22px;right:22px;bottom:19px;color:#fff7eb;font:900 clamp(22px,2vw,29px)/1.1 Georgia,serif;text-shadow:0 3px 12px rgba(0,0,0,.35);overflow-wrap:anywhere}
        .sb-campaign-body{padding:17px 20px 18px}
        .sb-campaign-masterline{display:flex;align-items:center;gap:10px;min-width:0}
        .sb-campaign-avatar{display:grid;place-items:center;flex:0 0 34px;width:34px;height:34px;border-radius:11px;background:linear-gradient(160deg,#f3e1cc,#ead0b6);border:1px solid #d7b997;color:#65261d;font:900 16px Georgia,serif}
        .sb-campaign-smalllabel{display:block;color:#a17c55;font:900 8px/1 system-ui,sans-serif;letter-spacing:.16em;text-transform:uppercase;margin-bottom:4px}
        .sb-campaign-mastername{display:block;color:#613529;font:800 12px/1.2 system-ui,sans-serif;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .sb-campaign-party{border-top:1px solid rgba(164,128,97,.24);margin-top:15px;padding-top:13px}
        .sb-campaign-party-label{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:11px}
        .sb-campaign-person-list{display:flex;gap:8px;align-items:center;flex-wrap:wrap;min-height:35px}
        .sb-campaign-person{max-width:100%;display:inline-flex;align-items:center;gap:6px;border-radius:999px;border:1px solid #e0cdb9;background:#faf2e8;padding:4px 9px 4px 5px;font:800 9px/1.2 system-ui,sans-serif;color:#664235}
        .sb-campaign-person-avatar{display:grid;place-items:center;width:23px;height:23px;flex:0 0 23px;border-radius:50%;background:#dbc1a4;color:#5b2a21;font:900 11px Georgia,serif;overflow:hidden}
        .sb-campaign-person-avatar img{width:100%;height:100%;object-fit:cover}
        .sb-campaign-active{display:flex;gap:12px;align-items:center;margin-top:14px;border:1px solid rgba(150,109,72,.26);border-radius:13px;padding:10px;background:rgba(242,229,213,.5)}
        .sb-campaign-active-image{display:grid;place-items:center;width:46px;height:51px;flex:0 0 46px;border-radius:9px;overflow:hidden;background:#dfc6a9;font:900 21px Georgia,serif}
        .sb-campaign-active-image img{width:100%;height:100%;object-fit:cover;object-position:top}
        .sb-campaign-active-title{font:900 13px/1.25 Georgia,serif;color:#52251c}
        .sb-campaign-active-meta{font:700 9px/1.45 system-ui,sans-serif;color:#80624f;margin-top:3px}
        .sb-campaign-card-foot{margin-top:15px;display:flex;gap:8px;align-items:center;justify-content:space-between}
        .sb-campaign-empty{border:1px dashed rgba(146,113,86,.4);border-radius:20px;padding:32px 24px;text-align:center;background:rgba(255,252,247,.5);font:600 12px/1.6 system-ui,sans-serif;color:#785b45}
        .sb-campaign-invite{border:1px solid rgba(163,123,87,.36);border-radius:16px;background:#fffcf7;padding:16px;box-shadow:0 5px 15px rgba(73,39,19,.05)}
        .sb-campaign-invite h3{font:900 17px/1.2 Georgia,serif;color:#5d251e;margin-top:4px}
        .sb-campaign-invite p{font:700 10px/1.5 system-ui,sans-serif;color:#82604f;margin-top:5px}
        .sb-campaign-modal-backdrop{position:fixed;inset:0;z-index:170;display:grid;place-items:center;padding:16px;background:rgba(20,11,9,.7);backdrop-filter:blur(3px)}
        .sb-campaign-modal{width:min(480px,100%);overflow:hidden;border:1px solid #c9ab8b;border-radius:20px;background:#fcf7ef;box-shadow:0 30px 90px rgba(0,0,0,.4)}
        .sb-campaign-field{width:100%;border:1px solid #d7bfa7;background:#fffdf8;border-radius:10px;padding:10px 12px;font:600 13px system-ui,sans-serif;color:#4c3025}
        @media(max-width:800px){.sb-campaign-grid{grid-template-columns:minmax(0,1fr)}.sb-campaign-cover{height:165px}.sb-campaign-body{padding:16px}}
        @media(prefers-reduced-motion:reduce){.sb-campaign-card,.sb-campaign-cover-photo,.sb-campaign-btn{transition:none!important}}
    </style>

    <main class="sb-campaign-index min-h-screen px-4 py-8 sm:px-6" x-data="{ createOpen: {{ $errors->any() ? 'true' : 'false' }} }">
        <div class="mx-auto max-w-[1380px]">
            <header class="sb-campaign-heading flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="mb-2 text-[9px] font-black uppercase tracking-[.24em] text-[#9b7653]">Caderno de jornadas</p>
                    <h1 class="sb-campaign-title">Campanhas</h1>
                    <p class="sb-campaign-subtitle">{{ $isMasterMode ? 'Suas mesas, seus aventureiros e as histórias que você conduz.' : 'Suas mesas e os companheiros que compartilham a jornada.' }}</p>
                </div>
                @if($isMasterMode)
                    <button type="button" class="sb-campaign-btn" @click="createOpen = true">+ Nova campanha</button>
                @endif
            </header>

            @if(session('success'))
                <p class="mb-5 rounded-xl border border-[#d6bd9f] bg-[#fff8ed] p-3 text-xs font-semibold text-[#6f392a]">{{ session('success') }}</p>
            @endif
            @if($errors->any())
                <div class="mb-5 rounded-xl border border-red-300 bg-red-50 p-3 text-xs text-red-800">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            @if($isPlayerMode && $pendingInvitations->isNotEmpty())
                <section class="mb-8">
                    <div class="mb-4 flex items-center gap-3"><h2 class="sb-campaign-section-title">Convites</h2><span class="rounded-full bg-[#e8d6bf] px-2 py-1 text-[9px] font-black text-[#68342a]">{{ $pendingInvitations->count() }}</span></div>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach($pendingInvitations as $invitation)
                            <article class="sb-campaign-invite">
                                <span class="sb-campaign-smalllabel">Convite para uma aventura</span>
                                <h3>{{ $invitation->campaign->name }}</h3>
                                <p>Mestre: {{ $invitation->campaign->owner->name }}</p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('campaign-invitations.accept', $invitation) }}">@csrf<button class="sb-campaign-btn" type="submit">Aceitar</button></form>
                                    <form method="POST" action="{{ route('campaign-invitations.decline', $invitation) }}">@csrf<button class="sb-campaign-btn secondary" type="submit">Recusar</button></form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <section>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="sb-campaign-section-title">{{ $isMasterMode ? 'Minhas mesas' : 'Minhas campanhas' }}</h2>
                    <span class="text-[10px] font-bold text-[#957658]">{{ $isMasterMode ? $ownedCampaigns->count() : $joinedCampaigns->count() }} {{ $isMasterMode ? 'campanhas' : 'jornadas' }}</span>
                </div>
                @php $list = $isMasterMode ? $ownedCampaigns : $joinedCampaigns; @endphp
                @if($list->isEmpty())
                    <div class="sb-campaign-empty">
                        {{ $isMasterMode ? 'Nenhuma campanha criada. Comece uma nova história.' : 'Você ainda não participa de uma campanha. Quando aceitar um convite, ela aparecerá aqui.' }}
                    </div>
                @else
                    <div class="sb-campaign-grid">
                        @foreach($list as $campaign)
                            @php
                                // Regra de privacidade: nunca renderizar nomes/avatares ocultos
                                // para outros jogadores; o dono da ficha pode ver a si mesmo.
                                $visibleMembers = $campaign->members->filter(function ($member) use ($viewerId, $isMasterMode) {
                                    return $isMasterMode
                                        || !(bool) ($member->hidden_from_party ?? false)
                                        || (int) $member->user_id === $viewerId;
                                });
                                $visibleIds = $visibleMembers->pluck('user_id')->map(fn ($id) => (int) $id)->all();
                                $visibleCharacters = $isPlayerMode
                                    ? $campaign->characters->filter(fn ($char) => in_array((int) $char->user_id, $visibleIds, true))
                                    : collect();
                                $myActiveCharacter = $isPlayerMode
                                    ? $visibleCharacters->first(fn ($char) => (int) $char->user_id === $viewerId && (bool) ($char->pivot->is_active ?? false))
                                    : null;
                                $cover = $campaign->cover_image_path ?? null;
                                $focus = max(0, min(100, (int) ($campaign->cover_image_focus_y ?? 50)));
                            @endphp
                            <article class="sb-campaign-card">
                                <div class="sb-campaign-cover">
                                    @if($cover)
                                        <img class="sb-campaign-cover-photo" src="{{ Storage::url($cover) }}" alt="" loading="lazy" style="object-position:center {{ $focus }}%">
                                    @else
                                        <span class="sb-campaign-cover-engraving" aria-hidden="true"></span>
                                    @endif
                                    <span class="sb-campaign-cover-label">{{ $isMasterMode ? 'Mesa do Mestre' : 'Campanha do Player' }}</span>
                                    @if($isMasterMode)
                                        <div class="sb-campaign-cover-upload">
                                            <form action="{{ route('campaigns.cover.update', $campaign) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <label title="Selecionar foto de capa para esta campanha">
                                                    <span>{{ $cover ? 'Trocar capa' : '+ Capa' }}</span>
                                                    <input type="file" name="cover_image" accept="image/png,image/jpeg,image/webp" aria-label="Escolher capa" onchange="if(this.files.length){this.form.submit()}">
                                                </label>
                                            </form>
                                            @if($cover)
                                                <form action="{{ route('campaigns.cover.update', $campaign) }}" method="POST">@csrf<input type="hidden" name="remove_cover" value="1"><button type="submit" title="Remover a capa">×</button></form>
                                            @endif
                                        </div>
                                    @endif
                                    <h3 class="sb-campaign-cover-name">{{ $campaign->name }}</h3>
                                </div>
                                <div class="sb-campaign-body">
                                    <div class="sb-campaign-masterline">
                                        <span class="sb-campaign-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($campaign->owner?->name ?? 'M', 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <small class="sb-campaign-smalllabel">Mestre da campanha</small>
                                            <strong class="sb-campaign-mastername">{{ $campaign->owner?->name ?? 'Mestre' }}</strong>
                                        </div>
                                    </div>
                                    <div class="sb-campaign-party">
                                        <div class="sb-campaign-party-label">
                                            <small class="sb-campaign-smalllabel">Jogadores {{ $isMasterMode ? 'da mesa' : 'visíveis na Party' }}</small>
                                            <span class="text-[9px] font-bold text-[#896a51]">{{ $visibleMembers->count() }}</span>
                                        </div>
                                        <div class="sb-campaign-person-list">
                                            @forelse($visibleMembers as $member)
                                                <span class="sb-campaign-person">
                                                    <span class="sb-campaign-person-avatar">{{ mb_strtoupper(mb_substr($member->user?->name ?? '?', 0, 1)) }}</span>
                                                    {{ $member->user?->name ?? 'Jogador' }}
                                                    @if((int) $member->user_id === $viewerId)<small>(você)</small>@endif
                                                    @if($isMasterMode && $member->hidden_from_party)<small>· oculto</small>@endif
                                                </span>
                                            @empty
                                                <span class="text-[10px] font-medium text-[#957960]">Nenhum jogador visível nesta mesa.</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    @if($isPlayerMode)
                                        @if($myActiveCharacter)
                                            <div class="sb-campaign-active">
                                                <div class="sb-campaign-active-image">
                                                    @if($myActiveCharacter->image_path)
                                                        <img src="{{ Storage::url($myActiveCharacter->image_path) }}" alt="" loading="lazy">
                                                    @else
                                                        {{ mb_strtoupper(mb_substr($myActiveCharacter->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <small class="sb-campaign-smalllabel">Seu personagem em jogo</small>
                                                    <p class="sb-campaign-active-title">{{ $myActiveCharacter->name }}</p>
                                                    <p class="sb-campaign-active-meta">Nível {{ $myActiveCharacter->level }} · {{ $myActiveCharacter->class_label }}</p>
                                                    @if($campaign->experience_enabled)
                                                        @php
                                                            $xpInfo = \App\Support\CharacterExperience::progress((int) $myActiveCharacter->level,(int) ($myActiveCharacter->pivot->experience_points ?? 0));
                                                        @endphp
                                                        <p class="sb-campaign-active-meta">{{ number_format($xpInfo['experience'],0,',','.') }} XP{{ $xpInfo['max_level'] ? ' · Nível máximo' : ' · Próximo: ' . number_format($xpInfo['next_threshold'],0,',','.') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="mt-4 text-[10px] font-semibold text-[#8c6d54]">Nenhum personagem seu está ativo nesta campanha.</p>
                                        @endif
                                    @endif
                                    <div class="sb-campaign-card-foot">
                                        <span class="text-[9px] font-bold text-[#9c775b]">{{ $isPlayerMode && $campaign->experience_enabled ? 'Progressão por XP' : ($isMasterMode ? 'Gerenciar campanha' : 'Campanha compartilhada') }}</span>
                                        <a href="{{ route($isMasterMode ? 'campaigns.show' : 'campaigns.overview', $campaign) }}" class="sb-campaign-btn">{{ $isMasterMode ? 'Abrir mesa' : 'Entrar na campanha' }} <span aria-hidden="true">›</span></a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        @if($isMasterMode)
            <template x-teleport="body">
                <div x-show="createOpen" x-cloak @keydown.escape.window="createOpen = false" class="sb-campaign-modal-backdrop">
                    <div class="absolute inset-0" @click="createOpen = false"></div>
                    <form method="POST" action="{{ route('campaigns.store') }}" class="sb-campaign-modal relative z-10" @click.stop>
                        @csrf
                        <div class="border-b border-[#dbc4a9] bg-[#f3e4d2] px-5 py-4">
                            <p class="sb-campaign-smalllabel">Uma nova história</p>
                            <h2 class="font-serif text-xl font-black text-[#5b211b]">Criar campanha</h2>
                            <p class="mt-1 text-[11px] text-[#805c4b]">Comece com o essencial. Os jogadores entram por convite.</p>
                        </div>
                        <div class="space-y-4 p-5">
                            <div><label for="sb-campaign-new-name" class="sb-campaign-smalllabel">Nome da campanha</label><input id="sb-campaign-new-name" class="sb-campaign-field" name="name" required maxlength="120" value="{{ old('name') }}"></div>
                            <div><label for="sb-campaign-new-desc" class="sb-campaign-smalllabel">Descrição</label><textarea id="sb-campaign-new-desc" class="sb-campaign-field" rows="4" name="description" maxlength="2000">{{ old('description') }}</textarea></div>
                            <p class="text-[10px] leading-relaxed text-[#8d6e56]">Depois de criar, você poderá adicionar uma capa diretamente pelo card da campanha.</p>
                        </div>
                        <div class="flex justify-end gap-2 border-t border-[#dbc4a9] bg-[#f7ede1] px-5 py-3">
                            <button class="sb-campaign-btn secondary" type="button" @click="createOpen = false">Cancelar</button>
                            <button class="sb-campaign-btn" type="submit">Criar mesa</button>
                        </div>
                    </form>
                </div>
            </template>
        @endif
    </main>
</x-app-layout>
