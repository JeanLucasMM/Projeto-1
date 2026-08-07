{{-- COLUNA DA DIREITA: PREVIEW DA FICHA DO NPC (preview.blade.php) --}}
<div class="w-full lg:w-[25%] h-full overflow-y-auto custom-scrollbar hidden lg:block pr-1 pb-24">
    <div class="bg-[#fbf9f4] border border-[#d6a56c] shadow-[0_5px_15px_rgba(107,29,20,0.08)] p-5 relative font-sans rounded-sm">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#e5c494] via-[#d6a56c] to-[#e5c494]"></div>
        <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#e5c494] via-[#d6a56c] to-[#e5c494]"></div>

        {{-- Cabeçalho do NPC --}}
        <h2 class="text-2xl font-black text-[#6b1d14] font-serif capitalize tracking-tight leading-none" x-text="header.name || 'Nome do NPC'"></h2>
        <p class="text-[11px] italic text-[#53150f] mt-1.5" x-text="`${sizeLabel} ${typesLabel}, ${alignmentsLabel}`"></p>

        <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
            <polygon points="0,0 100,0 100,10 0,10 5,5"/>
        </svg>

        {{-- Status Básicos --}}
        <div class="text-[#8a2519] space-y-0.5 text-[11px]">
            <p><strong class="font-bold text-[#6b1d14]">Classe de Armadura</strong> <span x-text="armorClass"></span> <span x-show="combat.ac_type" x-text="`(${combat.ac_type})`"></span></p>
            <p><strong class="font-bold text-[#6b1d14]">Pontos de Vida</strong> <span x-text="getCalculatedHP()"></span> <span x-text="`(${getHitDiceString()})`"></span></p>
            <p><strong class="font-bold text-[#6b1d14]">Deslocamento</strong> <span x-text="getSpeedString()"></span></p>
        </div>

        <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
            <polygon points="0,0 100,0 100,10 0,10 5,5"/>
        </svg>

        {{-- Tabela de Atributos --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 my-2 text-[10px] text-[#6b1d14]">
            <div class="flex flex-col">
                <div class="grid grid-cols-4 font-bold text-[8px] text-[#8c6239] uppercase tracking-widest mb-0.5">
                    <div></div><div></div><div class="text-center">Mod</div><div class="text-center">Save</div>
                </div>
                <div class="grid grid-cols-4 items-center py-1 bg-[#ece6d7]/70">
                    <div class="font-bold text-left pl-1.5">FOR</div>
                    <div class="text-center font-normal" x-text="abilities.str"></div>
                    <div class="text-center font-normal" x-text="formatModifier(getModifier(abilities.str))"></div>
                    <div class="text-center font-bold" x-text="formatModifier(getSavingThrowValue('str'))"></div>
                </div>
                <div class="grid grid-cols-4 items-center py-1 bg-[#e0d6c8]/40">
                    <div class="font-bold text-left pl-1.5">INT</div>
                    <div class="text-center font-normal" x-text="abilities.int"></div>
                    <div class="text-center font-normal" x-text="formatModifier(getModifier(abilities.int))"></div>
                    <div class="text-center font-bold" x-text="formatModifier(getSavingThrowValue('int'))"></div>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="grid grid-cols-4 font-bold text-[8px] text-[#8c6239] uppercase tracking-widest mb-0.5">
                    <div></div><div></div><div class="text-center">Mod</div><div class="text-center">Save</div>
                </div>
                <div class="grid grid-cols-4 items-center py-1 bg-[#ece6d7]/70">
                    <div class="font-bold text-left pl-1.5">DES</div>
                    <div class="text-center font-normal" x-text="abilities.dex"></div>
                    <div class="text-center font-normal" x-text="formatModifier(getModifier(abilities.dex))"></div>
                    <div class="text-center font-bold" x-text="formatModifier(getSavingThrowValue('dex'))"></div>
                </div>
                <div class="grid grid-cols-4 items-center py-1 bg-[#e0d6c8]/40">
                    <div class="font-bold text-left pl-1.5">SAB</div>
                    <div class="text-center font-normal" x-text="abilities.wis"></div>
                    <div class="text-center font-normal" x-text="formatModifier(getModifier(abilities.wis))"></div>
                    <div class="text-center font-bold" x-text="formatModifier(getSavingThrowValue('wis'))"></div>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="grid grid-cols-4 font-bold text-[8px] text-[#8c6239] uppercase tracking-widest mb-0.5">
                    <div></div><div></div><div class="text-center">Mod</div><div class="text-center">Save</div>
                </div>
                <div class="grid grid-cols-4 items-center py-1 bg-[#ece6d7]/70">
                    <div class="font-bold text-left pl-1.5">CON</div>
                    <div class="text-center font-normal" x-text="abilities.con"></div>
                    <div class="text-center font-normal" x-text="formatModifier(getModifier(abilities.con))"></div>
                    <div class="text-center font-bold" x-text="formatModifier(getSavingThrowValue('con'))"></div>
                </div>
                <div class="grid grid-cols-4 items-center py-1 bg-[#e0d6c8]/40">
                    <div class="font-bold text-left pl-1.5">CAR</div>
                    <div class="text-center font-normal" x-text="abilities.cha"></div>
                    <div class="text-center font-normal" x-text="formatModifier(getModifier(abilities.cha))"></div>
                    <div class="text-center font-bold" x-text="formatModifier(getSavingThrowValue('cha'))"></div>
                </div>
            </div>
        </div>

        <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
            <polygon points="0,0 100,0 100,10 0,10 5,5"/>
        </svg>

        {{-- Perícias, Resistências, Imunidades, Sentidos e ND --}}
        <div class="text-[#8a2519] space-y-0.5 text-[11px]">
            <p x-show="skills.filter(s => s.proficient).length > 0">
                <strong class="font-bold text-[#6b1d14]">Perícias: </strong>
                <template x-for="(skill, index) in skills.filter(s => s.proficient)" :key="skill.key">
                    <span>
                        <span x-text="skill.label"></span>
                        <span x-text="formatModifier(getSkillValue(skill))"></span><span x-show="index < skills.filter(s => s.proficient).length - 1">, </span>
                    </span>
                </template>
            </p>

            <p x-show="combat.resistances && combat.resistances.length > 0">
                <strong class="font-bold text-[#6b1d14]">Resistências a Dano </strong>
                <span x-text="resistancesLabel"></span>
            </p>
            <p x-show="combat.immunities && combat.immunities.length > 0">
                <strong class="font-bold text-[#6b1d14]">Imunidades a Dano </strong>
                <span x-text="immunitiesLabel"></span>
            </p>
            <p x-show="combat.conditionImmunities && combat.conditionImmunities.length > 0">
                <strong class="font-bold text-[#6b1d14]">Imunidades a Condição </strong>
                <span x-text="conditionImmunitiesLabel"></span>
            </p>
            <p x-show="combat.vulnerabilities && combat.vulnerabilities.length > 0">
                <strong class="font-bold text-[#6b1d14]">Vulnerabilidades a Dano </strong>
                <span x-text="vulnerabilitiesLabel"></span>
            </p>

            <p>
                <strong class="font-bold text-[#6b1d14]">Sentidos: </strong>
                <span x-text="getSensesString()"></span>
            </p>
            <p><strong class="font-bold text-[#6b1d14]">Idiomas</strong> <span x-text="languagesLabel || '--'"></span></p>
            <p><strong class="font-bold text-[#6b1d14]">Nível de Desafio</strong> <span x-text="header.challengeRating"></span> <span x-text="`(Prof. +${proficiencyBonus}, ${xp} XP)`" class="text-gray-600"></span></p>
        </div>

        {{-- SEÇÃO DE HABILIDADES NA FICHA --}}
        <template x-if="features && features.length > 0">
            <div>
                <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
                    <polygon points="0,0 100,0 100,10 0,10 5,5"/>
                </svg>
                <div class="text-[#8a2519] space-y-2.5">
                    <template x-for="feature in features" :key="feature.id">
                        <div class="text-[11px] leading-relaxed">
                            <strong class="font-bold text-[#6b1d14] italic pr-1" x-text="feature.title ? feature.title + '.' : ''"></strong>

                            <template x-if="feature.tracker && feature.tracker.enabled">
                                <span>
                                    <template x-if="feature.tracker.reset === 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1">
                                            (Recarga <span x-text="feature.tracker.min"></span><span x-show="feature.tracker.max && feature.tracker.max !== feature.tracker.min" x-text="'-' + feature.tracker.max"></span>).
                                        </span>
                                    </template>
                                    <template x-if="feature.tracker.reset !== 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1"
                                              x-text="`(${feature.tracker.uses || 1}/${(!feature.tracker.reset || feature.tracker.reset === 'none') ? (feature.tracker.uses || 1) : (feature.tracker.reset === 'custom' ? (feature.tracker.customReset || 'Personalizado') : trackerResetLabel(feature.tracker.reset))}).`">
                                        </span>
                                    </template>
                                </span>
                            </template>

                            <span class="inline" x-html="cleanContent(feature.content)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- AÇÕES, ATAQUES E MULTIATAQUES (Agrupados) --}}
        <template x-if="(actions && actions.length > 0) || (multiAttacks && multiAttacks.length > 0) || (attacks && attacks.length > 0)">
            <div>
                <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
                    <polygon points="0,0 100,0 100,10 0,10 5,5"/>
                </svg>
                <div class="text-[11px] font-bold text-[#6b1d14] uppercase tracking-wider mb-2">Ações</div>
                <div class="text-[#53150f] space-y-3">
                    
                    {{-- 1. Multiataques --}}
                    <template x-if="multiAttacks && multiAttacks.length > 0">
                        <div>
                            <template x-for="ma in multiAttacks" :key="ma.id">
                                <div class="text-[11px] leading-relaxed mb-2">
                                    <strong class="font-bold italic text-[#6b1d14]">
                                        <span x-text="(ma.title || 'Multiataque') + '. '"></span>
                                        <span x-html="getMultiAttackDescription(ma)"></span>
                                    </strong>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- 2. Ataques Estruturados --}}
                    <template x-if="attacks && attacks.length > 0">
                        <div class="space-y-2.5">
                            <template x-for="attack in attacks" :key="attack.id">
                                <div class="text-[11px] leading-relaxed" x-html="attackPreview(attack)"></div>
                            </template>
                        </div>
                    </template>

                    {{-- 3. Ações Normais e Conjurações --}}
                    <template x-if="actions && actions.length > 0">
                        <div>
                            <template x-for="action in actions" :key="action.id">
                                <div class="text-[11px] leading-relaxed mb-2">
                                    <strong class="font-bold text-[#6b1d14] italic pr-1" x-text="action.title ? action.title + '.' : ''"></strong>

                                    <template x-if="action.tracker && action.tracker.enabled">
                                        <span>
                                            <template x-if="action.tracker.reset === 'recharge'">
                                                <span class="font-bold text-[#6b1d14] mr-1">
                                                    (Recarga <span x-text="action.tracker.min"></span><span x-show="action.tracker.max && action.tracker.max !== action.tracker.min" x-text="'-' + action.tracker.max"></span>).
                                                </span>
                                            </template>
                                            <template x-if="action.tracker.reset !== 'recharge'">
                                                <span class="font-bold text-[#6b1d14] mr-1"
                                                      x-text="`(${action.tracker.uses || 1}/${(!action.tracker.reset || action.tracker.reset === 'none') ? (action.tracker.uses || 1) : (action.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(action.tracker.reset))}).`">
                                                </span>
                                            </template>
                                        </span>
                                    </template>

                                    {{-- Detalhes se for Conjuração de Magia --}}
                                    <template x-if="action.type === 'spellcasting' && action.spellcasting">
                                        <span class="font-semibold" x-text="getSpellcastingDescription(action)"></span>
                                    </template>

                                    {{-- Conteúdo limpo encapsulado em span inline --}}
                                    <span x-html="cleanContent(action.content)"></span>

                                    {{-- Lista de magias isolada do fluxo inline --}}
                                    <template x-if="action.type === 'spellcasting' && action.spellcasting && action.spellcasting.slots && action.spellcasting.slots.length">
                                        <ul class="pl-2 mt-1 space-y-0.5 block">
                                            <template x-for="slot in action.spellcasting.slots" :key="slot.id || slot.level">
                                                <li class="text-[10.5px]">
                                                    <span class="font-bold text-[#6b1d14]" x-text="spellSlotLabel(slot) + ': '"></span>
                                                    <span class="italic" x-text="slot.spells || 'Nenhuma magia.'"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- SEÇÃO DE AÇÕES BÔNUS NA FICHA --}}
        <template x-if="bonusActions && bonusActions.length > 0">
            <div>
                <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
                    <polygon points="0,0 100,0 100,10 0,10 5,5"/>
                </svg>
                <div class="text-[11px] font-bold text-[#6b1d14] uppercase tracking-wider mb-2">Ações Bônus</div>
                <div class="text-[#53150f] space-y-3">
                    <template x-for="action in bonusActions" :key="action.id">
                        <div class="text-[11px] leading-relaxed mb-2">
                            <strong class="font-bold text-[#6b1d14] italic pr-1" x-text="action.title ? action.title + '.' : ''"></strong>

                            <template x-if="action.tracker && action.tracker.enabled">
                                <span>
                                    <template x-if="action.tracker.reset === 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1">
                                            (Recarga <span x-text="action.tracker.min"></span><span x-show="action.tracker.max && action.tracker.max !== action.tracker.min" x-text="'-' + action.tracker.max"></span>).
                                        </span>
                                    </template>
                                    <template x-if="action.tracker.reset !== 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1"
                                              x-text="`(${action.tracker.uses || 1}/${(!action.tracker.reset || action.tracker.reset === 'none') ? (action.tracker.uses || 1) : (action.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(action.tracker.reset))}).`">
                                        </span>
                                    </template>
                                </span>
                            </template>

                            {{-- Detalhes se for Conjuração de Magia --}}
                            <template x-if="action.type === 'spellcasting' && action.spellcasting">
                                <span class="font-semibold" x-text="getSpellcastingDescription(action)"></span>
                            </template>

                            {{-- Conteúdo limpo encapsulado em span inline --}}
                            <span x-html="cleanContent(action.content)"></span>

                            {{-- Lista de magias isolada do fluxo inline --}}
                            <template x-if="action.type === 'spellcasting' && action.spellcasting && action.spellcasting.slots && action.spellcasting.slots.length">
                                <ul class="pl-2 mt-1 space-y-0.5 block">
                                    <template x-for="slot in action.spellcasting.slots" :key="slot.id || slot.level">
                                        <li class="text-[10.5px]">
                                            <span class="font-bold text-[#6b1d14]" x-text="spellSlotLabel(slot) + ': '"></span>
                                            <span class="italic" x-text="slot.spells || 'Nenhuma magia.'"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- SEÇÃO DE REAÇÕES NA FICHA --}}
        <template x-if="reactions && reactions.length > 0">
            <div>
                <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
                    <polygon points="0,0 100,0 100,10 0,10 5,5"/>
                </svg>
                <div class="text-[11px] font-bold text-[#6b1d14] uppercase tracking-wider mb-2">Reações</div>
                <div class="text-[#53150f] space-y-2.5">
                    <template x-for="reaction in reactions" :key="reaction.id">
                        <div class="text-[11px] leading-relaxed">
                            <strong class="font-bold text-[#6b1d14] italic pr-1" x-text="reaction.title ? reaction.title + '.' : ''"></strong>

                            <template x-if="reaction.tracker && reaction.tracker.enabled">
                                <span>
                                    <template x-if="reaction.tracker.reset === 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1">
                                            (Recarga <span x-text="reaction.tracker.min"></span><span x-show="reaction.tracker.max && reaction.tracker.max !== reaction.tracker.min" x-text="'-' + reaction.tracker.max"></span>).
                                        </span>
                                    </template>
                                    <template x-if="reaction.tracker.reset !== 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1"
                                              x-text="`(${reaction.tracker.uses || 1}/${(!reaction.tracker.reset || reaction.tracker.reset === 'none') ? (reaction.tracker.uses || 1) : (reaction.tracker.reset === 'custom' ? (reaction.tracker.customReset || 'Personalizado') : trackerResetLabel(reaction.tracker.reset))}).`">
                                        </span>
                                    </template>
                                </span>
                            </template>

                            <span class="inline" x-html="cleanContent(reaction.content)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- SEÇÃO DE AÇÕES LENDÁRIAS NA FICHA --}}
        <template x-if="legendaryActions && legendaryActions.length > 0">
            <div>
                <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
                    <polygon points="0,0 100,0 100,10 0,10 5,5"/>
                </svg>
                
                {{-- Título da Seção --}}
                <div class="text-[11px] font-bold text-[#6b1d14] uppercase tracking-wider mb-1">Ações Lendárias</div>
                
                {{-- Texto Introdutório Dinâmico --}}
                <div class="text-[11px] italic text-[#53150f] mb-2 leading-relaxed" x-text="getLegendaryIntro(legendaryActions[0])"></div>

                <div class="text-[#53150f] space-y-3">
                    <template x-for="action in legendaryActions" :key="action.id">
                        <div class="text-[11px] leading-relaxed mb-2">
                            <strong class="font-bold text-[#6b1d14] italic pr-1" x-text="action.title ? action.title + '.' : ''"></strong>

                            {{-- Custo da Ação Lendária (ex: (Custa 2 Ações)) --}}
                            <template x-if="action.cost && action.cost > 1">
                                <span class="font-bold text-[#6b1d14] mr-1" x-text="`(Custa ${action.cost} Ações).`"></span>
                            </template>

                            {{-- Rastreador (Tracker) --}}
                            <template x-if="action.tracker && action.tracker.enabled">
                                <span>
                                    <template x-if="action.tracker.reset === 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1">
                                            (Recarga <span x-text="action.tracker.min"></span><span x-show="action.tracker.max && action.tracker.max !== action.tracker.min" x-text="'-' + action.tracker.max"></span>).
                                        </span>
                                    </template>
                                    <template x-if="action.tracker.reset !== 'recharge'">
                                        <span class="font-bold text-[#6b1d14] mr-1"
                                              x-text="`(${action.tracker.uses || 1}/${(!action.tracker.reset || action.tracker.reset === 'none') ? (action.tracker.uses || 1) : (action.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(action.tracker.reset))}).`">
                                        </span>
                                    </template>
                                </span>
                            </template>

                            {{-- Conteúdo limpo da Ação Lendária --}}
                            <span class="inline" x-html="cleanContent(action.content)"></span>
                        </div>
                    </template>
                </div>
            </div>

        </template>

        {{-- SEÇÃO DE AÇÕES DE COVIL NA FICHA --}}
<template x-if="lairActions && lairActions.length > 0">
    <div>
        <svg class="w-full h-1 my-3 text-[#8a2519]" preserveAspectRatio="none" viewBox="0 0 100 10" fill="currentColor">
            <polygon points="0,0 100,0 100,10 0,10 5,5"/>
        </svg>
        
        <div class="text-[11px] font-bold text-[#6b1d14] uppercase tracking-wider mb-1">Ações de Covil</div>
        
        <div class="text-[11px] italic text-[#53150f] mb-2 leading-relaxed" 
             x-text="lairActions[0]?.lair?.intro || 'No valor de iniciativa 20 (perdendo empates de iniciativa), a criatura executa uma ação de covil para gerar um dos seguintes efeitos:'">
        </div>

        <div class="text-[#53150f] space-y-3">
            <template x-for="action in lairActions" :key="action.id">
                <div class="text-[11px] leading-relaxed mb-2">
                    <strong class="font-bold text-[#6b1d14] italic pr-1" x-text="action.title ? action.title + '.' : ''"></strong>
                    <span class="inline" x-html="cleanContent(action.content)"></span>
                </div>
            </template>
        </div>
    </div>
</template>

    </div>

    
</div>