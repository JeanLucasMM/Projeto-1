<?php

namespace App\Services\Interpreters;

use App\DTOs\FeatureData;
use App\DTOs\SectionData;

class SectionInterpreter
{
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
     * Multiattack
     */

    if (!empty($json['multiattackOptions']['customMultiattackRenderer'])) {

$items[] = new FeatureData(
    title: '',
    text: $json['multiattackOptions']['customMultiattackRenderer']
        ?? ''
);
    }

    /*
     * Attacks
     */

    foreach ($json['attacks'] ?? [] as $attack) {

$items[] = new FeatureData(
    title: '',
    text: $attack['customRenderer']
        ?? $attack['description']
        ?? ''
);
    }

    /*
     * Normal actions
     */

    foreach ($json['actions'] ?? [] as $action) {

        if (($action['bonusAction'] ?? false) === true) {
            continue;
        }

        $items[] = new FeatureData(
            title: $action['name'],
            text: $action['description']
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