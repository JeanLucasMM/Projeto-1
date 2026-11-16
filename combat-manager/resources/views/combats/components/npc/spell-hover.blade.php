{{--
    Uso: incluir UMA vez dentro do npc-card.blade.php, depois de npc-sheet /
    npc-native-sheet (os dois formatos são cobertos sem modificar seus parsers).
--}}
@php
    $spellHoverCombat = $combat ?? ($combatNpc->combat ?? null);
    $spellHoverCampaignId = $spellHoverCombat?->campaign_id ?? null;
    $npcHoverSpells = app(\App\Services\Spells\NpcSpellTooltipService::class)
        ->forNpc($combatNpc, $spellHoverCampaignId !== null ? (int) $spellHoverCampaignId : null);
@endphp

@if(count($npcHoverSpells) > 0)
    <script type="application/json" data-npc-spell-tooltip-payload="{{ $combatNpc->id }}">@json($npcHoverSpells)</script>
@endif

@once
    <style>
        .sb-npc-spell-name {
            color: var(--theme-accent-strong, #681f18);
            text-decoration: underline;
            text-decoration-color: color-mix(in srgb, currentColor 30%, transparent);
            text-underline-offset: 3px;
            text-decoration-thickness: 1px;
            cursor: help;
            border-radius: 2px;
            transition: color .15s ease, text-decoration-color .15s ease;
        }
        .sb-npc-spell-name:hover,
        .sb-npc-spell-name:focus-visible {
            color: var(--theme-accent, #a04b31);
            text-decoration-color: currentColor;
            outline-offset: 3px;
        }
        .sb-npc-spell-tooltip {
            position: fixed;
            z-index: 2147483000;
            width: min(375px, calc(100vw - 24px));
            max-height: min(570px, calc(100dvh - 24px));
            overflow-x: hidden;
            overflow-y: auto;
            padding: 0;
            border: 1px solid var(--theme-border, #cdb89d);
            border-radius: 12px;
            background: var(--theme-paper-strong, #fcf8ef);
            color: var(--theme-text-primary, #3f2b22);
            box-shadow: 0 18px 48px rgba(34, 18, 12, .26), 0 3px 10px rgba(34, 18, 12, .09);
            font-size: 12px;
            line-height: 1.5;
            opacity: 0;
            transform: translateY(4px);
            pointer-events: none;
            transition: opacity .13s ease, transform .13s ease;
            overscroll-behavior: contain;
        }
        .sb-npc-spell-tooltip.sb-npc-spell-tooltip-visible {
            opacity: 1;
            transform: none;
            pointer-events: auto;
        }
        .sb-npc-spell-tooltip-header {
            position: relative;
            padding: 15px 17px 12px;
            border-bottom: 1px solid var(--theme-border, #dec9b2);
            background: color-mix(in srgb, var(--theme-accent-soft, #eadbca) 65%, var(--theme-paper-strong, #fcf8ef));
        }
        .sb-npc-spell-tooltip-title {
            margin: 0;
            color: var(--theme-text-strong, #53150f);
            font-family: Georgia, 'Times New Roman', serif;
            font-variant: small-caps;
            font-weight: 750;
            font-size: 21px;
            line-height: 1.12;
            overflow-wrap: anywhere;
        }
        .sb-npc-spell-tooltip-subtitle {
            margin: 6px 0 0;
            color: var(--theme-text-secondary, #78624e);
            font: italic 11px/1.5 Georgia, serif;
        }
        .sb-npc-spell-tooltip-source {
            margin-top: 8px;
            font: 800 9px/1.2 ui-sans-serif, system-ui, sans-serif;
            color: var(--theme-accent-strong, #7b3527);
            letter-spacing: .06em;
        }
        .sb-npc-spell-tooltip-body { padding: 12px 17px 16px; }
        .sb-npc-spell-tooltip-facts { display: grid; gap: 3px; margin-bottom: 12px; }
        .sb-npc-spell-tooltip-fact { overflow-wrap: anywhere; }
        .sb-npc-spell-tooltip-fact strong {
            color: var(--theme-text-strong, #4c2217);
            font-family: Georgia, serif;
            font-weight: 750;
        }
        .sb-npc-spell-tooltip-description {
            color: var(--theme-text-primary, #3b2920);
            font: 12px/1.52 Georgia, 'Times New Roman', serif;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }
        .sb-npc-spell-tooltip-higher {
            border-top: 1px solid var(--theme-border, #dfcbb2);
            margin-top: 12px;
            padding-top: 10px;
            white-space: pre-line;
            font: 11.5px/1.48 Georgia, serif;
        }
        .sb-npc-spell-tooltip-higher strong {
            display: block;
            margin-bottom: 3px;
            color: var(--theme-text-strong, #53150f);
        }
        @media (prefers-reduced-motion: reduce) {
            .sb-npc-spell-tooltip, .sb-npc-spell-name { transition: none; }
        }
    </style>

    <script>
        (() => {
            if (window.__spellboundNpcSpellHoverV63) return;
            window.__spellboundNpcSpellHoverV63 = true;

            const tokenPayload = new WeakMap();
            const skipped = new Set(['SCRIPT', 'STYLE', 'TEXTAREA', 'BUTTON', 'INPUT', 'SELECT', 'OPTION', 'A']);
            const normalize = (value) => String(value ?? '')
                .replace(/[’]/g, "'")
                .replace(/\s+/g, ' ')
                .trim()
                .toLocaleLowerCase();

            const escapeRegex = (value) => value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            let tooltip = null;
            let active = null;
            let hideTimer = null;

            function makeText(tag, className, text) {
                const element = document.createElement(tag);
                if (className) element.className = className;
                element.textContent = String(text ?? '');
                return element;
            }

            function buildTooltip(spell) {
                const card = document.createElement('section');
                card.className = 'sb-npc-spell-tooltip';
                card.setAttribute('role', 'tooltip');
                card.setAttribute('aria-label', `Feitiço: ${spell.name}`);

                const header = makeText('header', 'sb-npc-spell-tooltip-header', '');
                header.append(makeText('h4', 'sb-npc-spell-tooltip-title', spell.name));

                const level = Number(spell.level) || 0;
                const school = spell.school ? ` (${spell.school})` : '';
                header.append(makeText('p', 'sb-npc-spell-tooltip-subtitle',
                    `${level === 0 ? 'Truque' : level + 'º círculo'}${school}${spell.ritual ? ' · Ritual' : ''}`));
                if (spell.source) header.append(makeText('div', 'sb-npc-spell-tooltip-source', spell.source));
                card.append(header);

                const body = makeText('div', 'sb-npc-spell-tooltip-body', '');
                const facts = makeText('div', 'sb-npc-spell-tooltip-facts', '');
                [
                    ['Conjuração', spell.casting_time],
                    ['Alcance', spell.range],
                    ['Componentes', spell.components],
                    ['Duração', `${spell.concentration ? 'Concentração, ' : ''}${spell.duration ?? ''}`],
                ].forEach(([label, value]) => {
                    if (!String(value ?? '').trim()) return;
                    const line = makeText('div', 'sb-npc-spell-tooltip-fact', '');
                    line.append(makeText('strong', '', `${label}: `));
                    line.append(document.createTextNode(String(value)));
                    facts.append(line);
                });
                body.append(facts);

                if (spell.description) {
                    body.append(makeText('div', 'sb-npc-spell-tooltip-description', spell.description));
                }
                if (spell.higher_level) {
                    const higher = makeText('div', 'sb-npc-spell-tooltip-higher', '');
                    higher.append(makeText('strong', '', 'Em níveis superiores'));
                    higher.append(document.createTextNode(spell.higher_level));
                    body.append(higher);
                }
                card.append(body);
                return card;
            }

            function placeTooltip(anchor) {
                if (!tooltip || !anchor) return;
                const rect = anchor.getBoundingClientRect();
                const cardRect = tooltip.getBoundingClientRect();
                const margin = 12;
                let left = rect.right + 12;
                if (left + cardRect.width > window.innerWidth - margin) left = rect.left - cardRect.width - 12;
                left = Math.max(margin, Math.min(left, window.innerWidth - cardRect.width - margin));

                let top = rect.top - 12;
                if (top + cardRect.height > window.innerHeight - margin) top = window.innerHeight - cardRect.height - margin;
                top = Math.max(margin, top);
                tooltip.style.left = `${Math.round(left)}px`;
                tooltip.style.top = `${Math.round(top)}px`;
            }

            function hide() {
                if (hideTimer) clearTimeout(hideTimer);
                hideTimer = null;
                active = null;
                if (tooltip) { tooltip.remove(); tooltip = null; }
            }
            function scheduleHide() {
                if (hideTimer) clearTimeout(hideTimer);
                hideTimer = window.setTimeout(hide, 200);
            }
            function show(anchor) {
                if (hideTimer) clearTimeout(hideTimer);
                hideTimer = null;
                const spell = tokenPayload.get(anchor);
                if (!spell) return;
                if (anchor === active) return;

                hide();
                active = anchor;
                tooltip = buildTooltip(spell);
                const themeHost = anchor.closest('[data-theme]');
                if (themeHost) {
                    tooltip.setAttribute('data-theme', themeHost.getAttribute('data-theme'));
                    tooltip.classList.add('character-sheet-theme');
                }
                tooltip.addEventListener('pointerenter', () => {
                    if (hideTimer) clearTimeout(hideTimer);
                    hideTimer = null;
                });
                tooltip.addEventListener('pointerleave', scheduleHide);
                document.body.append(tooltip);
                placeTooltip(anchor);
                requestAnimationFrame(() => tooltip?.classList.add('sb-npc-spell-tooltip-visible'));
            }

            function decorateTextNode(node, spells, pattern) {
                const value = node.nodeValue;
                if (!value?.trim()) return;
                pattern.lastIndex = 0;
                if (!pattern.test(value)) return;
                pattern.lastIndex = 0;

                const fragment = document.createDocumentFragment();
                let last = 0;
                let match;
                while ((match = pattern.exec(value)) !== null) {
                    const prefix = match[1] ?? '';
                    const name = match[2] ?? '';
                    const start = match.index + prefix.length;
                    const end = start + name.length;
                    const spell = spells.get(normalize(name));
                    if (!spell) continue;

                    if (start > last) fragment.append(document.createTextNode(value.slice(last, start)));
                    const element = makeText('span', 'sb-npc-spell-name', name);
                    element.tabIndex = 0;
                    element.setAttribute('aria-label', `${name}. Passe o mouse ou pressione Enter para ver os detalhes.`);
                    tokenPayload.set(element, spell);
                    fragment.append(element);
                    last = end;
                }
                if (last === 0) return;
                if (last < value.length) fragment.append(document.createTextNode(value.slice(last)));
                node.replaceWith(fragment);
            }

            function mount(script) {
                if (script.dataset.npcSpellMounted === 'yes') return;
                script.dataset.npcSpellMounted = 'yes';
                let list;
                try { list = JSON.parse(script.textContent || '[]'); }
                catch { return; }
                if (!Array.isArray(list) || !list.length) return;

                const root = script.closest('[x-data]');
                if (!root) return;
                // O nome usado pelo mestre pode diferir do catálogo:
                // FireBolt → Fire Bolt, ou pequeno erro de digitação reconhecido.
                const map = new Map();
                const names = [];
                list.forEach(spell => {
                    const variants = [spell.name, ...(Array.isArray(spell.mentions) ? spell.mentions : [])];
                    variants.forEach(name => {
                        const normalized = normalize(name);
                        if (!normalized || map.has(normalized)) return;
                        map.set(normalized, spell);
                        names.push(name);
                    });
                });
                names.sort((a, b) => b.length - a.length);
                const escapedNames = names.map(escapeRegex);
                const pattern = new RegExp('(^|[^\\p{L}\\p{N}])(' + escapedNames.join('|') + ')(?![\\p{L}\\p{N}])', 'giu');

                root.querySelectorAll('.content-flow').forEach(section => {
                    const walker = document.createTreeWalker(section, NodeFilter.SHOW_TEXT, {
                        acceptNode(node) {
                            let parent = node.parentElement;
                            while (parent && parent !== section) {
                                if (skipped.has(parent.tagName)
                                    || parent.classList.contains('sb-npc-spell-name')
                                    || parent.hasAttribute('contenteditable')
                                    || parent.hasAttribute('x-text')) {
                                    return NodeFilter.FILTER_REJECT;
                                }
                                parent = parent.parentElement;
                            }
                            return NodeFilter.FILTER_ACCEPT;
                        },
                    });
                    const nodes = [];
                    while (walker.nextNode()) nodes.push(walker.currentNode);
                    nodes.forEach(node => decorateTextNode(node, map, pattern));
                });
            }

            function scan() {
                document.querySelectorAll('script[data-npc-spell-tooltip-payload]').forEach(mount);
            }
            document.addEventListener('pointerover', event => {
                const anchor = event.target.closest?.('.sb-npc-spell-name');
                if (anchor) show(anchor);
            });
            document.addEventListener('pointerout', event => {
                const anchor = event.target.closest?.('.sb-npc-spell-name');
                if (anchor && !anchor.contains(event.relatedTarget)) scheduleHide();
            });
            document.addEventListener('focusin', event => {
                const anchor = event.target.closest?.('.sb-npc-spell-name');
                if (anchor) show(anchor);
            });
            document.addEventListener('focusout', event => {
                const anchor = event.target.closest?.('.sb-npc-spell-name');
                if (anchor) scheduleHide();
            });
            document.addEventListener('click', event => {
                const anchor = event.target.closest?.('.sb-npc-spell-name');
                if (anchor) { event.preventDefault(); show(anchor); return; }
                if (tooltip && !tooltip.contains(event.target)) hide();
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') hide();
                if ((event.key === 'Enter' || event.key === ' ') && event.target.classList?.contains('sb-npc-spell-name')) {
                    event.preventDefault(); show(event.target);
                }
            });
            window.addEventListener('scroll', hide, true);
            window.addEventListener('resize', hide);

            let pending = false;
            const observer = new MutationObserver(mutations => {
                if (pending) return;
                const addedPayload = mutations.some(record => [...record.addedNodes].some(node =>
                    node.nodeType === 1 && (node.matches?.('script[data-npc-spell-tooltip-payload]')
                        || node.querySelector?.('script[data-npc-spell-tooltip-payload]'))));
                if (!addedPayload) return;
                pending = true;
                requestAnimationFrame(() => { pending = false; scan(); });
            });
            const observe = () => {
                scan();
                observer.observe(document.body, { subtree: true, childList: true });
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', observe, { once: true });
            } else {
                observe();
            }
        })();
    </script>
@endonce