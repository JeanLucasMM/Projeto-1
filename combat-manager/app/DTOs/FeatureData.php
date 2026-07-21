<?php

namespace App\DTOs;

class FeatureData
{
    public function __construct(
        public string $title,
        public string $text
    ) {}
}