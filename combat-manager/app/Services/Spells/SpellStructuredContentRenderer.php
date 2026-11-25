<?php

namespace App\Services\Spells;

/**
 * Apresenta verbetes importados do 5etools sem achatar seus blocos de conteúdo.
 * O texto pode vir de `raw_data.entries` (preferencial) ou das descrições
 * antigas, já normalizadas pelo importador em parágrafos separados por \n\n.
 *
 * Não escreve na base de dados. O HTML gerado é seguro e usado apenas na
 * apresentação pública do feitiço no Inventário/Grimório.
 */
final class SpellStructuredContentRenderer
{
    private const MAX_DEPTH = 14;

    public function __construct(
        private readonly SpellRulesTextRenderer $rules,
        private readonly FiveEToolsTextFormatter $formatter,
    ) {
    }

    public function render(mixed $entries, ?string $fallback = null, bool $higherLevel = false): string
    {
        if ($higherLevel) {
            $entries = $this->withoutRedundantHigherTitle($entries);
        }
        $body = $this->blocks($entries, 0);
        if ($body === '') {
            $body = $this->legacyBlocks(trim((string) $fallback));
        }

        // Os blocos ficam diretamente dentro da leitura do cofre/grimório.
        // Isto mantém as regras CSS já aprovadas do Cofre (`> p`, `> ul`).
        if ($higherLevel && substr_count($body, '<p>') === 1
            && preg_match('~^<p>(.*?)</p>$~su', $body, $match)) {
            return $match[1];
        }
        return $body;
    }

    /** O Grimório já imprime o título traduzido fora do corpo. */
    private function withoutRedundantHigherTitle(mixed $value): mixed
    {
        // Algumas importações antigas guardaram o cabeçalho como linha solta,
        // não como `entries.name`. Remover apenas a linha que for SÓ o título.
        if (is_string($value)) {
            return $this->isRedundantHigherTitle($value) ? '' : $value;
        }
        if (!is_array($value)) {
            return $value;
        }
        if (array_is_list($value)) {
            return array_map(fn ($item) => $this->withoutRedundantHigherTitle($item), $value);
        }
        if ($this->isRedundantHigherTitle((string) ($value['name'] ?? ''))) {
            unset($value['name']);
        }
        foreach (['entries', 'entry', 'items'] as $key) {
            if (array_key_exists($key, $value)) {
                $value[$key] = $this->withoutRedundantHigherTitle($value[$key]);
            }
        }
        return $value;
    }

    private function isRedundantHigherTitle(string $value): bool
    {
        $title = trim($value);
        return $title !== '' && preg_match(
            '/^(?:at higher levels?|using a higher.level spell slot|upcasting|cantrip upgrades?|cantrip scaling|cantrip improvements?|em n[ií]veis superiores|em n[ií]vel superior|aprimoramento do truque)\s*[:.]?$/iu',
            $title,
        ) === 1;
    }

    private function blocks(mixed $value, int $depth): string
    {
        if ($depth >= self::MAX_DEPTH || $value === null) {
            return '';
        }

        if (is_string($value) || is_numeric($value)) {
            return $this->legacyBlocks((string) $value);
        }

        if (!is_array($value)) {
            return '';
        }

        if (array_is_list($value)) {
            return implode('', array_map(fn ($child) => $this->blocks($child, $depth + 1), $value));
        }

        // Algumas fontes preservam o feitiço dentro de `spell`.
        if (!isset($value['entries']) && isset($value['spell']) && is_array($value['spell'])) {
            return $this->blocks($value['spell'], $depth + 1);
        }

        $type = strtolower((string) ($value['type'] ?? 'entries'));
        if ($type === 'table') {
            return $this->table($value, $depth + 1);
        }
        if ($type === 'list') {
            return $this->listBlocks($value['items'] ?? [], $depth + 1);
        }
        if (in_array($type, ['quote', 'inset', 'insetreadaloud'], true)) {
            $content = $this->blocks($value['entries'] ?? $value['entry'] ?? [], $depth + 1);
            return $content === '' ? '' : '<blockquote class="sb-spell-quote">' . $content . '</blockquote>';
        }

        $body = '';
        $name = trim((string) ($value['name'] ?? ''));
        if ($name !== '') {
            $body .= '<p class="sb-spell-subheading"><strong>' . $this->inline($name) . '</strong></p>';
        }

        foreach (['entry', 'entries', 'items'] as $key) {
            if (array_key_exists($key, $value)) {
                $body .= $this->blocks($value[$key], $depth + 1);
            }
        }
        return $body;
    }

