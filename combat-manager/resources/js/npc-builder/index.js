import { createNpcState } from './state';
import utils from './utils';
import combat from './combat';
import entries from './entries';
import attacks from './attacks';
import exportModule from './export';
import importModule from './import';

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

export default function npcBuilder(
    initialData,
    dictionaries = {},
    options = {}
) {
    const defaultState = createNpcState(dictionaries);
    const builder = mergeModules(
        defaultState,
        utils,
        entries,
        attacks,
        combat,
        exportModule,
        importModule
    );

    builder.initialData = initialData || null;
    builder.dictionaries = dictionaries || {};
    builder.draftSaveUrl = options.draftSaveUrl || null;
    builder.draftDeleteUrl = options.draftDeleteUrl || null;
    builder.draftTimer = null;
    builder.isRestoringDraft = false;
    builder.isSavingDraft = false;
    builder._hasDraftWatcher = false;

    builder.init = function init(data = null) {
        const sourceData = data !== null ? data : this.initialData;
        const freshState = createNpcState(this.dictionaries);
        const importedData = sourceData ? this.importNpc(sourceData) : {};

        this.header = { ...freshState.header, ...importedData.header };
        this.combat = { ...freshState.combat, ...importedData.combat };
        this.speed = { ...freshState.speed, ...importedData.speed };
        this.abilities = { ...freshState.abilities, ...(importedData.abilities || {}) };

        this.savingThrows = importedData.savingThrows ?? freshState.savingThrows ?? {};
        this.skills = importedData.skills ?? freshState.skills ?? [];
        this.spellcasting = importedData.spellcasting ?? freshState.spellcasting ?? {};
        this.sections = importedData.sections ?? freshState.sections ?? [];
        this.multiAttacks = importedData.multiAttacks ?? [];

        const entryKeys = [
            'features', 'actions', 'bonusActions', 'reactions',
            'legendaryActions', 'mythicActions', 'lairActions'
        ];

        for (const key of entryKeys) {
            this[key] = this.normalizeEntryCollection(importedData[key] ?? []);
        }

        this.attacks = this.normalizeAttackCollection(importedData.attacks ?? []);

        this.header.size = this.normalizeDictionaryValue(
            this.dictionaries.sizes, this.header.size
        ) || 'medium';

        this.header.challengeRating = String(this.header.challengeRating ?? '0');

        this.header.types = this.normalizeMultiValue(
            this.dictionaries.types, this.header.types, ['humanoid']
        );

        this.header.alignments = this.normalizeMultiValue(
            this.dictionaries.alignments, this.header.alignments, ['true_neutral']
        );

        this.header.languages = this.normalizeMultiValue(
            this.dictionaries.languages, this.header.languages, []
        );

        this.combat.hit_dice_count = Number(this.combat.hit_dice_count || 1);
        this.combat.hit_die = this.combat.hit_die || 'd8';
        this.combat.hp_mod_extra = Number(this.combat.hp_mod_extra || 0);
        this.combat.ac_base = Number(this.combat.ac_base ?? 10);
        this.combat.ac_bonus = Number(this.combat.ac_bonus || 0);

        this.combat.senses = this.normalizeSenseMap(this.combat.senses);
        this.combat.customSenses = this.normalizeCustomSenseList(this.combat.customSenses);

        this.combat.languages = this.normalizeMultiValue(
            this.dictionaries.languages, this.combat.languages, []
        );

        this.combat.resistances = this.normalizeMultiValue(
            this.dictionaries.damageTypes ?? {}, this.combat.resistances, []
        );

        this.combat.immunities = this.normalizeMultiValue(
            this.dictionaries.damageTypes ?? {}, this.combat.immunities, []
        );

        this.combat.conditionImmunities = this.normalizeMultiValue(
            this.dictionaries.conditions ?? {}, this.combat.conditionImmunities, []
        );

        this.combat.vulnerabilities = this.normalizeMultiValue(
            this.dictionaries.damageTypes ?? {}, this.combat.vulnerabilities, []
        );

        this.speed.walk = Number(this.speed.walk || 30);
        this.speed.climb = Number(this.speed.climb || 0);
        this.speed.swim = Number(this.speed.swim || 0);
        this.speed.burrow = Number(this.speed.burrow || 0);
        this.speed.fly = Number(this.speed.fly || 0);
        this.speed.jumpHorizontalBonus = Number(this.speed.jumpHorizontalBonus || 0);
        this.speed.jumpVerticalBonus = Number(this.speed.jumpVerticalBonus || 0);
        this.speed.hover = Boolean(this.speed.hover);
        this.speed.hasJumps = Boolean(this.speed.hasJumps);

        if (typeof this.$nextTick === 'function') {
            this.$nextTick(() => {
                this.initEditors?.();
                this.initAttackEditors?.();
            });
        }

        this.updateSize?.();

        // Configuração segura e imediata do Watcher nativo do Alpine
        this.setupDraftPersistence();
    };

    builder.load = function load(data) {
        const importedData = this.importNpc(data);
        this.init(importedData);
        this.saveDraftDebounced();
        return importedData;
    };

    builder.loadFile = async function loadFile(file) {
        const importedData = await this.importNpcFile(file);
        this.init(importedData);
        await this.saveDraft();
        return importedData;
    };

    builder.saveDraft = async function saveDraft() {
        if (this.isRestoringDraft || this.isSavingDraft || !this.draftSaveUrl) {
            return;
        }

        this.isSavingDraft = true;

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]');

            if (!csrf) {
                throw new Error('Token CSRF não encontrado.');
            }

            const npcExport = this.exportNpc();
            
            const payload = {
                json_data: {
                    format: 'npc-builder',
                    version: 1,
                    ...(typeof npcExport === 'object' ? npcExport : {})
                }
            };

            const response = await fetch(this.draftSaveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf.content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(
                    errorData.message || 'Não foi possível salvar o rascunho.'
                );
            }

            return await response.json();

        } catch (error) {
            console.error('NPC Builder: erro ao salvar rascunho.', error);
        } finally {
            this.isSavingDraft = false;
        }
    };

    builder.saveDraftDebounced = function saveDraftDebounced() {
        clearTimeout(this.draftTimer);
        this.draftTimer = setTimeout(() => {
            this.saveDraft();
        }, 800);
    };

    builder.clearDraft = async function clearDraft() {
        clearTimeout(this.draftTimer);
        this.isRestoringDraft = true;

        try {
            if (!this.draftDeleteUrl) {
                this.init(null);
                return;
            }

            const csrf = document.querySelector('meta[name="csrf-token"]');

            if (!csrf) {
                throw new Error('Token CSRF não encontrado.');
            }

            const response = await fetch(this.draftDeleteUrl, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf.content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(
                    errorData.message || 'Não foi possível limpar o rascunho.'
                );
            }

            this.init(null);

        } finally {
            if (typeof this.$nextTick === 'function') {
                this.$nextTick(() => {
                    this.isRestoringDraft = false;
                });
            } else {
                this.isRestoringDraft = false;
            }
        }
    };

    builder.setupDraftPersistence = function setupDraftPersistence() {
        if (!this.$watch || this._hasDraftWatcher) {
            return;
        }

        this._hasDraftWatcher = true;

        this.$watch(
            () => JSON.stringify(this.exportNpc()),
            () => {
                if (this.isRestoringDraft) {
                    return;
                }
                this.saveDraftDebounced();
            }
        );
    };

    builder.sendToVault = async function sendToVault(url) {
        const npc = this.exportNpc();
        const json = JSON.stringify(npc, null, 4);
        const name = (this.header?.name || 'npc')
            .trim()
            .replace(/[\\/:*?"<>|]/g, '_');

        const file = new File([json], `${name}.json`, {
            type: 'application/json'
        });

        const formData = new FormData();
        formData.append('npc_file', file);

        const imageInput = document.getElementById('npc_image');
        if (imageInput && imageInput.files && imageInput.files[0]) {
            formData.append('npc_image', imageInput.files[0]);
        }

        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (!csrf) {
            throw new Error('Token CSRF não encontrado.');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf.content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(
                errorData.message || 'Falha ao enviar a ficha para o cofre.'
            );
        }

        await this.clearDraft();
        return response;
    };

    return builder;
}