import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

export default {
    createTracker() {
        return {
            enabled: false,
            title: '',
            uses: 0,
            min: 4,
            max: 6,
            reset: '',
            customReset: '',
        };
    },

    normalizeTracker(tracker = {}) {
    const uses = Number(
        tracker.uses ??
        tracker.max ??
        0
    );

    const min = Number(
        tracker.min ??
        tracker.rechargeMin ??
        0
    );

    const max = Number(
        tracker.max ??
        tracker.rechargeMax ??
        uses
    );

    return {
        enabled: Boolean(tracker.enabled ?? false),
        title: tracker.title ?? tracker.name ?? '',
        uses: Number(tracker.uses ?? 0),
        min: Number(tracker.min ?? 4),
        max: Number(tracker.max ?? 6),
        reset: tracker.reset ?? '',
        customReset: tracker.customReset ?? '',
    };
},
createEntry(overrides = {}) {
    return {
        id: this.makeId(),

        title: '',
        content: '',

        type: 'normal',

        legendary: {
            enabled: false,
            totalActions: 3,
            intro:'possui Três (3) Ações Lendárias e pode usar uma delas ao final do turno de outra criatura.',
        },

        lair: {
            enabled: false,
            intro: 'No valor de iniciativa 20 (perdendo empates de iniciativa), a criatura executa uma ação de covil para gerar um dos seguintes efeitos:',
        },

        tracker: this.createTracker(),

        spellcasting: {
            enabled: false,

            // nível de conjurador
            casterLevel: 1,

            // atributo usado
            ability: 'cha',

            // bônus manual extra
            attackBonusExtra: 0,

            // bônus manual extra de CD
            saveDCExtra: 0,

            slots: [],
        },

        editor: null,

        ...overrides,
    };
},

normalizeEntry(entry = {}) {
    return {

        id: entry.id ?? this.makeId(),

        title:
            entry.title ??
            entry.name ??
            '',

        content:
            entry.content ??
            entry.description ??
            entry.text ??
            '',


        type:
            entry.type ??
            'normal',

        legendary: {
            enabled: Boolean(entry.legendary?.enabled ?? false),

        totalActions: Number(
            entry.legendary?.totalActions ?? 3),

        intro:
            entry.legendary?.intro ??
            'possui Três (3) Ações Lendárias e pode usar uma delas ao final do turno de outra criatura.',
            },


        lair: {
            enabled: Boolean(entry.lair?.enabled ?? false),
            intro: entry.lair?.intro ??
                'No valor de iniciativa 20 (perdendo empates de iniciativa), a criatura executa uma ação de covil para gerar um dos seguintes efeitos:',
        },

        tracker:
            this.normalizeTracker(entry.tracker ?? {}),


        spellcasting: {

            enabled:
                Boolean(entry.spellcasting?.enabled ?? false),


            casterLevel:
                Number(
                    entry.spellcasting?.casterLevel ?? 1
                ),


            ability:
                entry.spellcasting?.ability ?? 'cha',


            attackBonusExtra:
                Number(
                    entry.spellcasting?.attackBonusExtra ?? 0
                ),


            saveDCExtra:
                Number(
                    entry.spellcasting?.saveDCExtra ?? 0
                ),


            slots:

                Array.isArray(entry.spellcasting?.slots)

                ?

                entry.spellcasting.slots.map(slot => ({

                    id:
                        slot.id ??
                        this.makeId(),


                    level:
                        Number(slot.level ?? 0),


                    uses:
                        Number(slot.uses ?? 0),


                    reset:
                        slot.reset ??
                        'day',


                    customReset:
                        slot.customReset ??
                        '',


                    spells:
                        slot.spells ??
                        '',

                }))

                :

                [],

        },


        editor:null,

    };
},

    normalizeEntryCollection(list = []) {
        if (!Array.isArray(list)) {
            return [];
        }

        return list.map(item => this.normalizeEntry(item));
    },

    initEditors(collections = this.entryCollections) {
        const targets = Array.isArray(collections) ? collections : [collections];

        targets.forEach(collection => {
            const items = this[collection];

            if (!Array.isArray(items)) {
                return;
            }

            items.forEach(item => {
                if (item.editor) {
                    return;
                }

                const element = document.getElementById(`${collection}-editor-${item.id}`);

                if (!element) {
                    return;
                }

                item.editor = new Editor({
                    element,
                    extensions: [StarterKit],
                    content: item.content ?? '',
                    onUpdate: ({ editor }) => {
                        item.content = editor.getHTML();
                    },
                });
            });
        });
    },

    destroyEntryEditor(entry) {
        if (entry?.editor) {
            entry.editor.destroy();
            entry.editor = null;
        }
    },

    addEntry(collection) {
        if (!Array.isArray(this[collection])) {
            this[collection] = [];
        }

        const entry = this.createEntry();
        this[collection].push(entry);

        this.$nextTick(() => {
            this.initEditors(collection);
        });
    },

    addLairAction() {
    this.addEntry('lairActions');
},

removeLairAction(index) {
    this.removeEntry('lairActions', index);
},

moveLairAction(from, to) {
    this.moveEntry('lairActions', from, to);
},

    removeEntry(collection, index) {
        const item = this[collection]?.[index];

        if (!item) {
            return;
        }

        this.destroyEntryEditor(item);
        this[collection].splice(index, 1);
    },

    moveEntry(collection, from, to) {
        if (from === to) {
            return;
        }

        const list = this[collection];

        if (!Array.isArray(list)) {
            return;
        }

        const item = list.splice(from, 1)[0];

        if (!item) {
            return;
        }

        list.splice(to, 0, item);
    },

createSpellSlot() {
    return {

        id:this.makeId(),

        // 0 = Truques
        level:0,

        // 0 = À vontade
        uses:0,

        reset:'day',

        customReset:'',

        spells:'',

    };
},

getSpellcastingAbility(entry) {

    const ability =
        entry.spellcasting.ability;


    const values = {
        str:'Força',
        dex:'Destreza',
        con:'Constituição',
        int:'Inteligência',
        wis:'Sabedoria',
        cha:'Carisma',
    };


    return values[ability] ?? ability;
},

getAbilityModifier(value) {

    return Math.floor((value - 10) / 2);

},

getSpellAttack(entry) {

    const proficiency =
        this.proficiencyBonus;


    const ability =
        this.getAbilityModifier(
            this.abilities[
                entry.spellcasting.ability
            ]
        );


    return (
        proficiency +
        ability +
        entry.spellcasting.attackBonusExtra
    );

},
getSpellDC(entry) {

    const proficiency =
        this.proficiencyBonus;


    const ability =
        this.getAbilityModifier(
            this.abilities[
                entry.spellcasting.ability
            ]
        );


    return (
        8 +
        proficiency +
        ability +
        entry.spellcasting.saveDCExtra
    );

},
getSpellcastingDescription(entry){

    return `${this.header.name} é um conjurador de ${entry.spellcasting.casterLevel}º Nível, usando ${this.getSpellcastingAbility(entry)} como canalização. (+${this.getSpellAttack(entry)} Acerto, CD ${this.getSpellDC(entry)})`;

},

getLegendaryIntro(entry) {

    const nome =
        this.header?.name || 'A criatura';

    const quantidade =
        Number(entry.legendary.totalActions || 3);

    const extenso =
        this.numberToWords(quantidade);

    const texto =
        entry.legendary.intro ||
        'e pode usar uma delas ao final do turno de outra criatura.';

    return `${nome} possui ${extenso} (${quantidade}) Ações Lendárias ${texto}`;

},

numberToWords(value) {

    const words = {
        1:'Um',
        2:'Dois',
        3:'Três',
        4:'Quatro',
        5:'Cinco',
        6:'Seis',
        7:'Sete',
        8:'Oito',
        9:'Nove',
        10:'Dez',
    };

    return words[value] ?? value;

},

spellSlotLabel(slot){

    if(slot.level === 0){
        return 'Truques';
    }


    const uses =
        slot.uses === 0
        ? 'À vontade'
        : `${slot.uses}/Dia`;


    return `${slot.level}º Nível (${uses})`;

},

createMultiAttack() {
    return {

        id: this.makeId(),

        title: 'Multiataque',

        mode: 'automatic',

        entries: [],

        customText: '',

    };
},
createMultiAttackEntry() {
    return {

        id: this.makeId(),

        source: 'attack', // attack | action

        sourceId: '',

        quantity: 1,

    };
},
normalizeMultiAttack(entry = {}) {

    return {

        id:
            entry.id ??
            this.makeId(),

        title:
            entry.title ??
            'Multiataque',

        mode:
            entry.mode ??
            'automatic',

        customText:
            entry.customText ??
            '',

        entries:
            Array.isArray(entry.entries)
                ? entry.entries.map(item => ({
                    id: item.id ?? this.makeId(),
                    source:
    item.source ??
    (
        this.attacks.some(
            attack => String(attack.id) === String(item.sourceId)
        )
            ? 'attack'
            : 'action'
    ),
                    sourceId: item.sourceId ?? '',
                    quantity: Number(item.quantity ?? 1),
                }))
                : [],

    };

},
addMultiAttack() {

    this.multiAttacks.push(
        this.createMultiAttack()
    );

},

removeMultiAttack(index) {

    this.multiAttacks.splice(index, 1);

},

addMultiAttackEntry(multiAttack) {

    multiAttack.entries.push(
        this.createMultiAttackEntry()
    );

},

removeMultiAttackEntry(multiAttack, index) {

    multiAttack.entries.splice(index, 1);

},
getMultiAttackOptions() {

    return [

        ...this.attacks.map(attack => ({
            id: attack.id,
            type: 'attack',
            label: attack.name,
        })),

        ...this.actions.map(action => ({
            id: action.id,
            type: 'action',
            label: action.title,
        })),

    ];

},
getMultiAttackText(multiAttack) {

    if (!multiAttack?.entries?.length) {
        return '';
    }

    return multiAttack.entries
        .map(entry => {

            if (entry.source === 'attack') {

                const attack = this.attacks.find(
                    item => String(item.id) === String(entry.sourceId)
                );

                return `${attack?.name ?? 'Ataque sem nome'} (${entry.quantity})`;
            }


            if (entry.source === 'action') {

                const action = this.actions.find(
                    item => String(item.id) === String(entry.sourceId)
                );

                return `${action?.title ?? 'Ação sem nome'} (${entry.quantity})`;
            }


            return 'Sem nome';

        })
        .join(', ');

},

getMultiAttackDescription(multiAttack) {
        if (multiAttack.mode === 'custom') {
            return multiAttack.customText || '';
        }

        if (!multiAttack.entries?.length) {
            return '';
        }

        const numeros = {
            1: 'um',
            2: 'dois',
            3: 'três',
            4: 'quatro',
            5: 'cinco',
            6: 'seis',
            7: 'sete',
            8: 'oito',
            9: 'nove',
            10: 'dez'
        };

        const partes = multiAttack.entries.map((entry) => {
            if (entry.source === 'attack') {
                const attack = this.attacks.find(
                    a => String(a.id) === String(entry.sourceId)
                );
                // CORRIGIDO: Verifica tanto 'title' quanto 'name' para evitar "Ataque sem nome"
                const nome = attack?.title || attack?.name || 'Ataque sem nome';
                
                if (entry.quantity <= 1) {
                    return `um ataque usando ${nome}`;
                }

                const extenso = numeros[entry.quantity] ?? entry.quantity;
                return `${extenso} (${entry.quantity}) ataques usando ${nome}`;
            }

            if (entry.source === 'action') {
                const action = this.actions.find(
                    a => String(a.id) === String(entry.sourceId)
                );
                // CORRIGIDO: Verifica tanto 'title' quanto 'name' para evitar "Ação sem nome"
                const nome = action?.title || action?.name || 'Ação sem nome';
                
                if (entry.quantity <= 1) {
                    return `a ação ${nome}`;
                }

                const extenso = numeros[entry.quantity] ?? entry.quantity;
                return `${extenso} (${entry.quantity}) utilizações da ação ${nome}`;
            }

            return 'entrada desconhecida';
        });

        const nomeNpc = this.header?.name || 'A criatura';

        return `${nomeNpc} realiza ${partes.join(' e ')}.`;
    },

addSpellSlot(entry) {
    entry.spellcasting.slots.push(
        this.createSpellSlot()
    );
},

removeSpellSlot(entry, index) {
    entry.spellcasting.slots.splice(index, 1);
},

moveSpellSlot(entry, from, to) {
    if (from === to) return;

    const slot = entry.spellcasting.slots.splice(from, 1)[0];

    if (!slot) return;

    entry.spellcasting.slots.splice(to, 0, slot);
},

    addFeature() {
        this.addEntry('features');
    },

    removeFeature(index) {
        this.removeEntry('features', index);
    },

    moveFeature(from, to) {
        this.moveEntry('features', from, to);
    },

    addAction() {
        this.addEntry('actions');
    },

    removeAction(index) {
        this.removeEntry('actions', index);
    },

    moveAction(from, to) {
        this.moveEntry('actions', from, to);
    },

    addBonusAction() {
        this.addEntry('bonusActions');
    },

    removeBonusAction(index) {
        this.removeEntry('bonusActions', index);
    },

    moveBonusAction(from, to) {
        this.moveEntry('bonusActions', from, to);
    },

    addReaction() {
        this.addEntry('reactions');
    },

    removeReaction(index) {
        this.removeEntry('reactions', index);
    },

    moveReaction(from, to) {
        this.moveEntry('reactions', from, to);
    },

    addLegendaryAction() {
        this.addEntry('legendaryActions');
    },

    removeLegendaryAction(index) {
        this.removeEntry('legendaryActions', index);
    },

    moveLegendaryAction(from, to) {
        this.moveEntry('legendaryActions', from, to);
    },

    addMythicAction() {
        this.addEntry('mythicActions');
    },

    removeMythicAction(index) {
        this.removeEntry('mythicActions', index);
    },

    moveMythicAction(from, to) {
        this.moveEntry('mythicActions', from, to);
    },
};