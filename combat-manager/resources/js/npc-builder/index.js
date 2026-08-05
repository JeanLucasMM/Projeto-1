import { createNpcState } from './state';
import utils from './utils';
import combat from './combat';
import entries from './entries';
import attacks from './attacks';

function mergeModules(...modules) {
    const target = {};

    for (const mod of modules) {
        if (!mod) continue;

        for (const key of Reflect.ownKeys(mod)) {
            Object.defineProperty(
                target,
                key,
                Object.getOwnPropertyDescriptor(mod, key)
            );
        }
    }

    return target;
}

export default function npcBuilder(initialData, dictionaries = {}) {
    const state = createNpcState(dictionaries);
    const builder = mergeModules(state, utils, entries, attacks, combat);

    builder.init = function init() {
        if (initialData) {
            this.header = { ...this.header, ...initialData.header };
            this.combat = { ...this.combat, ...initialData.combat };
            this.speed = { ...this.speed, ...initialData.speed };

            if (initialData.abilities) {
                this.abilities = { ...this.abilities, ...initialData.abilities };
            }

            this.savingThrows = initialData.savingThrows ?? {};
            this.skills = initialData.skills ?? [];
            this.spellcasting = initialData.spellcasting ?? {};
            this.sections = initialData.sections ?? [];

            this.features = this.normalizeEntryCollection(initialData.features ?? []);
            this.actions = this.normalizeEntryCollection(initialData.actions ?? []);
            this.bonusActions = this.normalizeEntryCollection(initialData.bonusActions ?? []);
            this.reactions = this.normalizeEntryCollection(initialData.reactions ?? []);
            this.legendaryActions = this.normalizeEntryCollection(initialData.legendaryActions ?? []);
            this.mythicActions = this.normalizeEntryCollection(initialData.mythicActions ?? []);
            this.attacks = this.normalizeAttackCollection(initialData.attacks ?? []);
        }

        this.header.size = this.normalizeDictionaryValue(this.dictionaries.sizes, this.header.size) || 'medium';
        this.header.challengeRating = String(this.header.challengeRating ?? '0');

        const headerTypesSource = initialData?.header?.types ?? initialData?.header?.type ?? this.header.types;
        const headerAlignmentsSource = initialData?.header?.alignments ?? initialData?.header?.alignment ?? this.header.alignments;
        const headerLanguagesSource = initialData?.header?.languages ?? this.header.languages;

        this.header.types = this.normalizeMultiValue(this.dictionaries.types, headerTypesSource, ['humanoid']);
        this.header.alignments = this.normalizeMultiValue(this.dictionaries.alignments, headerAlignmentsSource, ['true_neutral']);
        this.header.languages = this.normalizeMultiValue(this.dictionaries.languages, headerLanguagesSource, []);

        this.combat.hit_dice_count = Number(this.combat.hit_dice_count || 1);
        this.combat.hit_die = this.combat.hit_die || 'd8';
        this.combat.hp_mod_extra = Number(this.combat.hp_mod_extra || 0);
        this.combat.ac_base = Number(this.combat.ac_base ?? this.combat.ac ?? 10);
        this.combat.ac_bonus = Number(this.combat.ac_bonus || 0);

        this.combat.senses = this.normalizeSenseMap(this.combat.senses);
        this.combat.customSenses = this.normalizeCustomSenseList(this.combat.customSenses);
        this.combat.languages = this.normalizeMultiValue(this.dictionaries.languages, this.combat.languages, []);
        this.combat.resistances = this.normalizeMultiValue(this.dictionaries.damageTypes ?? {}, this.combat.resistances, []);
        this.combat.immunities = this.normalizeMultiValue(this.dictionaries.damageTypes ?? {}, this.combat.immunities, []);
        this.combat.conditionImmunities = this.normalizeMultiValue(this.dictionaries.conditions ?? {}, this.combat.conditionImmunities, []);
        this.combat.vulnerabilities = this.normalizeMultiValue(this.dictionaries.damageTypes ?? {}, this.combat.vulnerabilities, []);

        this.speed.walk = Number(this.speed.walk || 30);
        this.speed.climb = Number(this.speed.climb || 0);
        this.speed.swim = Number(this.speed.swim || 0);
        this.speed.burrow = Number(this.speed.burrow || 0);
        this.speed.fly = Number(this.speed.fly || 0);
        this.speed.jumpHorizontalBonus = Number(this.speed.jumpHorizontalBonus || 0);
        this.speed.jumpVerticalBonus = Number(this.speed.jumpVerticalBonus || 0);
        this.speed.hover = Boolean(this.speed.hover);
        this.speed.hasJumps = Boolean(this.speed.hasJumps);

        this.features = this.normalizeEntryCollection(this.features);
        this.actions = this.normalizeEntryCollection(this.actions);
        this.bonusActions = this.normalizeEntryCollection(this.bonusActions);
        this.reactions = this.normalizeEntryCollection(this.reactions);
        this.legendaryActions = this.normalizeEntryCollection(this.legendaryActions);
        this.mythicActions = this.normalizeEntryCollection(this.mythicActions);
        this.attacks = this.normalizeAttackCollection(this.attacks);

        this.$nextTick(() => {
            this.initEditors();
            this.initAttackEditors();
        });

        if (
            !initialData ||
            initialData.combat?.hit_dice_count === undefined ||
            initialData.combat?.hit_die === undefined
        ) {
            this.updateSize();
        }
    };

    return builder;
}