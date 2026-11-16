<?php

namespace App\Services\Spells;

class SpellDisplayService
{
    private const SCHOOL_PT = [
        'Abjuration' => 'Abjuração',
        'Conjuration' => 'Conjuração',
        'Divination' => 'Adivinhação',
        'Enchantment' => 'Encantamento',
        'Evocation' => 'Evocação',
        'Illusion' => 'Ilusão',
        'Necromancy' => 'Necromancia',
        'Transmutation' => 'Transmutação',
    ];

    public function __construct(
        private readonly FiveEToolsTextFormatter $formatter,
        private readonly SpellRulesTextRenderer $rulesRenderer
    ) {
    }

    public function payload(object $spell): array
    {
        $components = $this->decode($spell->components ?? null);
        $higherLevelData = $this->decode($spell->higher_level_data ?? null);
        $scaling = $this->decode($spell->scaling_level_dice ?? null);
        $areaTags = $this->decode($spell->area_tags ?? null);
        $raw = null;

        // A fonte original foi preservada na importação. Ela também serve
        // de fallback quando os campos derivados não estiverem preenchidos.
        if ($scaling === null || $higherLevelData === null) {
            $raw = $this->decode($spell->raw_data ?? null);

            if (is_array($raw)) {
                if ($scaling === null) {
                    $scaling = $raw['scalingLevelDice'] ?? null;
                }

                if ($higherLevelData === null) {
                    $higherLevelData = $raw['entriesHigherLevel'] ?? null;
                }
            }
        }

        $description = $this->cleanDisplayText(
            trim((string) ($spell->description ?? ''))
        );

        $higherText = trim((string) ($spell->higher_level_text ?? ''));

        if (is_array($higherLevelData)) {
            $corrections = 0;
            $correctedData = $this->correctUpcastScalingTags(
                $higherLevelData,
                $corrections
            );

            if ($corrections > 0) {
                // Corrige textos JÁ importados: higher_level_text pode ter sido
                // salvo com o dano base (5d8), embora a fonte preserve o
                // incremento verdadeiro (1d8) em {@scaledamage 5d8|4-9|1d8}.
                $higherText = $this->formatter->entriesToText($correctedData);
            } elseif ($higherText === '') {
                $higherText = $this->formatter->entriesToText($higherLevelData);
            }
        } elseif ($higherText === '' && $higherLevelData !== null) {
            $higherText = $this->formatter->entriesToText($higherLevelData);
        }

        $higherText = $this->cleanDisplayText(
            $this->normalizeHigherLevelText($higherText)
        );

        $level = (int) $spell->level;
        $school = (string) $spell->school;
        $schoolPt = $this->schoolPt($school);
        $castingTime = (string) ($spell->casting_time ?? '—');
        $range = (string) ($spell->range ?? '—');
        $duration = (string) ($spell->duration ?? '—');

        return [
            'id' => (int) $spell->id,
            'name' => (string) $spell->name,
            'level' => $level,
            'level_label' => $this->levelLabel($level),
            'level_label_pt' => $level === 0 ? 'Truque' : $level . 'º círculo',
            'school' => $school,
            'school_pt' => $schoolPt,
            'subtitle' => $this->subtitle($school, $level),
            'subtitle_pt' => $level === 0
                ? 'Truque (' . $schoolPt . ')'
                : $level . 'º círculo (' . $schoolPt . ')',
            'casting_time' => $castingTime,
            'casting_time_display' => $this->translateCastingTime($castingTime),
            'range' => $range,
            'range_display' => $this->translateRange($range),
            'components' => $this->componentsText($components),
            'duration' => $duration,
            'duration_display' => $this->translateDuration($duration),
            'ritual' => (bool) ($spell->ritual ?? false),
            'concentration' => (bool) ($spell->concentration ?? false),
            'description' => $description,
            'description_html' => $this->rulesRenderer->render($description),
            'higher_level_text' => $higherText,
            'higher_level_html' => $this->rulesRenderer->render($higherText),
            'higher_level_label' => $level === 0
                ? 'Aprimoramento do Truque'
                : 'Em Níveis Superiores',
            'scaling_level_dice' => $this->scalingPayload($scaling),
            'saving_throw' => $spell->saving_throw ?? null,
            'attack_type' => $spell->attack_type ?? null,
            'area_tags' => is_array($areaTags) ? array_values($areaTags) : [],
            'source' => (string) ($spell->source ?? ''),
            'page' => isset($spell->page) ? (int) $spell->page : null,
            'source_label' => $this->sourceLabel(
                (string) ($spell->source ?? ''),
                isset($spell->page) ? (int) $spell->page : null
            ),
            'provider' => (string) ($spell->provider ?? 'custom'),
            'rules_version' => $spell->rules_version ?? null,
            'kind' => match ($spell->provider ?? null) {
                '5etools-homebrew' => 'homebrew',
                'custom' => 'custom',
                default => 'standard',
            },
        ];
    }

