<?php

namespace App\DTOs;

class SectionData
{
    /**
     * @param FeatureData[] $items
     */
    public function __construct(
        public string $title,
        public array $items
    ) {}
}