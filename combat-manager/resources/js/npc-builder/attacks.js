import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

export default {
    createAttackDamage() {
        return {
            id: this.makeId(),
            count: 1,
            die: 'd6',
            type: 'slashing',
            ability: 'str',
            extra: 0,
        };
    },

    createAttackEffect() {
        return {
            id: this.makeId(),
            content: '',
            editor: null,
        };
    },

    createAttack() {
        return {
            id: this.makeId(),
            title: '',
            mode: 'builder',
            content: '',
            editor: null,
            builder: {
                targets: 'Um alvo',
                range: 'melee',
                reach: 5,
                attackAbility: 'str',
                proficiency: true,
                extraHitBonus: 0,
                attackType: 'weapon',
                damages: [
                    this.createAttackDamage(),
                ],
                effects: [],
            },
        };
    },

    normalizeAttackEffect(effect = {}) {
        if (typeof effect === 'string') {
            return {
                id: this.makeId(),
                content: effect,
                editor: null,
            };
        }

        return {
            id: effect.id ?? this.makeId(),
            content: effect.content ?? '',
            editor: null,
        };
    },

    normalizeAttack(attack = {}) {
        return {
            id: attack.id ?? this.makeId(),
            title: attack.title ?? '',
            mode: attack.mode ?? 'builder',
            content: attack.content ?? '',
            editor: null,
            builder: {
                targets: attack.builder?.targets ?? 'Um alvo',
                range: attack.builder?.range ?? 'melee',
                reach: Number(attack.builder?.reach ?? 5),
                attackAbility: attack.builder?.attackAbility ?? 'str',
                proficiency: Boolean(attack.builder?.proficiency ?? true),
                extraHitBonus: Number(attack.builder?.extraHitBonus ?? 0),
                attackType: attack.builder?.attackType ?? 'weapon',
                damages: Array.isArray(attack.builder?.damages)
                    ? attack.builder.damages.map(damage => ({
                        id: damage.id ?? this.makeId(),
                        count: Number(damage.count ?? 1),
                        die: damage.die ?? 'd6',
                        type: damage.type ?? 'slashing',
                        ability: damage.ability ?? 'str',
                        extra: Number(damage.extra ?? 0),
                    }))
                    : [this.createAttackDamage()],
                effects: Array.isArray(attack.builder?.effects)
                    ? attack.builder.effects.map(effect => this.normalizeAttackEffect(effect))
                    : [],
            },
        };
    },

    normalizeAttackCollection(list = []) {
        if (!Array.isArray(list)) {
            return [];
        }

        return list.map(attack => this.normalizeAttack(attack));
    },

    initAttackEditors() {
        if (!Array.isArray(this.attacks)) {
            return;
        }

        this.attacks.forEach(attack => {
            if (!attack) {
                return;
            }

            if (!attack.editor) {
                const element = document.getElementById(`attacks-editor-${attack.id}`);

                if (element) {
                    attack.editor = new Editor({
                        element,
                        extensions: [StarterKit],
                        content: attack.content ?? '',
                        onUpdate: ({ editor }) => {
                            attack.content = editor.getHTML();
                        },
                    });
                }
            }

            if (attack.builder && Array.isArray(attack.builder.effects)) {
                attack.builder.effects.forEach(effect => {
                    if (effect.editor) {
                        return;
                    }

                    const element = document.getElementById(`attacks-effect-${attack.id}-${effect.id}`);

                    if (!element) {
                        return;
                    }

                    effect.editor = new Editor({
                        element,
                        extensions: [StarterKit],
                        content: effect.content ?? '',
                        onUpdate: ({ editor }) => {
                            effect.content = editor.getHTML();
                        },
                    });
                });
            }
        });
    },

    destroyAttackEditor(attack) {
        if (attack?.editor) {
            attack.editor.destroy();
            attack.editor = null;
        }

        if (attack?.builder?.effects && Array.isArray(attack.builder.effects)) {
            attack.builder.effects.forEach(effect => {
                if (effect?.editor) {
                    effect.editor.destroy();
                    effect.editor = null;
                }
            });
        }
    },

    addAttack() {
        if (!Array.isArray(this.attacks)) {
            this.attacks = [];
        }

        const attack = this.createAttack();
        this.attacks.push(attack);

        this.$nextTick(() => {
            this.initAttackEditors();
        });
    },

    removeAttack(index) {
        const attack = this.attacks?.[index];

        if (!attack) {
            return;
        }

        this.destroyAttackEditor(attack);
        this.attacks.splice(index, 1);
    },

    moveAttack(from, to) {
        if (from === to) {
            return;
        }

        const list = this.attacks;

        if (!Array.isArray(list)) {
            return;
        }

        const item = list.splice(from, 1)[0];

        if (!item) {
            return;
        }

        list.splice(to, 0, item);
    },

    addAttackDamage(attack) {
        if (!attack?.builder) {
            return;
        }

        if (!Array.isArray(attack.builder.damages)) {
            attack.builder.damages = [];
        }

        attack.builder.damages.push(this.createAttackDamage());
    },

    removeAttackDamage(attack, index) {
        attack?.builder?.damages?.splice(index, 1);
    },

    addAttackEffect(attack) {
        if (!attack?.builder) {
            return;
        }

        if (!Array.isArray(attack.builder.effects)) {
            attack.builder.effects = [];
        }

        attack.builder.effects.push(this.createAttackEffect());

        this.$nextTick(() => {
            this.initAttackEditors();
        });
    },

    removeAttackEffect(attack, index) {
        const effect = attack?.builder?.effects?.[index];

        if (!effect) {
            return;
        }

        if (effect.editor) {
            effect.editor.destroy();
            effect.editor = null;
        }

        attack.builder.effects.splice(index, 1);
    },

    attackModifier(attack) {
        let total = 0;

        if (attack?.builder?.attackAbility) {
            total += this.getModifier(this.abilities[attack.builder.attackAbility]);
        }

        if (attack?.builder?.proficiency) {
            total += this.proficiencyBonus;
        }

        total += Number(attack?.builder?.extraHitBonus || 0);

        return total;
    },

    damageModifier(damage) {
        let total = 0;

        if (damage?.ability && damage.ability !== 'none') {
            total += this.getModifier(this.abilities[damage.ability]);
        }

        total += Number(damage?.extra || 0);

        return total;
    },

    renderAttackDamageString(damages) {
        if (!Array.isArray(damages) || damages.length === 0) {
            return '';
        }

        return damages.map(dmg => {
            const dieSides = parseInt(String(dmg.die).replace('d', ''), 10);
            const average = (dieSides / 2) + 0.5;
            const modifier = this.damageModifier(dmg);
            const total = Math.floor((Number(dmg.count) || 1) * average + modifier);

            const modifierText =
                modifier === 0
                    ? ''
                    : modifier > 0
                        ? ` + ${modifier}`
                        : ` - ${Math.abs(modifier)}`;

            return `
                ${total}
                (${Number(dmg.count) || 1}${dmg.die}${modifierText})
                ${this.labelFromMap(this.dictionaries.damageTypes, dmg.type, dmg.type)}`;
        }).join(', mais ');
    },

   attackPreview(attack) {
        if (!attack) {
            return '';
        }

        // Se o ataque estiver no modo Texto Livre (suporta 'text' ou 'custom')
        if (attack.mode === 'text' || attack.mode === 'custom') {
            const title = attack.title ? `<strong class="font-bold text-[#6b1d14] italic pr-1">${attack.title}.</strong>` : '';
            const rawContent = attack.content ?? '';
            
            // Usa a função cleanContent se existir, ou limpa as tags <p> manualmente
            const cleanText = typeof this.cleanContent === 'function' 
                ? this.cleanContent(rawContent) 
                : rawContent.replace(/^<p[^>]*>/, '').replace(/<\/p>$/, '');
            
            return `<span class="inline">${title}<span class="inline">${cleanText}</span></span>`;
        }

        // --- MODO ESTRUTURADO (Mantido exatamente como estava para não quebrar nada) ---
        const b = attack.builder || {};
        const attackMode = b.range === 'ranged' ? 'à Distância' : 'Corpo a Corpo';

        let attackType = '';
        switch (b.attackType) {
            case 'weapon':
                attackType = `Ataque ${attackMode} com Arma`;
                break;
            case 'spell':
                attackType = `Ataque Mágico ${attackMode}`;
                break;
            case 'feature':
                attackType = `Ataque ${attackMode} Desarmado`;
                break;
            default:
                attackType = `Ataque ${attackMode}`;
        }

        const bonusText = this.formatModifier(this.attackModifier(attack));

        const reachText = b.range === 'ranged'
            ? `${Number(b.reach) || 20}/${Number(b.reach) || 60} ft`
            : `${Number(b.reach) || 5} ft`;

        const targetsText = b.targets || 'Um alvo';
        const damageText = this.renderAttackDamageString(b.damages);

        const effects = Array.isArray(b.effects) && b.effects.length
            ? ` ${b.effects
                .map(effect => (effect?.content ?? '').replace(/<p>/gi, '').replace(/<\/p>/gi, ' '))
                .join(' ')
                .trim()}`
            : '';

        return `
            <strong>${attack.title || 'Ataque'}.</strong> ${attackType}: <strong>${bonusText}</strong> Acerto,
             ${reachText} Alcance, ${targetsText}.
            Dano: ${damageText}.${effects}
        `.replace(/\s+/g, ' ').trim();
    },
};