<?php

namespace App\DTOs\Native;


class NativeHeaderData
{

    public function __construct(

        public string $name = '',

        public string $size = 'medium',

        public array $types = [],

        public array $alignments = [],

        public array $languages = [],

        public string $languageCustom = '',

        public string $challengeRating = '0',

    ) {}

}