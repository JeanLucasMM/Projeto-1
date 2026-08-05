<?php

namespace App\Builders\Objects;

class Section
{
    public string $title;

    /** @var Feature[] */
    public array $features = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function findFeature(string $title): ?Feature
    {
        foreach ($this->features as $feature) {

            if (strcasecmp($feature->title, $title) === 0) {
                return $feature;
            }

        }

        return null;
    }

 public function feature(string $title): Feature
{
    foreach ($this->features as $feature) {

        if (strcasecmp($feature->title, $title) === 0) {
            return $feature;
        }

    }

    $feature = new Feature($title);

    $this->features[] = $feature;

    return $feature;
}

    
}