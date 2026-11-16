<?php

namespace App\Services\Spells;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpellClassListService
{
    private const LISTS = [
        'artificer' => ['name' => 'Artificer', 'label' => 'Artífice', 'aliases' => ['artificer', 'artifice', 'artífice']],
        'bard' => ['name' => 'Bard', 'label' => 'Bardo', 'aliases' => ['bard', 'bardo']],
        'cleric' => ['name' => 'Cleric', 'label' => 'Clérigo', 'aliases' => ['cleric', 'clerigo', 'clérigo']],
        'druid' => ['name' => 'Druid', 'label' => 'Druida', 'aliases' => ['druid', 'druida']],
        'paladin' => ['name' => 'Paladin', 'label' => 'Paladino', 'aliases' => ['paladin', 'paladino']],
        'ranger' => ['name' => 'Ranger', 'label' => 'Patrulheiro', 'aliases' => ['ranger', 'patrulheiro']],
        'sorcerer' => ['name' => 'Sorcerer', 'label' => 'Feiticeiro', 'aliases' => ['sorcerer', 'feiticeiro']],
        'warlock' => ['name' => 'Warlock', 'label' => 'Bruxo', 'aliases' => ['warlock', 'bruxo']],
        'wizard' => ['name' => 'Wizard', 'label' => 'Mago', 'aliases' => ['wizard', 'mago']],
    ];

    public function availableLists(): array
    {
        return collect(self::LISTS)
            ->map(fn (array $row, string $code) => [
                'code' => $code,
                'name' => $row['name'],
                'label' => $row['label'],
            ])
            ->values()
            ->all();
    }

    public function canonicalCode(?string $name): ?string
    {
        $needle = $this->normalize((string) $name);
        if ($needle === '') {
            return null;
        }
        foreach (self::LISTS as $code => $row) {
            foreach ($row['aliases'] as $alias) {
                if ($needle === $this->normalize($alias)) {
                    return $code;
                }
            }
        }
        return null;
    }

    public function displayLabel(string $code): string
    {
        return self::LISTS[$code]['label'] ?? Str::headline($code);
    }

    public function canonicalName(string $code): string
    {
        return self::LISTS[$code]['name'] ?? Str::headline($code);
    }

    public function classCodesForCharacterClasses(iterable $classes): array
    {
        $codes = [];
        foreach ($classes as $class) {
            $code = $this->canonicalCode((string) ($class->class ?? ''));
            if ($code) {
                $codes[] = $code;
                continue;
            }
            $className = $this->normalize((string) ($class->class ?? ''));
            $subclass = $this->normalize((string) ($class->subclass ?? ''));
            if (in_array($className, ['fighter', 'guerreiro'], true)
                && in_array($subclass, ['eldritch knight', 'cavaleiro arcano'], true)) {
                $codes[] = 'wizard';
            }
            if (in_array($className, ['rogue', 'ladino'], true)
                && in_array($subclass, ['arcane trickster', 'trapaceiro arcano'], true)) {
                $codes[] = 'wizard';
            }
        }
        return array_values(array_unique($codes));
    }

    /**
     * Listas-base, e apenas elas. Entradas de variante/subclasse NÃO são
     * classes-base, mesmo quando concedem uma magia adicional ao personagem.
     */
    public function listsForSpellIds(Collection|array $spellIds): array
    {
        $ids = collect($spellIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $query = DB::table('spell_class_lists as scl')
            ->join('spells as s', 's.id', '=', 'scl.spell_id')
            ->whereIn('scl.spell_id', $ids->all())
            ->where('scl.list_type', 'class');

        $this->constrainToPrimary2024Lists($query, 's', 'scl');

        return $query
            ->orderBy('scl.class_name')
            ->get([
                'scl.spell_id',
                'scl.class_code',
                'scl.class_name',
                'scl.class_source',
                'scl.subclass_name',
                'scl.subclass_source',
                'scl.list_type',
            ])
            ->groupBy(fn ($row) => (int) $row->spell_id)
            ->map(fn ($rows) => $rows
                ->map(fn ($row) => [
                    'code' => $row->class_code,
                    'name' => $row->class_name,
                    'label' => $this->displayLabel($row->class_code),
                    'source' => $row->class_source,
                    'subclass' => $row->subclass_name,
                    'subclass_source' => $row->subclass_source,
                    'type' => $row->list_type,
                ])
                ->unique('code')
                ->values()
                ->all())
            ->all();
    }

    /**
     * O filtro "Listas" do Spellbound usa as listas-base da edição de 2024.
     *
     * XPHB contém o texto das magias do Player's Handbook 2024. Neste contexto,
     * não misturamos listagens de classes PHB 2014 ou de Artífice EFA 2025.
     * Para feitiços de suplementos, vale a lista da classe 2024 (XPHB),
     * além da lista 2025 do Artífice (EFA) quando aplicável.
     * Política da mesa: os EGW sem lista e Encode Thoughts (GGR) entram em Mago.
     *
     * Os vínculos de outros livros CONTINUAM no banco; apenas a apresentação
     * canônica de 2024 é filtrada. Atributos de subclasse/variant ficam em tipos
     * separados e não são apresentados como lista-base.
     *
     * Exige joins/aliases válidos no escopo da consulta. Não altera registros.
     */
    public function constrainToPrimary2024Lists(
        \Illuminate\Database\Query\Builder $query,
        string $spellAlias = 'spells',
        string $listAlias = 'scl'
    ): void {
        $query->where(function ($outer) use ($spellAlias, $listAlias): void {
            $outer->where($spellAlias . '.provider', '<>', '5etools')
                ->orWhere(function ($base) use ($spellAlias, $listAlias): void {
                    $base->where($spellAlias . '.provider', '=', '5etools')
                        ->where($spellAlias . '.source', '=', 'XPHB')
                        ->where($listAlias . '.class_source', '=', 'XPHB');
                })
                ->orWhere(function ($other) use ($spellAlias, $listAlias): void {
                    $other->where($spellAlias . '.provider', '=', '5etools')
                        ->where($spellAlias . '.source', '<>', 'XPHB')
                        ->whereIn($listAlias . '.class_source', ['XPHB', 'EFA']);
                })
                // Política de catálogo Spellbound: EGW e Encode Thoughts
                // (GGR) aparecem como Mago na lista-base. São apenas
                // vínculos de catálogo, sem controles adicionais de acesso.
                ->orWhere(function ($localWizard) use ($spellAlias, $listAlias): void {
                    $localWizard->where($spellAlias . '.provider', '=', '5etools')
                        ->where($listAlias . '.class_code', '=', 'wizard')
                        ->where(function ($sources) use ($spellAlias, $listAlias): void {
                            $sources->where(function ($egw) use ($spellAlias, $listAlias): void {
                                $egw->where($spellAlias . '.source', '=', 'EGW')
                                    ->where($listAlias . '.class_source', '=', 'EGW');
                            })->orWhere(function ($ggr) use ($spellAlias, $listAlias): void {
                                $ggr->where($spellAlias . '.source', '=', 'GGR')
                                    ->where($spellAlias . '.name', '=', 'Encode Thoughts')
                                    ->where($listAlias . '.class_source', '=', 'GGR');
                            });
                        });
                });
        });
    }

    public function normalize(string $value): string
    {
        $value = Str::ascii(mb_strtolower(trim($value), 'UTF-8'));
        return preg_replace('/\s+/u', ' ', $value) ?? $value;
    }
}
