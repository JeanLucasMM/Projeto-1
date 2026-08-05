export function createNpcState(dictionaries = {}) {
    return {
        dictionaries: {
            sizes: dictionaries.sizes ?? {},
            types: dictionaries.types ?? {},
            alignments: dictionaries.alignments ?? {},
            languages: dictionaries.languages ?? {},
            senses: dictionaries.senses ?? {},
            damageTypes: dictionaries.damageTypes ?? {},
            conditions: dictionaries.conditions ?? {},
        },

        entryCollections: [
            'features',
            'actions',
            'bonusActions',
            'reactions',
            'legendaryActions',
            'mythicActions',
        ],

        openSections: ['header'],

        toggleSection(section) {
            if (this.openSections.includes(section)) {
                this.openSections = this.openSections.filter(s => s !== section);
            } else {
                this.openSections.push(section);
            }
        },

        header: {
            name: '',
            size: 'medium',
            types: ['humanoid'],
            alignments: ['true_neutral'],
            languages: [],
            languageCustom: '',
            challengeRating: '0',
        },

        combat: {
            ac_base: 10,
            ac_bonus: 0,
            ac_type: '',
            hp_mode: 'average',
            hit_dice_count: 1,
            hit_die: 'd8',
            hp_mod_extra: 0,
            custom_hp: 10,
            passivePerceptionBonus: 0,

            senses: {
                blindsight: 0,
                darkvision: 0,
                tremorsense: 0,
                truesight: 0,
            },

            customSenses: [],
            languages: [],
            resistances: [],
            immunities: [],
            conditionImmunities: [],
            vulnerabilities: [],

            resistanceCustom: '',
            immunityCustom: '',
            vulnerabilityCustom: '',
            conditionImmunityCustom: '',
        },

        speed: {
            walk: 30,
            climb: 0,
            swim: 0,
            burrow: 0,
            fly: 0,
            hover: false,
            hasJumps: false,
            jumpHorizontalBonus: 0,
            jumpVerticalBonus: 0,
        },

        abilities: {
            str: 10,
            dex: 10,
            con: 10,
            int: 10,
            wis: 10,
            cha: 10,
        },

        skills: [
            { key: 'acrobatics', label: 'Acrobacia', ability: 'dex', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'animalHandling', label: 'Adestrar Animais', ability: 'wis', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'arcana', label: 'Arcanismo', ability: 'int', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'athletics', label: 'Atletismo', ability: 'str', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'deception', label: 'Enganação', ability: 'cha', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'history', label: 'História', ability: 'int', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'insight', label: 'Intuição', ability: 'wis', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'intimidation', label: 'Intimidação', ability: 'cha', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'investigation', label: 'Investigação', ability: 'int', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'medicine', label: 'Medicina', ability: 'wis', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'nature', label: 'Natureza', ability: 'int', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'perception', label: 'Percepção', ability: 'wis', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'performance', label: 'Performance', ability: 'cha', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'initiative', label: 'Iniciativa', ability: 'dex', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'persuasion', label: 'Persuasão', ability: 'cha', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'religion', label: 'Religião', ability: 'int', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'sleightOfHand', label: 'Prestidigitação', ability: 'dex', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'stealth', label: 'Furtividade', ability: 'dex', enabled: false, proficient: false, expertise: false, bonus: 0 },
            { key: 'survival', label: 'Sobrevivência', ability: 'wis', enabled: false, proficient: false, expertise: false, bonus: 0 },
        ],

        
        sections: [],

        multiAttacks: [],

        challengeRatings: {
            '0':   { proficiency: 2, xp: 10 },
            '1/8': { proficiency: 2, xp: 25 },
            '1/4': { proficiency: 2, xp: 50 },
            '1/2': { proficiency: 2, xp: 100 },
            '1':   { proficiency: 2, xp: 200 },
            '2':   { proficiency: 2, xp: 450 },
            '3':   { proficiency: 2, xp: 700 },
            '4':   { proficiency: 2, xp: 1100 },
            '5':   { proficiency: 3, xp: 1800 },
            '6':   { proficiency: 3, xp: 2300 },
            '7':   { proficiency: 3, xp: 2900 },
            '8':   { proficiency: 3, xp: 3900 },
            '9':   { proficiency: 4, xp: 5000 },
            '10':  { proficiency: 4, xp: 5900 },
            '11':  { proficiency: 4, xp: 7200 },
            '12':  { proficiency: 4, xp: 8400 },
            '13':  { proficiency: 5, xp: 10000 },
            '14':  { proficiency: 5, xp: 11500 },
            '15':  { proficiency: 5, xp: 13000 },
            '16':  { proficiency: 5, xp: 15000 },
            '17':  { proficiency: 6, xp: 18000 },
            '18':  { proficiency: 6, xp: 20000 },
            '19':  { proficiency: 6, xp: 22000 },
            '20':  { proficiency: 6, xp: 25000 },
            '21':  { proficiency: 7, xp: 33000 },
            '22':  { proficiency: 7, xp: 41000 },
            '23':  { proficiency: 7, xp: 50000 },
            '24':  { proficiency: 7, xp: 62000 },
            '25':  { proficiency: 8, xp: 75000 },
            '26':  { proficiency: 8, xp: 90000 },
            '27':  { proficiency: 8, xp: 105000 },
            '28':  { proficiency: 8, xp: 120000 },
            '29':  { proficiency: 9, xp: 135000 },
            '30':  { proficiency: 9, xp: 155000 },
        },

        sizeHitDice: {
            tiny: 'd4',
            small: 'd6',
            medium: 'd8',
            large: 'd10',
            huge: 'd12',
            gargantuan: 'd20',
        },

        attacks: [],
        features: [],
        actions: [],
        bonusActions: [],
        reactions: [],
        legendaryActions: [],
        mythicActions: [],

        dragFeatureIndex: null,

        savingThrows: {
            str: { enabled: false, proficient: false, bonus: 0 },
            dex: { enabled: false, proficient: false, bonus: 0 },
            con: { enabled: false, proficient: false, bonus: 0 },
            int: { enabled: false, proficient: false, bonus: 0 },
            wis: { enabled: false, proficient: false, bonus: 0 },
            cha: { enabled: false, proficient: false, bonus: 0 },
        },

        selectedSense: '',
        customSenseName: '',
        customSenseDistance: 0,
    };
}