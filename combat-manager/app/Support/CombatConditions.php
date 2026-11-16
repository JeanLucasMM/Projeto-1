<?php

namespace App\Support;

/**
 * Catálogo canônico de condições do Spellbound.
 * Esta lista descreve efeitos; modificadores mecânicos automáticos serão
 * implementados separadamente para não alterar rolagens pré-existentes.
 */
final class CombatConditions
{
    public const DEFINITIONS = [
        'blinded' => ['name' => 'Cego', 'effect' => 'Falha em testes de habilidade que exigem visão. Seus ataques têm desvantagem; ataques contra você têm vantagem.', 'symbol' => 'eye-off'],
        'charmed' => ['name' => 'Enfeitiçado', 'effect' => 'Não pode atacar nem causar dano ao encantador; ele tem vantagem em interações sociais com você.', 'symbol' => 'heart'],
        'deafened' => ['name' => 'Surdo', 'effect' => 'Não consegue ouvir e falha em testes de habilidade que exigem audição.', 'symbol' => 'ear-off'],
        'frightened' => ['name' => 'Amedrontado', 'effect' => 'Desvantagem em ataques e testes de habilidade enquanto vê a fonte do medo; não pode se aproximar dela voluntariamente.', 'symbol' => 'ghost'],
        'grappled' => ['name' => 'Agarrado', 'effect' => 'Deslocamento 0. Desvantagem em ataques contra alvos que não sejam o responsável pelo agarrão.', 'symbol' => 'hand'],
        'incapacitated' => ['name' => 'Incapacitado', 'effect' => 'Não pode realizar ações, ações bônus ou reações; perde concentração e não pode falar.', 'symbol' => 'ban'],
        'invisible' => ['name' => 'Invisível', 'effect' => 'Não pode ser visto sem meios especiais; possui os benefícios de combate da invisibilidade contra criaturas que não o enxerguem.', 'symbol' => 'scan-eye'],
        'paralyzed' => ['name' => 'Paralisado', 'effect' => 'Incapacitado e sem movimento; falha em salvaguardas de Força e Destreza. Ataques contra você têm vantagem; acertos a até 5 pés são críticos.', 'symbol' => 'zap-off'],
        'petrified' => ['name' => 'Petrificado', 'effect' => 'Transformado em substância sólida; incapacitado, imóvel e resistente a danos. Falha em salvaguardas de Força e Destreza.', 'symbol' => 'mountain'],
        'poisoned' => ['name' => 'Envenenado', 'effect' => 'Desvantagem em ataques e testes de habilidade.', 'symbol' => 'flask-conical'],
        'prone' => ['name' => 'Caído', 'effect' => 'Seu ataque tem desvantagem. Ataques contra você têm vantagem a até 5 pés e desvantagem se mais distantes; levantar custa movimento.', 'symbol' => 'arrow-down-to-line'],
        'restrained' => ['name' => 'Contido', 'effect' => 'Deslocamento 0, desvantagem em seus ataques e salvaguardas de Destreza; ataques contra você têm vantagem.', 'symbol' => 'link'],
        'stunned' => ['name' => 'Atordoado', 'effect' => 'Incapacitado; falha em salvaguardas de Força e Destreza. Ataques contra você têm vantagem.', 'symbol' => 'orbit'],
        'unconscious' => ['name' => 'Inconsciente', 'effect' => 'Incapacitado e caído, sem consciência nem movimento; falha em salvaguardas de Força e Destreza. Acertos a até 5 pés são críticos.', 'symbol' => 'moon'],
        'burning' => ['name' => 'Em chamas', 'effect' => 'No início de seu turno, consulte o mestre para receber o dano de fogo determinado para o efeito. Não é aplicado automaticamente.', 'symbol' => 'flame'],
        'exhaustion' => ['name' => 'Exaustão', 'effect' => 'Cada nível impõe −2 em testes de d20 e −5 pés de deslocamento. No nível 6, a criatura morre.', 'symbol' => 'battery-low'],
    ];

    public static function all(): array
    {
        return self::DEFINITIONS;
    }
}
