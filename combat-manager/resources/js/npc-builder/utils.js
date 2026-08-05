export default {
    makeId() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }

        return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
    },

    trackerResetLabel(reset, custom = '') {
        switch (reset) {
            case '':
                return '';

            case 'day':
                return 'Dia';

            case 'short_rest':
                return 'Descanso Curto';

            case 'long_rest':
                return 'Descanso Longo';

            case 'turn':
                return 'Turno';

            case 'custom':
                return custom || 'Especial';

            case 'recharge':
                return 'Recarga';

            default:
                return '';
        }
    },

    labelFromMap(map, value, fallback = '') {
        if (value === null || value === undefined || value === '') {
            return fallback;
        }

        return map?.[value] ?? value ?? fallback;
    },

    normalizeDictionaryValue(map, value) {
        if (value === null || value === undefined || value === '') {
            return '';
        }

        if (map && Object.prototype.hasOwnProperty.call(map, value)) {
            return value;
        }

        const found = Object.entries(map ?? {}).find(([, label]) => label === value);
        return found ? found[0] : value;
    },

    normalizeMultiValue(map, value, fallback = []) {
        if (Array.isArray(value)) {
            return value
                .map(item => this.normalizeDictionaryValue(map, item))
                .filter(Boolean);
        }

        if (typeof value === 'string') {
            const parts = value
                .split(',')
                .map(item => item.trim())
                .filter(Boolean);

            if (parts.length) {
                return parts
                    .map(item => this.normalizeDictionaryValue(map, item))
                    .filter(Boolean);
            }
        }

        return fallback;
    },

    normalizeSenseMap(value) {
        const defaults = {
            blindsight: 0,
            darkvision: 0,
            tremorsense: 0,
            truesight: 0,
        };

        if (!value || typeof value !== 'object' || Array.isArray(value)) {
            return { ...defaults };
        }

        return {
            ...defaults,
            ...Object.fromEntries(
                Object.entries(value).map(([key, val]) => [key, Number(val) || 0])
            ),
        };
    },

    normalizeCustomSenseList(value) {
        if (!Array.isArray(value)) {
            return [];
        }

        return value
            .map(item => ({
                name: item?.name ?? item?.label ?? '',
                distance: Number(item?.distance ?? item?.value ?? 0) || 0,
            }))
            .filter(item => item.name || item.distance > 0);
    },

    getModifier(score) {
        return Math.floor((Number(score) - 10) / 2);
    },

    formatModifier(mod) {
        return mod >= 0 ? `+${mod}` : `${mod}`;
    },


    cleanContent(html) {
    if (!html) return '';
    return html.replace(/^<p[^>]*>/, '').replace(/<\/p>$/, '');
},
};