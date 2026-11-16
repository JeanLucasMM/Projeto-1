<?php

namespace App\Services\Spells;

use App\Models\Spell;
use Illuminate\Support\Str;

/**
 * Hover dos feitiços de NPCs: pesquisa SOMENTE nomes presentes na ficha.
 *
 * Não percorre as 16 mil+ magias do catálogo a cada abertura de combate.
 * A política de fontes é a mesma do Grimório. Não inventa vínculos.
 */
final class NpcSpellTooltipService
{
    public function __construct(
        private readonly CampaignSpellAccessService $spellAccess,
    ) {
    }

    /** @return array<int, array<string, mixed>> */
    public function forNpc(object $combatNpc, ?int $campaignId): array
    {
        $candidates = $this->spellNamesInNpc($combatNpc);
        if ($candidates === []) {
            return [];
        }

        $cacheKey = '_sb_npc_hover_v65_' . ($campaignId === null ? 'official' : $campaignId);
        /** @var array<string,array|null> $cache */
        $cache = request()->attributes->get($cacheKey, []);
        $unknown = array_filter(
            $candidates,
            static fn (string $name): bool => !array_key_exists(self::nameKey($name), $cache)
        );

        if ($unknown !== []) {
            $this->resolveCandidates($unknown, $campaignId, $cache);
            request()->attributes->set($cacheKey, $cache);
        }

        $result = [];
        foreach ($candidates as $candidate) {
            $entry = $cache[self::nameKey($candidate)] ?? null;
            if ($entry === null) {
                continue;
            }
            $key = (int) $entry['id'];
            if (!isset($result[$key])) {
                $result[$key] = $entry;
                $result[$key]['mentions'] = [];
            }
            if (!in_array($candidate, $result[$key]['mentions'], true)) {
                $result[$key]['mentions'][] = $candidate;
            }
        }

        return array_values($result);
    }

    /**
     * Uma consulta direcionada aos prefixos dos feitiços mencionados.
     * Fuzzy match só com diferença pequena para evitar nome errado.
     *
     * @param array<int,string> $names
     * @param array<string,array|null> $cache
     */
    private function resolveCandidates(array $names, ?int $campaignId, array &$cache): void
    {
        $names = array_values(array_unique($names));
        $prefixes = [];
        foreach ($names as $name) {
            $firstWord = preg_split('/[\s\'’\-_]+/u', trim($name))[0] ?? '';
            $prefix = mb_substr($firstWord, 0, 4);
            if (mb_strlen($prefix) >= 3 && preg_match('/^[\p{L}]+$/u', $prefix)) {
                $prefixes[mb_strtolower($prefix)] = $prefix;
            }
        }

        // Apenas ID e metadados leves primeiro; descrições completas só para matches.
        $query = $this->spellAccess->allowedSpellQuery($campaignId);
        $query->where(function ($builder) use ($names, $prefixes) {
            $builder->whereIn('spells.name', $names);
            foreach ($prefixes as $prefix) {
                // LIKE ancorado ao início; nunca executamos LIKE %nome% no catálogo.
                $builder->orWhere('spells.name', 'like', addcslashes($prefix, '%_\\') . '%');
            }
        });

        $rows = $query
            ->limit(1000)
            ->get(['spells.id', 'spells.name', 'spells.source', 'spells.provider']);

        $matched = [];
        foreach ($names as $candidate) {
            $canonicalKey = self::nameKey($candidate);
            $best = null;
            $bestScore = -1;
            foreach ($rows as $row) {
                $spellKey = self::nameKey((string) $row->name);
                if ($spellKey === $canonicalKey) {
                    $distance = 0;
                } else {
                    if (strlen($canonicalKey) < 6 || strlen($spellKey) < 6) {
                        continue;
                    }
                    $distance = levenshtein($canonicalKey, $spellKey);
                    $limit = strlen($canonicalKey) >= 11 ? 2 : 1;
                    if ($distance > $limit) {
                        continue;
                    }
                }
                $score = (100 - ($distance * 50)) + $this->priority($row);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = $row;
                }
            }
            $matched[$canonicalKey] = $best ? (int) $best->id : null;
        }

