<?php

namespace App\ViewModels;

class TrackerViewModel
{
    public function __construct(
        public string $type,
        public string $resourceKey,
        public int $current,
        public int $max,
        public ?string $label = null,
    ) {}
}