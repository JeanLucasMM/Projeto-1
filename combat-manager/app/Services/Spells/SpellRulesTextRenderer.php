<?php

namespace App\Services\Spells;

class SpellRulesTextRenderer
{
    private const DAMAGE_TYPES = [
        'acid', 'ácido',
        'bludgeoning', 'concussão',
        'cold', 'frio',
        'fire', 'fogo',
        'force', 'energia',
        'lightning', 'elétrico', 'eletricidade',
        'necrotic', 'necrótico',
        'piercing', 'perfurante',
        'poison', 'veneno',
        'psychic', 'psíquico',
        'radiant', 'radiante',
        'slashing', 'cortante',
        'thunder', 'trovejante',
    ];

    public function __construct(
        private readonly Dnd2024RulesGlossary $glossary
    ) {
    }

    public function render(?string $text): string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return '';
        }

        // Defensive cleanup. This makes old DB rows look correct immediately,
        // even before a new import/sync is run.
        $text = preg_replace(
            '/\s*\[(?:Area of Effect|Área de Efeito)\]/iu',
            '',
            $text
        ) ?? $text;

        $escaped = htmlspecialchars(
            $text,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $tokens = [];
        $counter = 0;

        $protect = function (string $html) use (&$tokens, &$counter): string {
            $token = "@@SPELLBOUND_TOKEN_{$counter}@@";
            $tokens[$token] = $html;
            $counter++;
            return $token;
        };

        // Dice are mechanically important, so make them pop without turning
        // the whole paragraph into colored noise.
        $escaped = preg_replace_callback(
            '/(?<![\pL\pN])(\d+d\d+(?:\s*[+\-]\s*\d+)?)(?![\pL\pN])/iu',
            fn (array $m) => $protect(
                '<strong class="spell-roll-value">'
                . $m[1]
                . '</strong>'
            ),
            $escaped
        ) ?? $escaped;

        $damagePattern = '/(?<![\pL\pN])('
            . implode('|', array_map(
                fn (string $term) => preg_quote($term, '/'),
                self::DAMAGE_TYPES
            ))
            . ')(?![\pL\pN])/iu';

        $escaped = preg_replace_callback(
            $damagePattern,
            fn (array $m) => $protect(
                '<strong class="spell-damage-type">'
                . $m[0]
                . '</strong>'
            ),
            $escaped
        ) ?? $escaped;

        $entries = $this->glossary->entries();
        $terms = array_keys($entries);

        usort(
            $terms,
            fn (string $a, string $b) => strlen($b) <=> strlen($a)
        );

        if ($terms !== []) {
            $pattern = '/(?<![\pL\pN])('
                . implode('|', array_map(
                    fn ($term) => preg_quote($term, '/'),
                    $terms
                ))
                . ')(?![\pL\pN])/iu';

            $escaped = preg_replace_callback(
                $pattern,
                function (array $matches) use ($entries, $protect): string {
                    $visible = $matches[0];
                    $entry = $entries[strtolower($visible)] ?? null;

                    if (! $entry) {
                        return $visible;
                    }

                    $label = htmlspecialchars(
                        (string) $entry['label'],
                        ENT_QUOTES | ENT_SUBSTITUTE,
                        'UTF-8'
                    );
                    $kind = htmlspecialchars(
                        (string) $entry['kind'],
                        ENT_QUOTES | ENT_SUBSTITUTE,
                        'UTF-8'
                    );
                    $description = htmlspecialchars(
                        (string) $entry['description'],
                        ENT_QUOTES | ENT_SUBSTITUTE,
                        'UTF-8'
                    );

                    return $protect(
                        '<span class="spell-rule-term" tabindex="0">'
                        . $visible
                        . '<span class="spell-rule-tooltip">'
                        . '<span class="spell-rule-tooltip-kind">'
                        . $kind
                        . ' · Regras 2024</span>'
                        . '<strong class="spell-rule-tooltip-title">'
                        . $label
                        . '</strong>'
                        . '<span class="spell-rule-tooltip-copy">'
                        . $description
                        . '</span>'
                        . '</span>'
                        . '</span>'
                    );
                },
                $escaped
            ) ?? $escaped;
        }

        foreach ($tokens as $token => $html) {
            $escaped = str_replace($token, $html, $escaped);
        }

        return nl2br($escaped, false);
    }
}
