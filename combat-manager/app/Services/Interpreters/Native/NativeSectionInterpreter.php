<?php

namespace App\Services\Interpreters\Native;

use App\DTOs\FeatureData;
use App\DTOs\SectionData;


class NativeSectionInterpreter
{

    public function interpret(array $json): array
    {

        $sections = [];


        $this->addFeatures(
            $sections,
            $json
        );


        $this->addActions(
            $sections,
            $json
        );


        $this->addBonusActions(
            $sections,
            $json
        );


        $this->addReactions(
            $sections,
            $json
        );


        $this->addLegendaryActions(
            $sections,
            $json
        );


        return $sections;

    }



    private function addFeatures(
        array &$sections,
        array $json
    ): void
    {

        if (empty($json['features'])) {
            return;
        }


        $items = [];


        foreach ($json['features'] as $feature) {

            $items[] = new FeatureData(

                title: $feature['title'] ?? '',

                text: $feature['content'] ?? ''

            );

        }


        $sections[] = new SectionData(

            title: 'Traits',

            items: $items

        );

    }




    private function addActions(
        array &$sections,
        array $json
    ): void
    {

        if (empty($json['actions'])) {
            return;
        }


        $items = [];


        foreach ($json['actions'] as $action) {

            $items[] = new FeatureData(

                title: $action['title'] ?? '',

                text: $action['content'] ?? ''

            );

        }


        $sections[] = new SectionData(

            title: 'Actions',

            items: $items

        );

    }




    private function addBonusActions(
        array &$sections,
        array $json
    ): void
    {

        if (empty($json['bonusActions'])) {
            return;
        }


        $items = [];


        foreach ($json['bonusActions'] as $action) {

            $items[] = new FeatureData(

                title: $action['title'] ?? '',

                text: $action['content'] ?? ''

            );

        }


        $sections[] = new SectionData(

            title: 'Bonus Actions',

            items: $items

        );

    }





    private function addReactions(
        array &$sections,
        array $json
    ): void
    {

        if (empty($json['reactions'])) {
            return;
        }


        $items = [];


        foreach ($json['reactions'] as $reaction) {

            $items[] = new FeatureData(

                title: $reaction['title'] ?? '',

                text: $reaction['content'] ?? ''

            );

        }


        $sections[] = new SectionData(

            title: 'Reactions',

            items: $items

        );

    }




    private function addLegendaryActions(
        array &$sections,
        array $json
    ): void
    {

        if (empty($json['legendaryActions'])) {
            return;
        }


        $items = [];


        foreach ($json['legendaryActions'] as $action) {

            $items[] = new FeatureData(

                title: $action['title'] ?? '',

                text: $action['content'] ?? ''

            );

        }


        $sections[] = new SectionData(

            title: 'Legendary Actions',

            items: $items

        );

    }

}