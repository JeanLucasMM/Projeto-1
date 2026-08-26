<div class="w-full h-full">
    <div class="preview-statblock">
        
        {{-- FLUXO GERAL EM 2 COLUNAS (Envolve tudo para fluir igual ao livro) --}}
        <div class="preview-content-flow">

            {{-- Cabeçalho do NPC --}}
            <div>
                <h1 x-text="header.name || 'Nome do NPC'"></h1>
                <div class="preview-subtitle" x-text="`${sizeLabel} ${typesLabel}, ${alignmentsLabel}`"></div>
            </div>

            <hr class="preview-divider">

            {{-- Status de Combate Básicos --}}
            <div>
                <div class="preview-combat-row">
                    <strong>Classe de Armadura</strong> <span x-text="armorClass"></span> <span x-show="combat.ac_type" x-text="`(${combat.ac_type})`"></span>
                </div>
                <div class="preview-combat-row">
                    <strong>Pontos de Vida</strong> <span x-text="getCalculatedHP()"></span> <span x-text="`(${getHitDiceString()})`"></span>
                </div>
                <div class="preview-combat-row">
                    <strong>Deslocamento</strong> <span x-text="getSpeedString()"></span>
                </div>
            </div>

            <hr class="preview-divider">

            {{-- Tabela de Atributos e Salvaguardas --}}
            <div class="preview-ability-groups">
                {{-- FOR & INT --}}
                <div class="preview-ability-group">
                    <div class="preview-ability-header">
                        <div></div><div>Mod</div><div>Save</div>
                    </div>
                    <div class="preview-ability-row">
                        <div class="preview-ability-name">FOR <span x-text="abilities.str"></span></div>
                        <div x-text="formatModifier(getModifier(abilities.str))"></div>
                        <div class="font-bold" x-text="formatModifier(getSavingThrowValue('str'))"></div>
                    </div>
                    <div class="preview-ability-row">
                        <div class="preview-ability-name">INT <span x-text="abilities.int"></span></div>
                        <div x-text="formatModifier(getModifier(abilities.int))"></div>
                        <div class="font-bold" x-text="formatModifier(getSavingThrowValue('int'))"></div>
                    </div>
                </div>

                {{-- DES & SAB --}}
                <div class="preview-ability-group">
                    <div class="preview-ability-header">
                        <div></div><div>Mod</div><div>Save</div>
                    </div>
                    <div class="preview-ability-row">
                        <div class="preview-ability-name">DES <span x-text="abilities.dex"></span></div>
                        <div x-text="formatModifier(getModifier(abilities.dex))"></div>
                        <div class="font-bold" x-text="formatModifier(getSavingThrowValue('dex'))"></div>
                    </div>
                    <div class="preview-ability-row">
                        <div class="preview-ability-name">SAB <span x-text="abilities.wis"></span></div>
                        <div x-text="formatModifier(getModifier(abilities.wis))"></div>
                        <div class="font-bold" x-text="formatModifier(getSavingThrowValue('wis'))"></div>
                    </div>
                </div>

                {{-- CON & CAR --}}
                <div class="preview-ability-group">
                    <div class="preview-ability-header">
                        <div></div><div>Mod</div><div>Save</div>
                    </div>
                    <div class="preview-ability-row">
                        <div class="preview-ability-name">CON <span x-text="abilities.con"></span></div>
                        <div x-text="formatModifier(getModifier(abilities.con))"></div>
                        <div class="font-bold" x-text="formatModifier(getSavingThrowValue('con'))"></div>
                    </div>
                    <div class="preview-ability-row">
                        <div class="preview-ability-name">CAR <span x-text="abilities.cha"></span></div>
                        <div x-text="formatModifier(getModifier(abilities.cha))"></div>
                        <div class="font-bold" x-text="formatModifier(getSavingThrowValue('cha'))"></div>
                    </div>
                </div>
            </div>

            {{-- Perícias, Resistências, Imunidades, Sentidos e ND --}}
            <div>
                <div class="preview-combat-row" x-show="skills.filter(s => s.proficient).length > 0">
                    <strong>Perícias: </strong>
                    <template x-for="(skill, index) in skills.filter(s => s.proficient)" :key="skill.key">
                        <span>
                            <span x-text="skill.label"></span>
                            <span x-text="formatModifier(getSkillValue(skill))"></span><span x-show="index < skills.filter(s => s.proficient).length - 1">, </span>
                        </span>
                    </template>
                </div>

                <div class="preview-combat-row" x-show="combat.resistances && combat.resistances.length > 0">
                    <strong>Resistências a Dano: </strong>
                    <span x-text="resistancesLabel"></span>
                </div>
                <div class="preview-combat-row" x-show="combat.immunities && combat.immunities.length > 0">
                    <strong>Imunidades a Dano: </strong>
                    <span x-text="immunitiesLabel"></span>
                </div>
                <div class="preview-combat-row" x-show="combat.conditionImmunities && combat.conditionImmunities.length > 0">
                    <strong>Imunidades a Condição: </strong>
                    <span x-text="conditionImmunitiesLabel"></span>
                </div>
                <div class="preview-combat-row" x-show="combat.vulnerabilities && combat.vulnerabilities.length > 0">
                    <strong>Vulnerabilidades a Dano: </strong>
                    <span x-text="vulnerabilitiesLabel"></span>
                </div>

                <div class="preview-combat-row">
                    <strong>Sentidos: </strong>
                    <span x-text="getSensesString()"></span>
                </div>
                <div class="preview-combat-row">
                    <strong>Idiomas: </strong> <span x-text="languagesLabel || '--'"></span>
                </div>
                <div class="preview-combat-row">
                    <strong>Nível de Desafio: </strong> <span x-text="header.challengeRating"></span> <span x-text="`(Prof. +${proficiencyBonus}, ${xp} XP)`" style="color: var(--stat-muted)"></span>
                </div>
            </div>

            {{-- Habilidades (Features) --}}
            <template x-if="features && features.length > 0">
                <div>
                    <div class="preview-section-title">Habilidades</div>
                    <div class="space-y-2 mt-2">
                        <template x-for="feature in features" :key="feature.id">
                            <div class="preview-entry">
                                <b class="italic" x-text="feature.title ? feature.title + '.' : ''"></b>

                                <template x-if="feature.tracker && feature.tracker.enabled">
                                    <span>
                                        <template x-if="feature.tracker.reset === 'recharge'">
                                            <b>
                                                (Recarga <span x-text="feature.tracker.min"></span><span x-show="feature.tracker.max && feature.tracker.max !== feature.tracker.min" x-text="'-' + feature.tracker.max"></span>).
                                            </b>
                                        </template>
                                        <template x-if="feature.tracker.reset !== 'recharge'">
                                            <b x-text="`(${feature.tracker.uses || 1}/${(!feature.tracker.reset || feature.tracker.reset === 'none') ? (feature.tracker.uses || 1) : (feature.tracker.reset === 'custom' ? (feature.tracker.customReset || 'Personalizado') : trackerResetLabel(feature.tracker.reset))}).`">
                                            </b>
                                        </template>
                                    </span>
                                </template>

                                <span class="inline" x-html="cleanContent(feature.content)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Ações, Ataques e Multiataques --}}
            <template x-if="(actions && actions.length > 0) || (multiAttacks && multiAttacks.length > 0) || (attacks && attacks.length > 0)">
                <div>
                    <div class="preview-section-title">Ações</div>
                    <div class="space-y-2">
                        
                        {{-- Multiataques --}}
                        <template x-if="multiAttacks && multiAttacks.length > 0">
                            <div>
                                <template x-for="ma in multiAttacks" :key="ma.id">
                                    <div class="preview-entry">
                                        <b>
                                            <span class="italic" x-text="(ma.title || 'Multiataque') + '. '"></span>
                                            <span class="font-normal" x-html="getMultiAttackDescription(ma)"></span>
                                        </b>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Ataques Estruturados --}}
                        <template x-if="attacks && attacks.length > 0">
                            <div class="space-y-2">
                                <template x-for="attack in attacks" :key="attack.id">
                                    <div class="preview-entry" x-html="attackPreview(attack)"></div>
                                </template>
                            </div>
                        </template>

                        {{-- Ações do NPC --}}
                        <template x-if="actions && actions.length > 0">
                            <div>
                                <template x-for="action in actions" :key="action.id">
                                    <div class="preview-entry">
                                        <b class="italic" x-text="action.title ? action.title + '.' : ''"></b>

                                        <template x-if="action.tracker && action.tracker.enabled">
                                            <span>
                                                <template x-if="action.tracker.reset === 'recharge'">
                                                    <b>
                                                        (Recarga <span x-text="action.tracker.min"></span><span x-show="action.tracker.max && action.tracker.max !== action.tracker.min" x-text="'-' + action.tracker.max"></span>).
                                                    </b>
                                                </template>
                                                <template x-if="action.tracker.reset !== 'recharge'">
                                                    <b x-text="`(${action.tracker.uses || 1}/${(!action.tracker.reset || action.tracker.reset === 'none') ? (action.tracker.uses || 1) : (action.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(action.tracker.reset))}).`">
                                                    </b>
                                                </template>
                                            </span>
                                        </template>

                                        <template x-if="action.type === 'spellcasting' && action.spellcasting">
                                            <span class="font-semibold" x-text="getSpellcastingDescription(action)"></span>
                                        </template>

                                        <span x-html="cleanContent(action.content)"></span>

                                        <template x-if="action.type === 'spellcasting' && action.spellcasting && action.spellcasting.slots && action.spellcasting.slots.length">
                                            <ul class="pl-2 mt-1 space-y-0.5 block">
                                                <template x-for="slot in action.spellcasting.slots" :key="slot.id || slot.level">
                                                    <li class="text-[12px]">
                                                        <b x-text="spellSlotLabel(slot) + ': '"></b>
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

            {{-- Ações Bônus --}}
            <template x-if="bonusActions && bonusActions.length > 0">
                <div>
                    <div class="preview-section-title">Ações Bônus</div>
                    <div class="space-y-2">
                        <template x-for="action in bonusActions" :key="action.id">
                            <div class="preview-entry">
                                <b class="italic" x-text="action.title ? action.title + '.' : ''"></b>

                                <template x-if="action.tracker && action.tracker.enabled">
                                    <span>
                                        <template x-if="action.tracker.reset === 'recharge'">
                                            <b>
                                                (Recarga <span x-text="action.tracker.min"></span><span x-show="action.tracker.max && action.tracker.max !== action.tracker.min" x-text="'-' + action.tracker.max"></span>).
                                            </b>
                                        </template>
                                        <template x-if="action.tracker.reset !== 'recharge'">
                                            <b x-text="`(${action.tracker.uses || 1}/${(!action.tracker.reset || action.tracker.reset === 'none') ? (action.tracker.uses || 1) : (action.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(action.tracker.reset))}).`">
                                            </b>
                                        </template>
                                    </span>
                                </template>

                                <span x-html="cleanContent(action.content)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Reações --}}
            <template x-if="reactions && reactions.length > 0">
                <div>
                    <div class="preview-section-title">Reações</div>
                    <div class="space-y-2">
                        <template x-for="reaction in reactions" :key="reaction.id">
                            <div class="preview-entry">
                                <b class="italic" x-text="reaction.title ? reaction.title + '.' : ''"></b>

                                <template x-if="reaction.tracker && reaction.tracker.enabled">
                                    <span>
                                        <template x-if="reaction.tracker.reset === 'recharge'">
                                            <b>
                                                (Recarga <span x-text="reaction.tracker.min"></span><span x-show="reaction.tracker.max && reaction.tracker.max !== reaction.tracker.min" x-text="'-' + reaction.tracker.max"></span>).
                                            </b>
                                        </template>
                                        <template x-if="reaction.tracker.reset !== 'recharge'">
                                            <b x-text="`(${reaction.tracker.uses || 1}/${(!reaction.tracker.reset || reaction.tracker.reset === 'none') ? (reaction.tracker.uses || 1) : (reaction.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(reaction.tracker.reset))}).`">
                                            </b>
                                        </template>
                                    </span>
                                </template>

                                <span class="inline" x-html="cleanContent(reaction.content)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Ações Lendárias --}}
            <template x-if="legendaryActions && legendaryActions.length > 0">
                <div>
                    <div class="preview-section-title">Ações Lendárias</div>
                    <div class="preview-subtitle mb-2" x-text="getLegendaryIntro(legendaryActions[0])"></div>
                    <div class="space-y-2">
                        <template x-for="action in legendaryActions" :key="action.id">
                            <div class="preview-entry">
                                <b class="italic" x-text="action.title ? action.title + '.' : ''"></b>

                                <template x-if="action.cost && action.cost > 1">
                                    <b x-text="`(Custa ${action.cost} Ações).`"></b>
                                </template>

                                <template x-if="action.tracker && action.tracker.enabled">
                                    <span>
                                        <template x-if="action.tracker.reset === 'recharge'">
                                            <b>
                                                (Recarga <span x-text="action.tracker.min"></span><span x-show="action.tracker.max && action.tracker.max !== action.tracker.min" x-text="'-' + action.tracker.max"></span>).
                                            </b>
                                        </template>
                                        <template x-if="action.tracker.reset !== 'recharge'">
                                            <b x-text="`(${action.tracker.uses || 1}/${(!action.tracker.reset || action.tracker.reset === 'none') ? (action.tracker.uses || 1) : (action.tracker.reset === 'custom' ? (action.tracker.customReset || 'Personalizado') : trackerResetLabel(action.tracker.reset))}).`">
                                            </b>
                                        </template>
                                    </span>
                                </template>

                                <span class="inline" x-html="cleanContent(action.content)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Ações de Covil --}}
            <template x-if="lairActions && lairActions.length > 0">
                <div>
                    <div class="preview-section-title">Ações de Covil</div>
                    <div class="preview-subtitle mb-2" x-text="lairActions[0]?.lair?.intro || 'No valor de iniciativa 20 (perdendo empates de iniciativa), a criatura executa uma ação de covil para gerar um dos seguintes efeitos:'"></div>
                    <div class="space-y-2">
                        <template x-for="action in lairActions" :key="action.id">
                            <div class="preview-entry">
                                <b class="italic" x-text="action.title ? action.title + '.' : ''"></b>
                                <span class="inline" x-html="cleanContent(action.content)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div> {{-- Fim da preview-content-flow --}}

    </div>
</div>