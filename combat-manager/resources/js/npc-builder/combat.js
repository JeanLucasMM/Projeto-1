export default {
    addResistance() {
        const value = (this.combat.resistanceCustom || '').trim();

        if (!value) {
            return;
        }

        if (!this.combat.resistances.includes(value)) {
            this.combat.resistances.push(value);
        }

        this.combat.resistanceCustom = '';
    },

    removeResistance(index) {
        this.combat.resistances.splice(index, 1);
    },

    addImmunity() {
        const value = (this.combat.immunityCustom || '').trim();

        if (!value) {
            return;
        }

        if (!this.combat.immunities.includes(value)) {
            this.combat.immunities.push(value);
        }

        this.combat.immunityCustom = '';
    },

    removeImmunity(index) {
        this.combat.immunities.splice(index, 1);
    },

    addVulnerability() {
        const value = (this.combat.vulnerabilityCustom || '').trim();

        if (!value) {
            return;
        }

        if (!this.combat.vulnerabilities.includes(value)) {
            this.combat.vulnerabilities.push(value);
        }

        this.combat.vulnerabilityCustom = '';
    },

    removeVulnerability(index) {
        this.combat.vulnerabilities.splice(index, 1);
    },

    addConditionImmunity() {
        const value = (this.combat.conditionImmunityCustom || '').trim();

        if (!value) {
            return;
        }

        if (!this.combat.conditionImmunities.includes(value)) {
            this.combat.conditionImmunities.push(value);
        }

        this.combat.conditionImmunityCustom = '';
    },

    removeConditionImmunity(index) {
        this.combat.conditionImmunities.splice(index, 1);
    },

    addCustomSense() {
        const name = (this.customSenseName || '').trim();
        const distance = Number(this.customSenseDistance) || 0;

        if (!name || distance <= 0) {
            return;
        }

        this.combat.customSenses.push({
            name,
            distance,
        });

        this.customSenseName = '';
        this.customSenseDistance = 0;
    },

    removeCustomSense(index) {
        this.combat.customSenses.splice(index, 1);
    },

    get resistancesLabel() {
        return this.combat.resistances
            .filter(Boolean)
            .map(v => this.labelFromMap(this.dictionaries.damageTypes, v, v))
            .join(', ');
    },

    get immunitiesLabel() {
        return this.combat.immunities
            .filter(Boolean)
            .map(v => this.labelFromMap(this.dictionaries.damageTypes, v, v))
            .join(', ');
    },

    get vulnerabilitiesLabel() {
        return this.combat.vulnerabilities
            .filter(Boolean)
            .map(v => this.labelFromMap(this.dictionaries.damageTypes, v, v))
            .join(', ');
    },

    get conditionImmunitiesLabel() {
        return this.combat.conditionImmunities
            .filter(Boolean)
            .map(v => this.labelFromMap(this.dictionaries.conditions, v, v))
            .join(', ');
    },
    get sizeLabel() {
    return this.labelFromMap(
        this.dictionaries.sizes,
        this.header.size,
        this.header.size
    );
},

get typesLabel() {
    return (this.header.types || [])
        .filter(Boolean)
        .map(type => this.labelFromMap(this.dictionaries.types, type, type))
        .join(', ');
},

get alignmentsLabel() {
    return (this.header.alignments || [])
        .filter(Boolean)
        .map(alignment => this.labelFromMap(this.dictionaries.alignments, alignment, alignment))
        .join(', ');
},

get languagesLabel() {
    return (this.header.languages || [])
        .filter(Boolean)
        .map(language => this.labelFromMap(this.dictionaries.languages, language, language))
        .join(', ');
},

    get proficientSkills() {
        return this.skills.filter(skill => skill.proficient);
    },

    get horizontalJump() {
        return (
            Number(this.abilities.str) +
            (Number(this.speed.jumpHorizontalBonus) || 0)
        );
    },

    get verticalJump() {
        const base = 3 + this.getModifier(this.abilities.str);

        return Math.max(
            0,
            base + (Number(this.speed.jumpVerticalBonus) || 0)
        );
    },

    get passivePerception() {
        const skill = this.skills.find(s => s.key === 'perception') || {
            ability: 'wis',
            proficient: false,
            expertise: false,
            bonus: 0,
        };

        return 10
            + this.getSkillValue(skill)
            + (Number(this.combat.passivePerceptionBonus) || 0);
    },

    getSensesString() {
        const parts = [];
        const labels = {
            blindsight: 'Visão às cegas',
            darkvision: 'Visão no escuro',
            tremorsense: 'Tremorsense',
            truesight: 'Visão verdadeira',
        };

        Object.entries(this.combat.senses).forEach(([key, value]) => {
            const distance = Number(value) || 0;

            if (distance > 0) {
                parts.push(`${labels[key] ?? key} ${distance} ft.`);
            }
        });

        this.combat.customSenses.forEach(item => {
            const name = (item?.name || '').trim();
            const distance = Number(item?.distance) || 0;

            if (name && distance > 0) {
                parts.push(`${name} ${distance} ft.`);
            }
        });

        parts.push(`Percepção passiva ${this.passivePerception}`);

        return parts.join(', ');
    },

    getSkillValue(skill) {
        const base = this.getModifier(this.abilities[skill.ability]);

        const proficiency = skill.expertise
            ? this.proficiencyBonus * 2
            : skill.proficient
                ? this.proficiencyBonus
                : 0;

        return base + proficiency + (Number(skill.bonus) || 0);
    },

    getSpeedString() {
        const speeds = [];

        if (this.speed.walk > 0) {
            speeds.push(`${this.speed.walk} ft.`);
        }

        if (this.speed.climb > 0) {
            speeds.push(`escalada ${this.speed.climb} ft.`);
        }

        if (this.speed.swim > 0) {
            speeds.push(`natação ${this.speed.swim} ft.`);
        }

        if (this.speed.burrow > 0) {
            speeds.push(`escavação ${this.speed.burrow} ft.`);
        }

        if (this.speed.fly > 0) {
            let fly = `voo ${this.speed.fly} ft.`;

            if (this.speed.hover) {
                fly += ' (pairar)';
            }

            speeds.push(fly);
        }

        if (this.speed.hasJumps) {
            speeds.push(`salto horizontal ${this.horizontalJump} ft.`);
            speeds.push(`salto vertical ${this.verticalJump} ft.`);
        }

        return speeds.join(', ') || '0 ft.';
    },

    getSavingThrowValue(ability) {
        const save = this.savingThrows[ability] ?? { proficient: false, bonus: 0 };

        return (
            this.getModifier(this.abilities[ability]) +
            (save.proficient ? this.proficiencyBonus : 0) +
            (Number(save.bonus) || 0)
        );
    },

    get proficiencyBonus() {
        return this.challengeRatings[this.header.challengeRating]?.proficiency ?? 2;
    },

    get xp() {
        return this.challengeRatings[this.header.challengeRating]?.xp ?? 10;
    },

    get constitutionModifier() {
        return this.getModifier(this.abilities.con);
    },

    get hpModifier() {
        return this.constitutionModifier * (Number(this.combat.hit_dice_count) || 1);
    },

    get totalHpModifier() {
        return this.hpModifier + (Number(this.combat.hp_mod_extra) || 0);
    },

    get armorClass() {
        return (Number(this.combat.ac_base) || 10)
            + this.getModifier(this.abilities.dex)
            + (Number(this.combat.ac_bonus) || 0);
    },

    addHeaderLanguage() {
        const value = (this.header.languageCustom || '').trim();

        if (!value) {
            return;
        }

        if (!this.header.languages.includes(value)) {
            this.header.languages.push(value);
        }

        this.header.languageCustom = '';
    },

    removeHeaderLanguage(index) {
        this.header.languages.splice(index, 1);
    },

    updateSize() {
        const die = this.sizeHitDice[this.header.size];

        if (die) {
            this.combat.hit_die = die;
        }
    },

    getCalculatedHP() {
        if (this.combat.hp_mode === 'manual') {
            return Number(this.combat.custom_hp) || 0;
        }

        const count = Number(this.combat.hit_dice_count) || 1;
        const extra = Number(this.combat.hp_mod_extra) || 0;

        const averages = {
            d4: 2.5,
            d6: 3.5,
            d8: 4.5,
            d10: 5.5,
            d12: 6.5,
            d20: 10.5,
        };

        const avg = averages[this.combat.hit_die] ?? 4.5;

        return Math.max(
            1,
            Math.floor((avg * count) + this.hpModifier + extra)
        );
    },

    getHitDiceString() {
        const count = Number(this.combat.hit_dice_count) || 1;
        const die = this.combat.hit_die || 'd8';
        const extra = Number(this.combat.hp_mod_extra) || 0;

        const parts = [`${count}${die}`];

        if (this.hpModifier !== 0) {
            parts.push(`${this.hpModifier > 0 ? '+' : '-'}${Math.abs(this.hpModifier)}`);
        }

        if (extra !== 0) {
            parts.push(`${extra > 0 ? '+' : '-'}${Math.abs(extra)}`);
        }

        return parts.join('');
    },
};