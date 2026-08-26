export default {

importNpc(source) {
    let data = source;

    if (typeof data === 'string') {
        try {
            data = JSON.parse(data);
        } catch (error) {
            throw new Error('O arquivo não contém um JSON válido.');
        }
    }

    if (!data || typeof data !== 'object' || Array.isArray(data)) {
        throw new Error('Os dados do NPC são inválidos.');
    }

    if (data.format !== 'npc-builder') {
        throw new Error('O arquivo não é um npc-builder válido.');
    }

    const version = Number(data.version ?? 1);

    if (version !== 1) {
        throw new Error(
            'Versão do npc-builder não suportada: ' + version
        );
    }

    return data;
},

async importNpcFile(file) {
    if (!(file instanceof File)) {
        throw new Error('Nenhum arquivo foi selecionado.');
    }

    if (
        file.type &&
        file.type !== 'application/json' &&
        !file.name.toLowerCase().endsWith('.json')
    ) {
        throw new Error('Selecione um arquivo JSON.');
    }

    const text = await file.text();

    return this.importNpc(text);
},

async importNpcFromUrl(url) {
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!response.ok) {
        throw new Error(
            'Não foi possível carregar a ficha. Código: ' +
            response.status
        );
    }

    const data = await response.json();

    return this.importNpc(data);
}

};