/* Spellbound V66.0.83 — histórico local de última página.
   Não faz POST, não troca de modo e não redireciona automaticamente. */
(() => {
  'use strict';
  const allowed = {
    master: new Set([
      'combats.index', 'combats.show', 'campaigns.index', 'campaigns.show',
      'npcs.index', 'npcs.show', 'folders.show', 'npc-builder.index',
      'master.vault.items.index'
    ]),
    player: new Set([
      'characters.index', 'characters.show', 'campaigns.index', 'campaigns.show'
    ])
  };
  const labels = {
    'combats.index': 'Combates', 'combats.show': 'Último combate',
    'campaigns.index': 'Campanhas', 'campaigns.show': 'Última campanha',
    'npcs.index': 'Cofre de NPCs', 'npcs.show': 'Último NPC',
    'folders.show': 'Pasta de NPCs', 'npc-builder.index': 'Construtor de NPCs',
    'master.vault.items.index': 'Cofre de itens',
    'characters.index': 'Personagens', 'characters.show': 'Última ficha'
  };
  const safeId = id => typeof id === 'string' && /^\d{1,18}$/.test(id);
  const key = (id,mode) => `sb:recent:v1:${id}:${mode}`;
  const safePath = p => {
    if (typeof p !== 'string' || p.length < 2 || p.length > 900 || !p.startsWith('/') || p.startsWith('//') || /[\\\x00-\x1f\x7f]/.test(p)) return false;
    try {
      const parsed = new URL(p, location.origin);
      return parsed.origin === location.origin && parsed.protocol === location.protocol && !parsed.username && !parsed.password;
    } catch (_) { return false; }
  };
  const get = (id, mode) => {
    try {
      const item = JSON.parse(localStorage.getItem(key(id,mode)) || 'null');
      if (!item || !allowed[mode]?.has(item.route) || !safePath(item.path)) return null;
      return item;
    } catch (_) { return null; }
  };
  const saveVisit = aside => {
    const { sbUser, sbMode, sbRoute } = {
      sbUser: aside.getAttribute('data-sb-user'),
      sbMode: aside.getAttribute('data-sb-mode'),
      sbRoute: aside.getAttribute('data-sb-route')
    };
    if (!safeId(sbUser) || !allowed[sbMode]?.has(sbRoute)) return;
    // Exclui query strings e âncoras que podem conter dados temporários.
    const path = location.pathname;
    if (!safePath(path)) return;
    try { localStorage.setItem(key(sbUser,sbMode), JSON.stringify({ route:sbRoute, path, savedAt:Date.now() })); }
    catch (_) { /* storage desativado: navegação continua normalmente */ }
  };
  const render = hub => {
    const id = hub.getAttribute('data-spellbound-user');
    const mode = hub.getAttribute('data-spellbound-mode');
    const link = hub.querySelector('[data-spellbound-resume]');
    if (!link || !safeId(id) || !allowed[mode]) return;
    const item = get(id,mode);
    if (!item) return;
    const name = hub.querySelector('[data-spellbound-resume-label]');
    if (name) name.textContent = labels[item.route] || 'Última página';
    link.href = item.path;
    link.hidden = false;
    link.classList.add('is-ready');
  };
  const init = () => {
    const aside = document.querySelector('[data-sb-user][data-sb-mode][data-sb-route]');
    if (aside) saveVisit(aside);
    const hub = document.querySelector('[data-spellbound-hub]');
    if (hub) render(hub);
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, {once:true});
  else init();
})();
