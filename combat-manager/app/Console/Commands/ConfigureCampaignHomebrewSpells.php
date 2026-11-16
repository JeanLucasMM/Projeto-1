<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConfigureCampaignHomebrewSpells extends Command
{
    protected $signature = 'campaign:homebrew-spells
                            {campaign : ID da campanha}
                            {--all : Autoriza todas as fontes homebrew}
                            {--none : Desautoriza todas as fontes homebrew}
                            {--enable=* : Autoriza uma ou mais fontes pelo source_code}
                            {--disable=* : Remove autorização de uma ou mais fontes pelo source_code}';

    protected $description = 'Configura quais fontes de feitiços homebrew uma campanha pode usar.';

    public function handle(): int
    {
        $campaignId = (int) $this->argument('campaign');

        $campaign = DB::table('campaigns')
            ->where('id', $campaignId)
            ->first(['id', 'name', 'allow_all_homebrew_spells']);

        if ($campaign === null) {
            $this->error("Campanha #{$campaignId} não encontrada.");

            return self::FAILURE;
        }

        $all = (bool) $this->option('all');
        $none = (bool) $this->option('none');
        $enable = $this->cleanCodes((array) $this->option('enable'));
        $disable = $this->cleanCodes((array) $this->option('disable'));

        if ($all && $none) {
            $this->error('Use --all ou --none, não ambos.');

            return self::FAILURE;
        }

        if ($all) {
            DB::transaction(function () use ($campaignId) {
                DB::table('campaigns')
                    ->where('id', $campaignId)
                    ->update([
                        'allow_all_homebrew_spells' => true,
                        'updated_at' => now(),
                    ]);

                DB::table('campaign_homebrew_spell_sources')
                    ->where('campaign_id', $campaignId)
                    ->delete();
            });

            $this->info(
                "Todas as fontes homebrew foram autorizadas para {$campaign->name}."
            );

            return self::SUCCESS;
        }

        if ($none) {
            DB::transaction(function () use ($campaignId) {
                DB::table('campaigns')
                    ->where('id', $campaignId)
                    ->update([
                        'allow_all_homebrew_spells' => false,
                        'updated_at' => now(),
                    ]);

                DB::table('campaign_homebrew_spell_sources')
                    ->where('campaign_id', $campaignId)
                    ->delete();
            });

            $this->info(
                "Todas as fontes homebrew foram desautorizadas para {$campaign->name}."
            );

            return self::SUCCESS;
        }

        if ($enable !== [] || $disable !== []) {
            $this->materializeAllowListIfNecessary(
                $campaignId,
                (bool) $campaign->allow_all_homebrew_spells
            );

            foreach ($enable as $code) {
                $source = $this->sourceByCode($code);

                if ($source === null) {
                    $this->warn("Fonte não encontrada: {$code}");
                    continue;
                }

                DB::table('campaign_homebrew_spell_sources')
                    ->updateOrInsert(
                        [
                            'campaign_id' => $campaignId,
                            'homebrew_spell_source_id' => $source->id,
                        ],
                        [
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );

                $this->line("✓ autorizada: {$source->source_code} — {$source->name}");
            }

            foreach ($disable as $code) {
                $source = $this->sourceByCode($code);

                if ($source === null) {
                    $this->warn("Fonte não encontrada: {$code}");
                    continue;
                }

                DB::table('campaign_homebrew_spell_sources')
                    ->where('campaign_id', $campaignId)
                    ->where('homebrew_spell_source_id', $source->id)
                    ->delete();

                $this->line("− removida: {$source->source_code} — {$source->name}");
            }
        }

        $this->showStatus($campaignId);

        return self::SUCCESS;
    }

    /**
     * Ao editar uma campanha que estava em "todos", convertemos esse estado
     * para uma whitelist explícita antes de remover/adicionar fontes.
     */
    private function materializeAllowListIfNecessary(
        int $campaignId,
        bool $wasAllowAll
    ): void {
        if (! $wasAllowAll) {
            return;
        }

        DB::transaction(function () use ($campaignId) {
            $sources = DB::table('homebrew_spell_sources')
                ->where('provider', '5etools-homebrew')
                ->where('is_available', true)
                ->pluck('id');

            foreach ($sources as $sourceId) {
                DB::table('campaign_homebrew_spell_sources')
                    ->updateOrInsert(
                        [
                            'campaign_id' => $campaignId,
                            'homebrew_spell_source_id' => $sourceId,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
            }

            DB::table('campaigns')
                ->where('id', $campaignId)
                ->update([
                    'allow_all_homebrew_spells' => false,
                    'updated_at' => now(),
                ]);
        });
    }

    private function sourceByCode(string $code): ?object
    {
        return DB::table('homebrew_spell_sources')
            ->where('provider', '5etools-homebrew')
            ->whereRaw('LOWER(source_code) = ?', [
                mb_strtolower($code, 'UTF-8'),
            ])
            ->first(['id', 'source_code', 'name']);
    }

    private function showStatus(int $campaignId): void
    {
        $campaign = DB::table('campaigns')
            ->where('id', $campaignId)
            ->first(['id', 'name', 'allow_all_homebrew_spells']);

        $this->newLine();
        $this->info(
            "Campanha: {$campaign->name} (#{$campaign->id})"
        );

        if ($campaign->allow_all_homebrew_spells) {
            $this->line('Homebrew: TODAS AS FONTES AUTORIZADAS');

            return;
        }

        $sources = DB::table('campaign_homebrew_spell_sources as chss')
            ->join(
                'homebrew_spell_sources as hss',
                'hss.id',
                '=',
                'chss.homebrew_spell_source_id'
            )
            ->where('chss.campaign_id', $campaignId)
            ->orderBy('hss.name')
            ->get([
                'hss.source_code',
                'hss.name',
                'hss.spell_count',
            ]);

        if ($sources->isEmpty()) {
            $this->line('Homebrew: nenhuma fonte autorizada');

            return;
        }

        $this->table(
            ['Código', 'Fonte', 'Feitiços'],
            $sources->map(fn ($source) => [
                $source->source_code,
                $source->name ?: $source->source_code,
                (int) $source->spell_count,
            ])->all()
        );
    }

    /**
     * @param  array<int, mixed>  $values
     * @return array<int, string>
     */
    private function cleanCodes(array $values): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn ($value) => trim((string) $value),
            $values
        ))));
    }
}