        $ids = array_values(array_unique(array_filter(array_values($matched))));
        $details = $ids === []
            ? collect()
            : Spell::query()->whereIn('id', $ids)->get()->keyBy('id');

        foreach ($matched as $key => $id) {
            $spell = $id !== null ? $details->get($id) : null;
            $cache[$key] = $spell ? $this->payload($spell) : null;
        }
    }

    /** @return array<int,string> */
    private function spellNamesInNpc(object $combatNpc): array
    {
        $npc = $combatNpc->npc ?? null;
        $data = $npc?->json_data ?? [];
        if (is_string($data)) {
            $data = json_decode($data, true) ?: [];
        }
        $out = [];

        // NPC Builder: nomes exatos estão nos slots; não serializar o JSON inteiro.
        if (is_array($data)) {
            foreach (['features', 'actions', 'bonusActions', 'reactions', 'legendaryActions', 'lairActions', 'mythicActions'] as $section) {
                foreach (($data[$section] ?? []) as $item) {
                    if (!is_array($item)) continue;
                    foreach (($item['spellcasting']['slots'] ?? []) as $slot) {
                        if (is_array($slot)) {
                            $this->addSpellList($slot['spells'] ?? '', $out);
                        }
                    }
                    $this->namesInParentheses((string) ($item['content'] ?? ''), $out);
                    $this->namesInSpellcastingLines((string) ($item['content'] ?? ''), $out);
                }
            }
        }

        // Fichas 5e tradicionais: listas aparecem nos parênteses das ações.
        foreach (($combatNpc->viewModel->sections ?? []) as $section) {
            $items = is_array($section) ? ($section['items'] ?? []) : ($section->items ?? []);
            foreach ($items as $item) {
                $content = is_array($item) ? ($item['text'] ?? '') : ($item->text ?? '');
                $this->namesInParentheses((string) $content, $out);
                $this->namesInSpellcastingLines((string) $content, $out);
            }
        }
        return array_slice(array_values($out), 0, 170);
    }

    /** @param array<string,string> $out */
    private function namesInParentheses(string $html, array &$out): void
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match_all('/\(((?:[^()]|\([^()]*\))*)\)/u', $text, $groups)) {
            foreach ($groups[1] as $group) {
                if (mb_strlen($group) > 380 || !preg_match('/[\p{L}]{3,}/u', $group)) continue;
                // Única palavra também pode ser magia (ex.: Shield),
                // mas números/referências de ataque não são considerados.
                $this->addSpellList($group, $out);
            }
        }
    }

    /** @param array<string,string> $out */
    private function namesInSpellcastingLines(string $html, array &$out): void
    {
        $text = preg_replace('/<\s*br\b[^>]*>|<\s*\/\s*(?:p|div|li)\s*>/iu', "\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        foreach (preg_split('/\r?\n/u', $text) ?: [] as $line) {
            if (!preg_match('/^\s*(?:truques|cantrips|à\s*vontade|a\s*vontade|at\s*will|[1-9][º°ªo]?\s*(?:nível|level|círculo)|[1-9](?:st|nd|rd|th)\s*level)\b[^:;.]*[:;.\-–]\s*(.+)$/iu', trim($line), $match)) {
                continue;
            }
            if (!str_contains($match[1], ',')) {
                $this->addSpellList($match[1], $out);
                continue;
            }
            $this->addSpellList($match[1], $out);
        }
    }

    /** @param array<string,string> $out */
    private function addSpellList(mixed $value, array &$out): void
    {
        if (!is_string($value) || trim($value) === '') return;
        foreach (preg_split('/[,;\r\n]+/u', html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?: [] as $part) {
            $name = trim($part);
            // Metadados do NPC: (1/Dia), (Reação), (BonusAct), (Concentração) etc.
            $name = trim(preg_replace('/\s*\([^()]{1,38}\)\s*$/u', '', $name) ?? $name);
            $name = trim($name, " \t\n\r\0\x0B.:-–—");
            $key = self::nameKey($name);
            if (mb_strlen($name) < 3 || mb_strlen($name) > 90 || !preg_match('/[\p{L}]{3,}/u', $name)) continue;
            if (preg_match('/\b\d+d\d+\b|^(?:cd|ac|prof|mod|nivel|level)\b/iu', $name)) continue;
            if (strlen($key) < 3) continue;
            $out[$key] = $name;
        }
    }

    private static function nameKey(string $name): string
    {
        // Compactar os espaços permite reconhecer FireBolt → Fire Bolt.
        $normalized = Str::lower(Str::ascii(str_replace('’', "'", $name)));
        return preg_replace('/[^a-z0-9]/', '', $normalized) ?? '';
    }

    private function priority(object $spell): int
    {
        $source = strtoupper((string) ($spell->source ?? ''));
        $provider = strtolower((string) ($spell->provider ?? ''));
        return ($source === 'XPHB' ? 10 : ($source === 'PHB' ? 8 : 0))
            + (str_contains($provider, 'official') ? 4 : 0);
    }

    /** @return array<string,mixed> */
    private function payload(Spell $spell): array
    {
        return [
            'id' => (int) $spell->id,
            'name' => (string) $spell->name,
            'level' => (int) ($spell->level ?? 0),
            'school' => $this->schoolLabel((string) ($spell->school ?? '')),
            'source' => (string) ($spell->source ?? ''),
            'casting_time' => (string) ($spell->casting_time ?? ''),
            'range' => (string) ($spell->range ?? ''),
            'components' => $this->formatComponents($spell->components ?? ''),
            'duration' => (string) ($spell->duration ?? ''),
            'concentration' => (bool) ($spell->concentration ?? false),
            'ritual' => (bool) ($spell->ritual ?? false),
            'description' => $this->plainDescription((string) ($spell->description ?? '')),
            'higher_level' => $this->higherLevelDescription($spell),
        ];
    }

    private function schoolLabel(string $school): string
    {
        return [
            'abjuration' => 'Abjuração', 'conjuration' => 'Conjuração',
            'divination' => 'Adivinhação', 'enchantment' => 'Encantamento',
            'evocation' => 'Evocação', 'illusion' => 'Ilusão',
            'necromancy' => 'Necromancia', 'transmutation' => 'Transmutação',
        ][Str::lower(trim($school))] ?? $school;
    }

    private function plainDescription(string $html): string
    {
        $html = preg_replace('/<\s*br\s*\/?\s*>/iu', "\n", $html) ?? $html;
        $html = preg_replace('/<\s*\/\s*(p|li|div|h[1-6])\s*>/iu', "\n\n", $html) ?? $html;
        $html = preg_replace('/<\s*li\b[^>]*>/iu', '• ', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\t ]+/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text);
    }

    private function higherLevelDescription(object $spell): string
    {
        $upcast = $spell->upcast ?? null;
        if (is_string($upcast) && trim($upcast) !== '') {
            return $this->plainDescription($upcast);
        }
        $raw = $spell->higher_level_data ?? null;
        if (is_string($raw)) {
            $data = json_decode($raw, true);
            if (is_array($data)) {
                foreach ($data as $value) {
                    if (is_string($value) && trim($value) !== '') {
                        return $this->plainDescription($value);
                    }
                }
            }
        }
        return '';
    }

    private function formatComponents(mixed $components): string
    {
        if (!is_array($components)) return trim((string) $components);
        $parts = [];
        foreach ($components as $key => $value) {
            if ($value === null || $value === false) continue;
            if (is_int($key)) {
                if (is_scalar($value)) $parts[] = (string) $value;
                continue;
            }
            $code = strtoupper((string) $key);
            if ($value === true || $value === 1) $parts[] = $code;
            elseif (is_string($value)) $parts[] = $code . ' (' . $value . ')';
            elseif (is_array($value)) $parts[] = $code;
        }
        return implode(', ', $parts);
    }
}