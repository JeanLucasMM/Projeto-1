<?php

return [
    'fiveetools' => [
        'index_url' => env(
            'FIVEETOOLS_SPELL_INDEX_URL',
            'https://raw.githubusercontent.com/5etools-mirror-3/5etools-src/main/data/spells/index.json'
        ),

        'base_url' => env(
            'FIVEETOOLS_SPELL_BASE_URL',
            'https://raw.githubusercontent.com/5etools-mirror-3/5etools-src/main/data/spells'
        ),

        'timeout' => (int) env('FIVEETOOLS_TIMEOUT', 60),
        'retries' => (int) env('FIVEETOOLS_RETRIES', 3),
        'retry_sleep_ms' => (int) env('FIVEETOOLS_RETRY_SLEEP_MS', 750),
    ],

    'fiveetools_homebrew' => [
        /*
        |--------------------------------------------------------------------------
        | Catálogo third-party/homebrew do ecossistema 5etools
        |--------------------------------------------------------------------------
        |
        | index-props.json informa quais arquivos possuem a propriedade "spell".
        | O Spellbound baixa somente esses arquivos e, dentro deles, importa
        | somente o array "spell".
        |
        */

        'index_url' => env(
            'FIVEETOOLS_HOMEBREW_INDEX_URL',
            'https://raw.githubusercontent.com/TheGiddyLimit/homebrew/master/_generated/index-props.json'
        ),

        'base_url' => env(
            'FIVEETOOLS_HOMEBREW_BASE_URL',
            'https://raw.githubusercontent.com/TheGiddyLimit/homebrew/master'
        ),

        'timeout' => (int) env('FIVEETOOLS_HOMEBREW_TIMEOUT', 60),
        'retries' => (int) env('FIVEETOOLS_HOMEBREW_RETRIES', 3),
        'retry_sleep_ms' => (int) env('FIVEETOOLS_HOMEBREW_RETRY_SLEEP_MS', 750),
    ],
];
