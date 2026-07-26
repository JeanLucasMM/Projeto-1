<?php

namespace App\Services\Interpreters;

use App\DTOs\FeatureData;
use App\DTOs\SectionData;
use App\Services\Calculators\StatisticCalculator;

class SectionInterpreter
{

    public function __construct(
        private StatisticCalculator $calculator
    ) {
    }


    public function interpret(array $json): array
    
{

    $sections = [];

    $this->addTraits($sections, $json);

    $this->addActions($sections, $json);

    $this->addBonusActions($sections, $json);

    $this->addReactions($sections, $json);

    $this->addLegendaryActions($sections, $json);

    

    return $sections;
}
private function addTraits(array &$sections, array $json): void
{
    if (empty($json['traits'])) {
        return;
    }

    $items = [];

    foreach ($json['traits'] as $trait) {

        $items[] = new FeatureData(
            title: $trait['name'] ?? '',
            text: $trait['description'] ?? '',
        );
    }

    $sections[] = new SectionData(
        title: 'Traits',
        items: $items,
    );
}
private function addActions(array &$sections, array $json): void
{
    $items = [];

    /*
    |--------------------------------------------------------------------------
    | Multiattack
    |--------------------------------------------------------------------------
    */

    // ==========================================================
    // 1 - Texto personalizado (compatibilidade com versões antigas)
    // ==========================================================

    if (
        ($json['multiattackOptions']['useCustomRenderer'] ?? false)
        && !empty($json['multiattackOptions']['customMultiattackRenderer'])
    ) {

        $items[] = new FeatureData(
            title: '',
            text: $json['multiattackOptions']['customMultiattackRenderer']
        );

    }

    // ==========================================================
    // 2 - Multiattack automático
    // ==========================================================

    elseif (!empty($json['multiattacks'])) {

        $lookup = [];

        foreach ($json['actions'] ?? [] as $action) {
            $lookup[$action['id']] = $action['name'];
        }

        foreach ($json['attacks'] ?? [] as $attack) {
            $lookup[$attack['id']] = $attack['name'];
        }

        foreach ($json['multiattacks'] as $multiattack) {

            $actionText = [];
            $attackText = [];

            /*
             * Ações
             */

            foreach ($multiattack['actions'] ?? [] as $id) {

                if (!isset($lookup[$id])) {
                    continue;
                }

                $actionText[] = $lookup[$id];
            }

            /*
             * Ataques
             */

            $counts = [];

            foreach ($multiattack['attacks'] ?? [] as $id) {

                if (!isset($lookup[$id])) {
                    continue;
                }

                $name = $lookup[$id];

                $counts[$name] = ($counts[$name] ?? 0) + 1;
            }

            foreach ($counts as $name => $count) {

                $numero = match ($count) {
                    2 => 'dois',
                    3 => 'três',
                    4 => 'quatro',
                    5 => 'cinco',
                    6 => 'seis',
                    default => $count,
                };

                $attackText[] = $count == 1
                    ? "um ataque {$name}"
                    : "{$numero} ataques {$name}";
            }

/*
 * Monta o texto
 */

$monsterName = $json['name'] ?? 'A criatura';

$text = "<b><i>Multiataque. {$monsterName} ";

if (!empty($actionText) && !empty($attackText)) {

    $text .= "usa ";

    if (count($actionText) == 1) {
        $text .= "{$actionText[0]} uma vez, seguido de ";
    } else {
        $text .= implode(', ', $actionText);
        $text .= ", seguidas de ";
    }

    $text .= implode(' e ', $attackText);

} elseif (!empty($actionText)) {

    $text .= "usa " . implode(', ', $actionText);

} elseif (!empty($attackText)) {

    $text .= "realiza " . implode(' e ', $attackText);

}

$text .= ".</i></b>";

$items[] = new FeatureData(
    title: '',
    text: '<span class="multiattack">'.$text.'</span>'
);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Attacks
    |--------------------------------------------------------------------------
    */

    foreach ($json['attacks'] ?? [] as $attack) {

        /*
         * Ataque personalizado
         */

        if (
            ($attack['useCustomRenderer'] ?? false)
            && !empty($attack['customRenderer'])
        ) {

            $items[] = new FeatureData(
                title: '',
                text: $attack['customRenderer']
            );

            continue;
        }

        /*
         * Ataque automático
         */

        $ability = $attack['modifier']['stat'] ?? 'STR';

        $abilityScore = $json['stats'][$ability] ?? 10;

        $abilityModifier = $this->calculator->abilityModifier($abilityScore);

        $proficiency = ($attack['modifier']['proficient'] ?? false)
            ? ($json['proficiency'] ?? 2)
            : 0;

        $hitBonus = $abilityModifier + $proficiency;

        /*
         * Tipo do ataque
         */

        $kind = $attack['kind'] ?? 'WEAPON';

        $distanceType = $attack['distance'] ?? 'MELEE';

        $distance = match ([$kind, $distanceType]) {

            // Arma
            ['WEAPON', 'MELEE'] =>
                'Ataque Corpo a Corpo com Arma',

            ['WEAPON', 'RANGED'] =>
                'Ataque à Distância com Arma',

            ['WEAPON', 'BOTH'] =>
                'Ataque Corpo a Corpo ou à Distância com Arma',

            // Magia
            ['SPELL', 'MELEE'] =>
                'Ataque Mágico Corpo a Corpo',

            ['SPELL', 'RANGED'] =>
                'Ataque Mágico à Distância',

            ['SPELL', 'BOTH'] =>
                'Ataque Mágico Corpo a Corpo ou à Distância',

            default =>
                'Ataque',
        };

        /*
         * Alcance
         */

        $rangeText = match ($distanceType) {

            'MELEE' =>
                "alcance {$attack['range']['reach']} pés",

            'RANGED' =>
                "alcance {$attack['range']['standard']} pés",

            'BOTH' =>
                "alcance {$attack['range']['reach']} pés ou {$attack['range']['standard']} pés",

            default =>
                '',
        };

        /*
         * Dano
         */

        $damageDice =
            $attack['damage']['count']
            .'d'.
            $attack['damage']['dice'];

        $damageAverage = floor(
            (
                $attack['damage']['count']
                * ($attack['damage']['dice'] + 1)
            ) / 2
        ) + $abilityModifier;

        $damageType = $attack['damage']['type'] ?? '';

        /*
         * Texto final
         */

        $items[] = new FeatureData(
            title: $attack['name'],
            text:
                "{$distance}: "
                . sprintf('%+d', $hitBonus)
                . " para atingir, {$rangeText}. "
                . "Acerto: {$damageAverage} ({$damageDice}"
                . ($abilityModifier >= 0 ? '+' : '')
                . $abilityModifier
                . ") de dano {$damageType}."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Normal Actions
    |--------------------------------------------------------------------------
    */

    foreach ($json['actions'] ?? [] as $action) {

        if (($action['bonusAction'] ?? false) === true) {
            continue;
        }

        $items[] = new FeatureData(
            title: $action['name'] ?? '',
            text: $action['description'] ?? ''
        );
    }

    if (empty($items)) {
        return;
    }

    $sections[] = new SectionData(
        title: 'Actions',
        items: $items
    );
}
private function addBonusActions(array &$sections, array $json): void
{
    $items = [];

    foreach ($json['actions'] ?? [] as $action) {

        if (($action['bonusAction'] ?? false) === false) {
            continue;
        }

        $items[] = new FeatureData(
            title: $action['name'] ?? '',
            text: $action['description'] ?? ''
        );
    }

    if (empty($items)) {
        return;
    }

    $sections[] = new SectionData(
        title: 'Bonus Actions',
        items: $items
    );
}
private function addReactions(array &$sections, array $json): void
{
    if (empty($json['reactions'])) {
        return;
    }

    $items = [];

    foreach ($json['reactions'] as $reaction) {

        $items[] = new FeatureData(
            title: $reaction['name'] ?? '',
            text: $reaction['description'] ?? ''
        );
    }

    $sections[] = new SectionData(
        title: 'Reactions',
        items: $items
    );
}
private function addLegendaryActions(array &$sections, array $json): void
{
    if (empty($json['legendaryActions'])) {
        return;
    }

    $legendary = $json['legendaryActions'];

    $items = [];

    // Caso o usuário tenha escrito um texto personalizado
    if (($legendary['useCustomPreamble'] ?? false)
        && !empty($legendary['customPreamble'])) {

        $items[] = new FeatureData(
            title: '',
            text: $legendary['customPreamble']
        );
    }

    // Caso existam ações lendárias individuais
    foreach ($legendary['actions'] ?? [] as $action) {

        $items[] = new FeatureData(
            title: $action['name'] ?? '',
            text: $action['description'] ?? ''
        );
    }

    if (empty($items)) {
        return;
    }

    $sections[] = new SectionData(
        title: 'Legendary Actions',
        items: $items
    );
}
}