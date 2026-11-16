// Spellbound — edição rica do Cofre. Usa as mesmas dependências Tiptap do Criador de NPCs.
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

const escapeHtml = (text) => String(text ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
}[char]));

// Contrato de leitura retrocompatível: itens antigos podem ter texto puro e **negrito**.
// Não permite atributos HTML, links, scripts, imagens ou eventos em textos salvos.
const allowed = new Set(['p', 'br', 'strong', 'em', 's', 'ul', 'ol', 'li', 'blockquote', 'h3', 'h4', 'code', 'hr']);
const blocked = new Set(['script', 'style', 'iframe', 'svg', 'math', 'object', 'embed', 'form', 'template']);
function sanitizeHtml(raw) {
    const documentFragment = new DOMParser().parseFromString(`<body>${raw}</body>`, 'text/html');
    const clean = (node) => {
        if (node.nodeType === Node.TEXT_NODE) return escapeHtml(node.textContent)
            .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
            .replace(/^\*\*([^*\n]+)$/g, '<strong>$1</strong>')
            .replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em>$2</em>');
        if (node.nodeType !== Node.ELEMENT_NODE) return '';
        const tag = node.tagName.toLowerCase();
        if (blocked.has(tag)) return '';
        if (tag === 'hr' || ((tag === 'p' || tag === 'div') && /^(?:-{3,}|\*{3,}|_{3,})$/.test(node.textContent.trim()))) return '<hr>';
        const inside = Array.from(node.childNodes).map(clean).join('');
        if (tag === 'b') return `<strong>${inside}</strong>`;
        if (tag === 'i') return `<em>${inside}</em>`;
        if (tag === 'del' || tag === 'strike') return `<s>${inside}</s>`;
        return allowed.has(tag) ? `<${tag}>${inside}</${tag}>` : inside;
    };
    return Array.from(documentFragment.body.childNodes).map(clean).join('');
}
function legacyToHtml(raw) {
    const text = String(raw ?? '').replace(/\r\n?/g, '\n');
    const formatted = escapeHtml(text)
        .replace(/\*\*([^*\n]+?)\*\*/g, '<strong>$1</strong>')
        .replace(/(^|\n)\*\*([^*\n]+)(?=\n|$)/g, '$1<strong>$2</strong>')
        .replace(/(?<!\*)\*([^*\n]+?)\*(?!\*)/g, '<em>$1</em>');
    const output = [];
    let lines = [];
    const flush = () => { if (lines.length) {output.push(`<p>${lines.join('<br>')}</p>`); lines = [];} };
    for (const line of formatted.split('\n')) {
        if (/^\s*(?:-{3,}|\*{3,}|_{3,})\s*$/.test(line)) {
            flush(); output.push('<hr>');
        } else if (!line.trim()) {
            flush();
        } else {
            lines.push(line);
        }
    }
    flush();
    return output.join('');
}
function toEditorHtml(raw) {
    const text = String(raw ?? '');
    if (!text.trim()) return '<p></p>';
    return /<(?:p|br|strong|b|em|i|ul|ol|li|blockquote|h[1-6]|s|del|hr)\b/i.test(text)
        ? sanitizeHtml(text)
        : legacyToHtml(text);
}

// Não guardar instâncias Tiptap/ProseMirror no estado reativo do Alpine.
// Devolvemos uma fachada sem referências diretas aos nós internos do editor.
function createEditor({element, content, placeholder, onUpdate, onSelectionUpdate}) {
    const editor = new Editor({
        element,
        extensions: [StarterKit.configure({ heading: { levels: [3] } })],
        content: toEditorHtml(content),
        editorProps: {
            attributes: {
                class: 'vault-v31-prosemirror',
                'aria-label': placeholder || 'Texto do item',
                spellcheck: 'true',
            },
        },
        onUpdate: ({editor: instance}) => onUpdate(instance.getHTML()),
        onSelectionUpdate,
        onFocus: onSelectionUpdate,
        onBlur: onSelectionUpdate,
    });
    const actions = {
        bold: () => editor.chain().focus().toggleBold().run(),
        italic: () => editor.chain().focus().toggleItalic().run(),
        strike: () => editor.chain().focus().toggleStrike().run(),
        heading: () => editor.chain().focus().toggleHeading({level: 3}).run(),
        bulletList: () => editor.chain().focus().toggleBulletList().run(),
        orderedList: () => editor.chain().focus().toggleOrderedList().run(),
        blockquote: () => editor.chain().focus().toggleBlockquote().run(),
        undo: () => editor.chain().focus().undo().run(),
        redo: () => editor.chain().focus().redo().run(),
        clear: () => editor.chain().focus().clearNodes().unsetAllMarks().run(),
        horizontalRule: () => editor.chain().focus().setHorizontalRule().run(),
    };
    return {
        execute: (name) => actions[name]?.(),
        isActive: (name) => name === 'heading'
            ? editor.isActive('heading', {level: 3}) : editor.isActive(name),
        getHTML: () => editor.getHTML(),
        focus: () => editor.commands.focus(),
        destroy: () => editor.destroy(),
    };
}

window.SpellboundVaultRichText = {toEditorHtml, sanitizeHtml, createEditor};
window.dispatchEvent(new Event('spellbound:vault-tiptap-ready'));
