<?php

namespace App\Services\Spells;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CampaignSpellAccessService
{
    /**
     * Retorna o catálogo permitido para a campanha e já aplica a
     * canonicalização de edição.
     *
     * O banco continua preservando versões antigas. Para exibição:
     * - XPHB é preferido;
     * - se existir um XPHB oficial com o mesmo nome, PHB/XGE/etc.
     *   ficam ocultos;
     * - homebrew/custom nunca é substituído por XPHB só por ter o mesmo nome.
     */
    public function allowedSpellQuery(
        ?int $campaignId = null
    ): Builder {
        $query = DB::table('spells')
            ->where('spells.active', true)
            ->where('spells.upstream_missing', false);

        $this->applyCampaignAccess(
            $query,
            $campaignId
        );

        $this->applyCanonicalEdition(
            $query
        );

        return $query;
    }

    public function isSpellAllowed(
        ?int $campaignId,
        int $spellId
    ): bool {
        /*
         * Aqui NÃO aplicamos a canonicalização. Se o personagem já possui
         * uma versão antiga, ela continua válida e não perde a referência.
         */
        $query = DB::table('spells')
            ->where('spells.id', $spellId)
            ->where('spells.active', true)
            ->where('spells.upstream_missing', false);

        $this->applyCampaignAccess(
            $query,
            $campaignId
        );

        return $query->exists();
    }

    private function applyCampaignAccess(
        Builder $query,
        ?int $campaignId
    ): void {
        if (! $campaignId) {
            $query->where(
                'spells.provider',
                '<>',
                '5etools-homebrew'
            );

            return;
        }

        $allowAll = (bool) DB::table('campaigns')
            ->where('id', $campaignId)
            ->value('allow_all_homebrew_spells');

        if ($allowAll) {
            return;
        }

        $query->where(
            function (
                Builder $builder
            ) use (
                $campaignId
            ) {
                $builder
                    ->where(
                        'spells.provider',
                        '<>',
                        '5etools-homebrew'
                    )
                    ->orWhereExists(
                        function (
                            Builder $subquery
                        ) use (
                            $campaignId
                        ) {
                            $subquery
                                ->selectRaw('1')
                                ->from(
                                    'campaign_homebrew_spell_sources as chss'
                                )
                                ->whereColumn(
                                    'chss.homebrew_spell_source_id',
                                    'spells.homebrew_spell_source_id'
                                )
                                ->where(
                                    'chss.campaign_id',
                                    $campaignId
                                );
                        }
                    );
            }
        );
    }

    private function applyCanonicalEdition(
        Builder $query
    ): void {
        $query->where(
            function (
                Builder $builder
            ) {
                $builder
                    ->where(
                        'spells.provider',
                        '<>',
                        '5etools'
                    )
                    ->orWhere(
                        'spells.source',
                        'XPHB'
                    )
                    ->orWhereNotExists(
                        function (
                            Builder $modern
                        ) {
                            $modern
                                ->selectRaw('1')
                                ->from(
                                    'spells as modern'
                                )
                                ->where(
                                    'modern.provider',
                                    '5etools'
                                )
                                ->where(
                                    'modern.source',
                                    'XPHB'
                                )
                                ->where(
                                    'modern.active',
                                    true
                                )
                                ->where(
                                    'modern.upstream_missing',
                                    false
                                )
                                ->whereColumn(
                                    'modern.name',
                                    'spells.name'
                                );
                        }
                    );
            }
        );
    }
}