    private function listBlocks(mixed $items, int $depth): string
    {
        if (!is_array($items) || $items === []) {
            return '';
        }

        $rows = '';
        foreach ($items as $entry) {
            if (is_string($entry) || is_numeric($entry)) {
                $content = $this->inline(trim((string) $entry));
            } elseif (is_array($entry) && !array_is_list($entry)) {
                $name = trim((string) ($entry['name'] ?? ''));
                $content = ($name !== '' ? '<strong>' . $this->inline($name) . '</strong> ' : '')
                    . $this->blocks($entry['entries'] ?? $entry['entry'] ?? $entry['items'] ?? [], $depth + 1);
            } else {
                $content = $this->blocks($entry, $depth + 1);
            }
            if ($content !== '') {
                $rows .= '<li>' . $content . '</li>';
            }
        }
        return $rows !== '' ? '<ul class="sb-spell-list">' . $rows . '</ul>' : '';
    }

    private function table(array $data, int $depth): string
    {
        $columns = is_array($data['colLabels'] ?? null) ? $data['colLabels'] : [];
        $rows = is_array($data['rows'] ?? null) ? $data['rows'] : [];
        if ($columns === [] && $rows === []) {
            return '';
        }
        $out = '<div class="sb-spell-table-wrap"><table class="sb-spell-table">';
        if (!empty($data['caption'])) {
            $out .= '<caption>' . $this->inline((string) $data['caption']) . '</caption>';
        }
        if ($columns !== []) {
            $out .= '<thead><tr>';
            foreach ($columns as $heading) {
                $out .= '<th scope="col">' . $this->inline(is_scalar($heading) ? (string) $heading : '') . '</th>';
            }
            $out .= '</tr></thead>';
        }
        $out .= '<tbody>';
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $out .= '<tr>';
            foreach ($row as $cell) {
                $content = is_scalar($cell) ? $this->inline((string) $cell) : $this->blocks($cell, $depth + 1);
                $out .= '<td>' . $content . '</td>';
            }
            $out .= '</tr>';
        }
        return $out . '</tbody></table></div>';
    }

    private function legacyBlocks(string $source): string
    {
        $source = trim(str_replace(["\r\n", "\r"], "\n", $source));
        if ($source === '') {
            return '';
        }

        $groups = preg_split('/\n[ \t]*\n+/u', $source) ?: [];
        $out = '';
        foreach ($groups as $i => $group) {
            $group = trim($group);
            if ($group === '') {
                continue;
            }

            // O importador antigo serializava `entries.name` como um parágrafo
            // isolado. Reconhecer só esse formato impede falsos subtítulos.
            $shortHeading = count($groups) > $i + 1
                && !str_contains($group, "\n")
                && (function_exists('mb_strlen') ? mb_strlen($group, 'UTF-8') : strlen($group)) <= 70
                && preg_match('/^[\pL][\pL\pN\s\-\/\(\),\x{2019}]+:?$/u', $group)
                && count(preg_split('/\s+/u', $group) ?: []) <= 7;

            if ($shortHeading) {
                $out .= '<p class="sb-spell-subheading"><strong>' . $this->inline(rtrim($group, ':')) . '</strong></p>';
            } elseif (preg_match('/^(?:[-*] .+(?:\n|$)){2,}$/u', $group)) {
                $items = preg_split('/\n/u', $group) ?: [];
                $out .= '<ul class="sb-spell-list">';
                foreach ($items as $item) {
                    $out .= '<li>' . $this->inline(preg_replace('/^[-*]\s+/u', '', $item) ?? $item) . '</li>';
                }
                $out .= '</ul>';
            } else {
                // Uma única quebra interna é uma nova linha do mesmo parágrafo;
                // não suprime as separações intencionais entre os blocos.
                $out .= '<p>' . str_replace("\n", '', $this->inline($group)) . '</p>';
            }
        }
        return $out;
    }

    private function inline(string $value): string
    {
        // Os marcadores de 5etools são traduzidos com segurança, antes que
        // o limpador existente descarte os metadados em {@b ...}/{@i ...}.
        $value = preg_replace_callback(
            '/\{@(b|bold|i|italic)\s+([^{}]+)\}/iu',
            static function (array $m): string {
                $bold = in_array(strtolower($m[1]), ['b', 'bold'], true);
                return ($bold ? 'SBBOLDOPENMARKER' : 'SBITALICOPENMARKER')
                    . $m[2]
                    . ($bold ? 'SBBOLDCLOSEMARKER' : 'SBITALICCLOSEMARKER');
            },
            $value,
        ) ?? $value;
        $text = $this->formatter->cleanInlineTags($value);
        $html = $this->rules->render($text);
        return str_replace(
            ['SBBOLDOPENMARKER', 'SBBOLDCLOSEMARKER', 'SBITALICOPENMARKER', 'SBITALICCLOSEMARKER'],
            ['<strong>', '</strong>', '<em>', '</em>'],
            $html,
        );
    }
}
