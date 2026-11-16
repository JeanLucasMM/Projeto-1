<?php

namespace App\Services\Spells;

class Dnd2024RulesGlossary
{
    public function entries(): array
    {
        return [
            'blinded' => [
                'label' => 'Blinded',
                'kind' => 'Condição',
                'description' => 'Você não enxerga e falha automaticamente em testes de habilidade que dependem da visão. Ataques contra você têm vantagem, e os seus ataques têm desvantagem.',
            ],

            'charmed' => [
                'label' => 'Charmed',
                'kind' => 'Condição',
                'description' => 'Você não pode atacar quem o enfeitiçou nem escolhê-lo como alvo de habilidades ou efeitos mágicos que causem dano. O encantador tem vantagem em testes de habilidade para interagir socialmente com você.',
            ],

            'deafened' => [
                'label' => 'Deafened',
                'kind' => 'Condição',
                'description' => 'Você não consegue ouvir e falha automaticamente em testes de habilidade que dependem da audição.',
            ],

            'exhaustion' => [
                'label' => 'Exhaustion',
                'kind' => 'Condição',
                'description' => 'É cumulativa. Cada nível reduz seus testes de d20 em 2 e sua Velocidade em 5 pés. No nível 6, a criatura morre. Um descanso longo remove 1 nível.',
            ],

            'frightened' => [
                'label' => 'Frightened',
                'kind' => 'Condição',
                'description' => 'Enquanto a fonte do medo estiver na sua linha de visão, você tem desvantagem em testes de habilidade e jogadas de ataque. Você também não pode se aproximar voluntariamente da fonte do medo.',
            ],

            'grappled' => [
                'label' => 'Grappled',
                'kind' => 'Condição',
                'description' => 'Sua Velocidade se torna 0. Você tem desvantagem em ataques contra alvos que não sejam o agarrador. O agarrador pode arrastar ou carregar você com custo extra de movimento.',
            ],

            'incapacitated' => [
                'label' => 'Incapacitated',
                'kind' => 'Condição',
                'description' => 'Você não pode realizar Ações, Ações Bônus ou Reações, perde a Concentração e não pode falar. Se estiver incapacitado ao rolar Iniciativa, faz a rolagem com desvantagem.',
            ],

            'invisible' => [
                'label' => 'Invisible',
                'kind' => 'Condição',
                'description' => 'Você recebe vantagem na Iniciativa e não pode ser alvo de efeitos que exigem ser visto por quem não consegue vê-lo. Seus ataques têm vantagem e ataques contra você têm desvantagem contra criaturas que não conseguem vê-lo.',
            ],

            'paralyzed' => [
                'label' => 'Paralyzed',
                'kind' => 'Condição',
                'description' => 'Você fica Incapacitado, sua Velocidade é 0, falha automaticamente em salvaguardas de Força e Destreza e ataques contra você têm vantagem. Um ataque que acerte a até 5 pés é um acerto crítico.',
            ],

            'petrified' => [
                'label' => 'Petrified',
                'kind' => 'Condição',
                'description' => 'Você se transforma em substância sólida inanimada, fica Incapacitado e com Velocidade 0, falha automaticamente em salvaguardas de Força e Destreza, ataques contra você têm vantagem, você ganha resistência a todo dano e imunidade a Poisoned.',
            ],

            'poisoned' => [
                'label' => 'Poisoned',
                'kind' => 'Condição',
                'description' => 'Você tem desvantagem em jogadas de ataque e testes de habilidade.',
            ],

            'prone' => [
                'label' => 'Prone',
                'kind' => 'Condição',
                'description' => 'Você só pode rastejar ou gastar metade da Velocidade para se levantar. Seus ataques têm desvantagem. Ataques contra você têm vantagem a até 5 pés e desvantagem de mais longe.',
            ],

            'restrained' => [
                'label' => 'Restrained',
                'kind' => 'Condição',
                'description' => 'Sua Velocidade é 0. Ataques contra você têm vantagem, seus ataques têm desvantagem e suas salvaguardas de Destreza têm desvantagem.',
            ],

            'stunned' => [
                'label' => 'Stunned',
                'kind' => 'Condição',
                'description' => 'Você fica Incapacitado, falha automaticamente em salvaguardas de Força e Destreza e ataques contra você têm vantagem.',
            ],

            'unconscious' => [
                'label' => 'Unconscious',
                'kind' => 'Condição',
                'description' => 'Você fica Incapacitado e Prone, solta o que estiver segurando, sua Velocidade é 0, falha automaticamente em salvaguardas de Força e Destreza e ataques contra você têm vantagem. Acertos a até 5 pés são críticos.',
            ],

            'concentration' => [
                'label' => 'Concentration',
                'kind' => 'Regra',
                'description' => 'Você só mantém um efeito de Concentração por vez. Começar outro encerra o anterior. Ao sofrer dano, faça uma salvaguarda de Constituição com CD 10 ou metade do dano, o que for maior, até CD 30. Incapacitated ou morte encerram a Concentração.',
            ],

            'ritual' => [
                'label' => 'Ritual',
                'kind' => 'Regra',
                'description' => 'Uma magia preparada com a tag Ritual pode ser conjurada como ritual: leva 10 minutos a mais e não consome slot. A versão ritual não pode ser conjurada em nível superior.',
            ],

            'advantage' => [
                'label' => 'Advantage',
                'kind' => 'Regra',
                'description' => 'Role dois d20 e use o resultado mais alto.',
            ],

            'disadvantage' => [
                'label' => 'Disadvantage',
                'kind' => 'Regra',
                'description' => 'Role dois d20 e use o resultado mais baixo.',
            ],

            'bloodied' => [
                'label' => 'Bloodied',
                'kind' => 'Regra',
                'description' => 'Uma criatura está Bloodied quando possui metade dos seus Pontos de Vida ou menos.',
            ],

            'difficult terrain' => [
                'label' => 'Difficult Terrain',
                'kind' => 'Regra',
                'description' => 'Cada pé de deslocamento em terreno difícil custa 1 pé adicional de movimento.',
            ],

            'bonus action' => [
                'label' => 'Bonus Action',
                'kind' => 'Regra',
                'description' => 'Você pode realizar no máximo uma Ação Bônus por turno, e apenas quando alguma regra, habilidade ou efeito disser que você possui uma disponível.',
            ],

            'reaction' => [
                'label' => 'Reaction',
                'kind' => 'Regra',
                'description' => 'Uma Reação responde a um gatilho específico. Depois de usar uma, você não pode usar outra até o início do seu próximo turno.',
            ],
        ];
    }

    public function find(
        string $term
    ): ?array {
        $key = mb_strtolower(
            trim($term),
            'UTF-8'
        );

        return $this->entries()[$key]
            ?? null;
    }
}