    /**
     * O 5etools usa a sintaxe:
     *   {@scaledamage DANO_BASE|FAIXA_DE_NIVEIS|INCREMENTO}
     *
     * Na seção de níveis superiores, o valor que deve aparecer no texto
     * "aumenta em X por nível" é o TERCEIRO campo.
     *
     * A rotina examina somente os dados de entriesHigherLevel, não a
     * descrição normal. Não altera o banco nem os campos raw_data.
     */
    private function correctUpcastScalingTags(
        mixed $value,
        int &$corrections
    ): mixed {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->correctUpcastScalingTags(
                    $item,
                    $corrections
                );
            }

            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        return preg_replace_callback(
            '~\{@scaledamage\s+([^|{}]+)\|([^|{}]+)\|([^|{}]+)(?:\|[^{}]*)?\}~iu',
            static function (array $match) use (&$corrections): string {
                $increment = trim($match[3]);

                if ($increment === '') {
                    return $match[0];
                }

                $corrections++;
                return $increment;
            },
            $value
        ) ?? $value;
    }

    private function cleanDisplayText(?string $text): string
    {
        $text = trim((string) $text);

        $text = preg_replace(
            '/\s*\[(?:Area of Effect|Área de Efeito)\]/iu',
            '',
            $text
        ) ?? $text;

        return trim($text);
    }

    private function normalizeHigherLevelText(?string $text): string
    {
        $text = trim((string) $text);

        if ($text === '') {
            return '';
        }

        // O Grimório já apresenta o título "Em Níveis Superiores".
        // Evita repetição da mesma informação em inglês no corpo.
        $headings = '(?:At Higher Levels|Using a Higher-Level Spell Slot|Cantrip Upgrade|Em Níveis Superiores|Aprimoramento do Truque)';

        $text = preg_replace(
            '/(^|\R)\s*' . $headings . '\.?\s*(?=\R|$)/iu',
            '$1',
            $text
        ) ?? $text;

        $text = preg_replace(
            '/^' . $headings . '\.?\s+/iu',
            '',
            $text
        ) ?? $text;

        $text = preg_replace('/\R{3,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function decode(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        }

        return $value;
    }

    private function levelLabel(int $level): string
    {
        return $level === 0 ? 'Cantrip' : $level . 'º círculo';
    }

    private function subtitle(string $school, int $level): string
    {
        return $level === 0
            ? trim($school . ' Cantrip')
            : trim($school . ' · ' . $level . 'º círculo');
    }

    private function schoolPt(string $school): string
    {
        return self::SCHOOL_PT[$school] ?? $school;
    }

    private function sourceLabel(string $source, ?int $page): string
    {
        $source = trim($source);
        return $page ? $source . ' ' . $page : $source;
    }

    private function translateCastingTime(string $value): string
    {
        return str_ireplace(
            [
                '1 Action', 'Bonus Action', '1 Reaction', 'Reaction',
                'Minute', 'Minutes', 'Hour', 'Hours', 'Round', 'Rounds',
            ],
            [
                '1 Ação', 'Ação Bônus', '1 Reação', 'Reação',
                'Minuto', 'Minutos', 'Hora', 'Horas', 'Rodada', 'Rodadas',
            ],
            $value
        );
    }

    private function translateRange(string $value): string
    {
        return str_ireplace(
            ['Self', 'Touch', 'Sight', 'Unlimited', 'Same Plane'],
            ['Pessoal', 'Toque', 'Visão', 'Ilimitado', 'Mesmo Plano'],
            $value
        );
    }

    private function translateDuration(string $value): string
    {
        return str_ireplace(
            [
                'Instantaneous', 'Concentration, up to ', 'Permanent',
                'minute', 'minutes', 'hour', 'hours', 'round', 'rounds', 'day', 'days',
            ],
            [
                'Instantânea', 'Concentração, até ', 'Permanente',
                'minuto', 'minutos', 'hora', 'horas', 'rodada', 'rodadas', 'dia', 'dias',
            ],
            $value
        );
    }

    private function componentsText(mixed $components): string
    {
        if (!is_array($components)) {
            return '—';
        }

        $parts = [];

        if (!empty($components['v'])) {
            $parts[] = 'V';
        }

        if (!empty($components['s'])) {
            $parts[] = 'S';
        }

        if (array_key_exists('m', $components) && $components['m'] !== false) {
            $material = $components['m'];

            if (is_array($material)) {
                $text = trim((string) ($material['text'] ?? ''));
                $parts[] = $text !== '' ? 'M (' . $text . ')' : 'M';
            } elseif (is_string($material) && trim($material) !== '') {
                $parts[] = 'M (' . trim($material) . ')';
            } else {
                $parts[] = 'M';
            }
        }

        if (!empty($components['r'])) {
            $parts[] = 'R';
        }

        return $parts !== [] ? implode(', ', $parts) : '—';
    }

    private function scalingPayload(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = array_is_list($value) ? $value : [$value];
        $result = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $scaling = $item['scaling'] ?? [];

            if (!is_array($scaling)) {
                continue;
            }

            $levels = [];

            foreach ($scaling as $level => $dice) {
                $levels[] = [
                    'level' => (int) $level,
                    'value' => (string) $dice,
                ];
            }

            usort($levels, fn (array $a, array $b) => $a['level'] <=> $b['level']);

            $label = trim((string) ($item['label'] ?? 'Progressão'));

            $result[] = [
                'label' => $label !== '' ? $label : 'Progressão',
                'levels' => $levels,
            ];
        }

        return $result;
    }
}