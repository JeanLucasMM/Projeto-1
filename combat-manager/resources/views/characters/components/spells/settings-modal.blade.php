<template x-teleport="body">
    <div x-show="settingsOpen" x-cloak
         class="character-spellbook-v4 sb-modal-backdrop fixed inset-0 z-[130] flex items-center justify-center p-2 backdrop-blur-sm sm:p-4"
         :style="spellbookThemeStyle"
         @click.self="closeSettings()">
        <div class="sb-modal-shell flex max-h-[96vh] w-full max-w-6xl flex-col overflow-hidden rounded-[22px] border">
            <header class="sb-modal-header flex shrink-0 items-center justify-between gap-4 border-b px-5 py-4 sm:px-6">
                <div>
                    <p class="text-[8px] font-black uppercase tracking-[0.18em] text-[#d9c0a4]">Grimório</p>
                    <h3 class="mt-1 font-serif text-2xl font-black">Configuração de conjuração</h3>
                    <p class="mt-1 text-[10px] text-[#ead9c5]">Use as classes da ficha como ponto de partida e ajuste somente o que for diferente na sua mesa.</p>
                </div>
                <button type="button" @click="closeSettings()"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-xl hover:bg-white/20" aria-label="Fechar">×</button>
            </header>

            <div class="grid min-h-0 flex-1 lg:grid-cols-[310px_minmax(0,1fr)]">
                {{-- PERFIS --}}
                <aside class="min-h-0 overflow-y-auto border-r border-[#b89d80]/45 bg-[#eadbc6] p-4">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#76523f]">Conjuradores</p>
                        <button type="button" @click="newProfile()" class="rounded-lg bg-[#6b1d14] px-3 py-2 text-[8px] font-black uppercase text-white">+ Novo</button>
                    </div>

                    <button x-show="characterContext.spellcasting_suggestions.length > 0" type="button" @click="importProfilesFromSheet()" :disabled="busy"
                            class="mt-3 w-full rounded-xl border border-[#8c6046]/30 bg-[#fff8ea] px-3 py-3 text-left transition hover:bg-white disabled:opacity-50">
                        <span class="block text-[9px] font-black text-[#53150f]">Usar classes da ficha</span>
                        <span class="mt-1 block text-[8px] leading-4 text-[#725b49]">Cria automaticamente os perfis reconhecidos e suas listas de magia.</span>
                    </button>

                    <div class="mt-3 space-y-2">
                        <template x-for="profile in profiles" :key="profile.id">
                            <button type="button" @click="editProfile(profile)"
                                    class="w-full rounded-xl border px-3 py-3 text-left transition"
                                    :class="Number(profileEditingId) === Number(profile.id) ? 'border-[#6b1d14]/40 bg-[#fff8ea] shadow-sm' : 'border-transparent bg-white/55 hover:border-[#b59573]/45 hover:bg-white/80'">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <strong class="block truncate text-[11px] font-black text-[#381712]" x-text="profile.class"></strong>
                                        <p class="mt-1 truncate text-[9px] text-[#79604c]">Nv. <span x-text="profile.caster_level"></span> · <span x-text="casterTypeLabel(profile.caster_type)"></span></p>
                                    </div>
                                    <span class="rounded-lg bg-[#6b1d14]/8 px-2 py-1 text-[9px] font-black text-[#6b1d14]" x-text="`CD ${profile.spell_save_dc}`"></span>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <template x-for="code in profile.spell_list_codes || []" :key="code">
                                        <span class="rounded-full bg-[#eadbc8] px-2 py-0.5 text-[7px] font-black text-[#694838]"
                                              x-text="catalogMeta.lists.find(row => row.code === code)?.label || code"></span>
                                    </template>
                                </div>
                            </button>
                        </template>

                        <div x-show="profiles.length === 0" class="rounded-xl border border-dashed border-[#aa8867]/45 bg-white/45 px-3 py-6 text-center text-[10px] leading-5 text-[#705747]">
                            Nenhum conjurador configurado.
                        </div>
                    </div>
                </aside>

                {{-- EDITOR --}}
                <main class="min-h-0 overflow-y-auto bg-[#f8f0e4] p-4 sm:p-6">
                    <section class="rounded-2xl border border-[#b99d7e]/45 bg-[#fffaf2] p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#8a654a]" x-text="profileEditingId === null ? 'Novo perfil' : 'Editar perfil'"></p>
                                <h4 class="mt-1 font-serif text-xl font-black text-[#53150f]">Perfil de conjuração</h4>
                                <p class="mt-1 max-w-2xl text-[10px] leading-5 text-[#715847]">Nível e classe podem acompanhar a ficha automaticamente. O jogador continua livre para mudar progressão, atributo e bônus mágicos.</p>
                            </div>

                            <button x-show="profileEditingId !== null" type="button"
                                    @click="deleteProfile(profiles.find(row => Number(row.id) === Number(profileEditingId)))" :disabled="busy"
                                    class="rounded-lg px-3 py-2 text-[8px] font-black uppercase text-red-700 hover:bg-red-50 disabled:opacity-40">Remover perfil</button>
                        </div>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <label class="sm:col-span-2 lg:col-span-1">
                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Classe da ficha</span>
                                <select :value="profileForm.character_class_id" @change="applyCharacterClassToProfile($event.target.value)"
                                        class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <option value="">Perfil livre / homebrew</option>
                                    <template x-for="row in characterContext.classes" :key="row.id">
                                        <option :value="row.id" x-text="`${row.class}${row.subclass ? ' · ' + row.subclass : ''} · Nv. ${row.level}`"></option>
                                    </template>
                                </select>
                            </label>

                            <label>
                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Nome</span>
                                <input type="text" x-model="profileForm.class" maxlength="80" placeholder="Ex.: Mago"
                                       :disabled="profileForm.sync_with_character_class"
                                       class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-semibold text-[#2f2019] disabled:bg-[#eee4d7]">
                            </label>

                            <label>
                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Nível conjurador</span>
                                <input type="number" min="1" max="20" x-model.number="profileForm.caster_level" :disabled="profileForm.sync_with_character_class"
                                       class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019] disabled:bg-[#eee4d7]">
                            </label>

                            <label>
                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Atributo de conjuração</span>
                                <select x-model="profileForm.ability" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <option value="intelligence">Inteligência</option><option value="wisdom">Sabedoria</option><option value="charisma">Carisma</option>
                                    <option value="strength">Força</option><option value="dexterity">Destreza</option><option value="constitution">Constituição</option>
                                </select>
                            </label>

                            <label>
                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Progressão</span>
                                <select x-model="profileForm.caster_type" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <option value="full">Conjurador completo</option><option value="half">Meio-conjurador</option><option value="half_up">Meio-conjurador 2024</option>
                                    <option value="third">1/3 conjurador</option><option value="pact">Pact Magic</option><option value="custom">Personalizado</option>
                                </select>
                            </label>

                            <label>
                                <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Gestão de slots</span>
                                <select x-model="profileForm.slot_mode" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <option value="automatic">Automática</option><option value="manual">Manual</option>
                                </select>
                            </label>
                        </div>

                        {{-- LISTAS --}}
                        <div class="mt-5 border-t border-[#dfcfbb] pt-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Listas de feitiços</span>
                                    <p class="mt-1 text-[9px] leading-4 text-[#806956]">Um perfil pode consultar mais de uma lista. Quando a classe é reconhecida, a lista é marcada automaticamente.</p>
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <template x-for="list in catalogMeta.lists" :key="list.code">
                                    <label class="cursor-pointer">
                                        <input type="checkbox" class="peer sr-only" :value="list.code" x-model="profileForm.spell_list_codes">
                                        <span class="inline-flex rounded-full border border-[#b99b79]/45 bg-white px-3 py-2 text-[9px] font-black text-[#644636] transition peer-checked:border-[#6b1d14]/30 peer-checked:bg-[#6b1d14] peer-checked:text-white" x-text="list.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        {{-- LIMITES DA FOLHA --}}
                        <div class="mt-5 rounded-2xl border border-[#c4aa89]/55 bg-[#fbf4e8] p-4">
                            <h5 class="font-serif text-[16px] font-black text-[#53150f]">Limites do Grimório</h5>
                            <p class="mt-1 text-[9px] leading-4 text-[#735a47]">Opcional. O Spellbound apenas contabiliza e avisa; nunca bloqueia sua folha. Truques não entram nesses limites.</p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <label>
                                    <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Feitiços registrados</span>
                                    <input type="number" min="0" max="999" x-model="profileForm.spell_limit" placeholder="Sem limite" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <span class="mt-1 block text-[8px] text-[#806956]">Conta somente feitiços de 1º círculo ou superior.</span>
                                </label>
                                <label>
                                    <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Feitiços preparados</span>
                                    <input type="number" min="0" max="999" x-model="profileForm.prepared_limit" placeholder="Sem limite" class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <span class="mt-1 block text-[8px] text-[#806956]">Truques são ignorados. Ultrapassar gera aviso, não trava.</span>
                                </label>
                            </div>
                        </div>

                        {{-- BÔNUS MÁGICOS --}}
                        <div class="mt-5 rounded-2xl border border-[#c4aa89]/55 bg-[#f5e7d4] p-4">
                            <h5 class="font-serif text-[16px] font-black text-[#53150f]">Bônus mágicos</h5>
                            <p class="mt-1 text-[9px] leading-4 text-[#735a47]">A CD e o ataque já são calculados com proficiência + atributo. Preencha somente bônus extras concedidos por item, feature ou regra da mesa.</p>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <label>
                                    <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Bônus mágico de CD</span>
                                    <input type="number" min="-20" max="20" x-model.number="profileForm.spell_save_dc_bonus" placeholder="0"
                                           class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <span class="mt-1 block text-[8px] text-[#806956]">Ex.: +1 para um item que aumente sua CD em 1.</span>
                                </label>
                                <label>
                                    <span class="block text-[8px] font-black uppercase tracking-[0.1em] text-[#795741]">Bônus de ataque mágico</span>
                                    <input type="number" min="-20" max="20" x-model.number="profileForm.spell_attack_bonus_extra" placeholder="0"
                                           class="mt-1 w-full rounded-xl border-[#c4a887] bg-white px-3 py-2.5 text-[11px] font-bold text-[#2f2019]">
                                    <span class="mt-1 block text-[8px] text-[#806956]">Ex.: +2 para um foco que dê +2 nos ataques mágicos.</span>
                                </label>
                            </div>
                        </div>

                        <label class="mt-4 flex items-center gap-3 rounded-xl border border-[#c4a887]/60 bg-white px-3 py-3">
                            <input type="checkbox" x-model="profileForm.is_active" class="rounded border-[#9c795d] text-[#6b1d14] focus:ring-[#6b1d14]">
                            <span><strong class="block text-[10px] text-[#421911]">Perfil ativo</strong><span class="block text-[8px] text-[#846b57]">Participa do cálculo de slots e aparece no Grimório.</span></span>
                        </label>

                        <div class="mt-5 flex justify-end">
                            <button type="button" @click="saveProfile()" :disabled="busy"
                                    class="rounded-xl bg-[#6b1d14] px-5 py-3 text-[9px] font-black uppercase tracking-[0.1em] text-white hover:bg-[#7c2a20] disabled:opacity-50"
                                    x-text="busy ? 'Salvando...' : (profileEditingId === null ? 'Criar perfil' : 'Salvar perfil')"></button>
                        </div>
                    </section>

                    {{-- SLOTS --}}
                    <section class="mt-5 overflow-hidden rounded-2xl border border-[#b99d7e]/45 bg-[#fffaf2]">
                        <header class="border-b border-[#ccb69a]/50 bg-[#eadbc6] px-5 py-4">
                            <p class="text-[8px] font-black uppercase tracking-[0.14em] text-[#81604a]">Recursos</p>
                            <h4 class="mt-1 font-serif text-xl font-black text-[#53150f]">Slots de magia</h4>
                            <p class="mt-1 text-[9px] leading-4 text-[#705746]">O máximo é calculado automaticamente. Você também pode definir um máximo manual; na tela principal, clique diretamente nos quadrados para gastar ou restaurar slots.</p>
                        </header>

                        <div x-show="settingsSlots().length === 0" class="px-5 py-9 text-center text-[10px] text-[#765e4c]">Nenhum slot configurável para os perfis atuais.</div>

                        <div x-show="settingsSlots().length > 0" class="divide-y divide-[#dfcfbb]">
                            <template x-for="slot in settingsSlots()" :key="slot.id">
                                <div class="grid items-center gap-3 px-4 py-3 sm:grid-cols-[minmax(130px,1fr)_80px_100px_100px_80px]">
                                    <div><strong class="block text-[10px] text-[#451a13]" x-text="slotLabel(slot)"></strong><span class="mt-0.5 block text-[8px] text-[#846b57]" x-text="slotRecoveryLabel(slot.recovery)"></span></div>
                                    <div><span class="block text-[7px] font-black uppercase text-[#8c6a50]">Calculado</span><strong class="mt-1 block text-[11px] text-[#53150f]" x-text="slot.calculated_maximum ?? 0"></strong></div>
                                    <label><span class="block text-[7px] font-black uppercase text-[#8c6a50]">Máximo</span><input type="number" min="0" max="99" x-model="slot.manual_maximum_input" placeholder="Auto" class="mt-1 w-full rounded-lg border-[#c3a686] bg-white px-2 py-2 text-[10px] font-bold text-[#38251d]"></label>
                                    <label><span class="block text-[7px] font-black uppercase text-[#8c6a50]">Atual</span><input type="number" min="0" max="99" x-model="slot.current_input" class="mt-1 w-full rounded-lg border-[#c3a686] bg-white px-2 py-2 text-[10px] font-bold text-[#38251d]"></label>
                                    <button type="button" @click="saveSlot(slot)" :disabled="busy" class="self-end rounded-lg border border-[#8c684c]/30 bg-[#fff7e6] px-3 py-2.5 text-[8px] font-black uppercase text-[#6b1d14] hover:bg-white disabled:opacity-40">Salvar</button>
                                </div>
                            </template>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>
</template>
