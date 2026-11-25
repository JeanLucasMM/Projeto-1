<x-app-layout>
    @php
        $cover = $campaign->cover_image_path ?? null;
        $focus = max(0, min(100, (int) ($campaign->cover_image_focus_y ?? 50)));
        $myXp = $myCharacter && $campaign->experience_enabled
            ? \App\Support\CharacterExperience::progress((int) $myCharacter->level,(int) ($myCharacter->pivot->experience_points ?? 0))
            : null;
    @endphp
    <style>
        .sb-camp-overview{--c-ink:#61251e;--c-text:#6d4d3e;--c-edge:rgba(155,113,73,.32);color:var(--c-ink)}
        .sb-camp-overview-hero{position:relative;isolation:isolate;min-height:285px;border-radius:24px;overflow:hidden;background:radial-gradient(ellipse at 75% 30%,#b99c7c,transparent 50%),linear-gradient(135deg,#4d3531,#856048);border:1px solid rgba(126,91,61,.35);box-shadow:0 18px 35px rgba(46,24,19,.14)}
        .sb-camp-overview-hero>img{width:100%;height:100%;position:absolute;inset:0;object-fit:cover}
        .sb-camp-overview-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(0deg,rgba(22,11,10,.92),rgba(28,16,14,.4) 65%,rgba(26,13,11,.10));z-index:0}
        .sb-camp-overview-hero-body{position:relative;z-index:1;display:flex;min-height:285px;flex-direction:column;justify-content:flex-end;padding:26px 29px;color:#fff7e8}
        .sb-camp-overview-hero-kicker{font:900 9px/1 system-ui,sans-serif;letter-spacing:.24em;text-transform:uppercase;color:#e1bc91}
        .sb-camp-overview-hero-title{font:900 clamp(27px,4.5vw,48px)/1.12 Georgia,serif;margin-top:9px}
        .sb-camp-overview-hero-text{font:600 11px/1.55 system-ui,sans-serif;max-width:720px;color:#f1d9bd;margin-top:9px}
        .sb-camp-overview-panel{border-radius:20px;border:1px solid var(--c-edge);background:linear-gradient(155deg,#fffdf9,#f5ebde);padding:20px;box-shadow:0 8px 22px rgba(59,28,16,.065)}
        .sb-camp-overview-title{font:900 21px/1.2 Georgia,serif;color:#5f2119}
        .sb-camp-overview-label{display:block;font:900 9px/1.2 system-ui,sans-serif;color:#9b7755;text-transform:uppercase;letter-spacing:.15em}
        .sb-camp-overview-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(155,113,73,.17)}
        .sb-camp-overview-row:last-child{border-bottom:0}
        .sb-camp-overview-avatar{width:44px;height:50px;flex:0 0 44px;border-radius:10px;overflow:hidden;background:#dfcbb4;display:grid;place-items:center;font:900 19px Georgia,serif;color:#5e2820}
        .sb-camp-overview-avatar img{width:100%;height:100%;object-fit:cover;object-position:top}
        .sb-camp-overview-person{font:900 13px/1.2 Georgia,serif;color:#5b221b}
        .sb-camp-overview-detail{font:700 10px/1.45 system-ui,sans-serif;color:var(--c-text);margin-top:4px}
        .sb-camp-overview-link{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 16px;border-radius:10px;background:#74281f;color:#fff8ef;font:900 10px/1.2 system-ui,sans-serif;letter-spacing:.06em;text-decoration:none}
        .sb-camp-overview-link.secondary{background:#fff9f0;color:#6f392d;border:1px solid rgba(166,126,92,.37)}
        .sb-camp-overview-progress{height:5px;margin-top:8px;background:#dfcdb8;border-radius:999px;overflow:hidden}
        .sb-camp-overview-progress span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#987144,#cda26d)}
        @media(max-width:640px){.sb-camp-overview-hero-body{padding:20px}.sb-camp-overview-hero{min-height:225px}.sb-camp-overview-hero-body{min-height:225px}.sb-camp-overview-panel{padding:16px}}
    </style>
    <main class="sb-camp-overview min-h-screen px-4 py-7 md:px-6">
        <div class="mx-auto max-w-[1360px]">
            <nav class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('campaigns.index') }}" class="sb-camp-overview-link secondary">‹ Campanhas</a>
                <a href="{{ route('campaigns.show', $campaign) }}" class="sb-camp-overview-link">Abrir mesa ›</a>
            </nav>
            <header class="sb-camp-overview-hero">
                @if($cover)
                    <img src="{{ Storage::url($cover) }}" alt="" style="object-position: center {{ $focus }}%">
                @endif
                <div class="sb-camp-overview-hero-body">
                    <span class="sb-camp-overview-hero-kicker">Crônicas da campanha</span>
                    <h1 class="sb-camp-overview-hero-title">{{ $campaign->name }}</h1>
                    @if($campaign->description)
                        <p class="sb-camp-overview-hero-text">{{ \Illuminate\Support\Str::limit(strip_tags($campaign->description), 200) }}</p>
                    @endif
                </div>
            </header>
            <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1.55fr)_minmax(290px,1fr)]">
                <div class="space-y-5">
                    @if($myCharacter)
                        <section class="sb-camp-overview-panel">
                            <span class="sb-camp-overview-label">Seu personagem em jogo</span>
                            <div class="sb-camp-overview-row">
                                <div class="sb-camp-overview-avatar">
                                    @if($myCharacter->image_path)<img src="{{ Storage::url($myCharacter->image_path) }}" alt="">@else{{ mb_strtoupper(mb_substr($myCharacter->name,0,1)) }}@endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="sb-camp-overview-person">{{ $myCharacter->name }}</p>
                                    <p class="sb-camp-overview-detail">Nível {{ $myCharacter->level }} · {{ $myCharacter->class_label }} · {{ (int)($myCharacter->combat?->current_hp ?? 0) }}/{{ (int)($myCharacter->effective_max_hp ?? 0) }} PV</p>
                                    @if($myXp)
                                        <p class="sb-camp-overview-detail">{{ number_format($myXp['experience'],0,',','.') }} XP {{ $myXp['max_level'] ? '· Nível máximo' : '· ' . number_format($myXp['remaining'],0,',','.') . ' restantes' }}</p>
                                        @if(!$myXp['max_level'])
                                            <div class="sb-camp-overview-progress" aria-label="Progresso de experiência"><span style="width:{{ $myXp['percent'] }}%"></span></div>
                                        @endif
                                    @endif
                                </div>
                                <a class="sb-camp-overview-link" href="{{ route('characters.show', ['character' => $myCharacter, 'campaign_id' => $campaign->id]) }}">Ficha</a>
                            </div>
                        </section>
                    @endif
                    <section class="sb-camp-overview-panel">
                        <div class="flex items-center justify-between gap-3"><h2 class="sb-camp-overview-title">Companheiros de jornada</h2><span class="sb-camp-overview-label">{{ $activeCharacters->count() }} ativos</span></div>
                        @forelse($activeCharacters as $character)
                            <div class="sb-camp-overview-row">
                                <div class="sb-camp-overview-avatar">
                                    @if($character->image_path)<img src="{{ Storage::url($character->image_path) }}" alt="">@else{{ mb_strtoupper(mb_substr($character->name,0,1)) }}@endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="sb-camp-overview-person">{{ $character->name }}</p>
                                    <p class="sb-camp-overview-detail">{{ $character->user?->name ?? 'Jogador' }} · Nível {{ $character->level }} · {{ $character->class_label }}</p>
                                </div>
                                @if((int)$character->user_id === (int)auth()->id())<span class="sb-camp-overview-label">Você</span>@endif
                            </div>
                        @empty
                            <p class="sb-camp-overview-detail mt-3">Nenhum personagem ativo está visível nesta campanha.</p>
                        @endforelse
                    </section>
                </div>
                <aside class="space-y-5">
                    <section class="sb-camp-overview-panel">
                        <h2 class="sb-camp-overview-title">Mestre da campanha</h2>
                        <div class="sb-camp-overview-row">
                            <div class="sb-camp-overview-avatar">{{ mb_strtoupper(mb_substr($campaign->owner?->name ?? 'M',0,1)) }}</div>
                            <div><p class="sb-camp-overview-person">{{ $campaign->owner?->name ?? 'Mestre' }}</p><p class="sb-camp-overview-detail">Responsável pela mesa</p></div>
                        </div>
                    </section>
                    <section class="sb-camp-overview-panel">
                        <div class="flex items-center justify-between"><h2 class="sb-camp-overview-title">Jogadores</h2><span class="sb-camp-overview-label">{{ $visibleMembers->count() }}</span></div>
                        @forelse($visibleMembers as $member)
                            <div class="sb-camp-overview-row">
                                <div class="sb-camp-overview-avatar">{{ mb_strtoupper(mb_substr($member->user?->name ?? '?',0,1)) }}</div>
                                <div class="min-w-0 flex-1"><p class="sb-camp-overview-person">{{ $member->user?->name ?? 'Jogador' }}</p><p class="sb-camp-overview-detail">{{ (int)$member->user_id === (int)auth()->id() ? 'Você' : 'Integrante da campanha' }}</p></div>
                            </div>
                        @empty
                            <p class="sb-camp-overview-detail mt-3">Nenhum jogador visível.</p>
                        @endforelse
                    </section>
                </aside>
            </div>
        </div>
    </main>
</x-app-layout>
