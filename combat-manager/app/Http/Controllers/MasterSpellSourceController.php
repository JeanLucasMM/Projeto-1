<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\HomebrewSpellSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterSpellSourceController extends Controller
{
    public function update(
        Request $request,
        Campaign $campaign
    ): RedirectResponse {
        abort_unless(
            $campaign->isOwner($request->user()),
            403
        );

        $validated = $request->validate([
            'allow_all_homebrew_spells' => [
                'nullable',
                'boolean',
            ],
            'source_ids' => [
                'nullable',
                'array',
            ],
            'source_ids.*' => [
                'integer',
                'distinct',
            ],
        ]);

        $allowAll =
            $request->boolean(
                'allow_all_homebrew_spells'
            );

        $requestedSourceIds = collect(
            $validated['source_ids'] ?? []
        )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $validSourceIds = HomebrewSpellSource::query()
            ->where('provider', '5etools-homebrew')
            ->where('is_available', true)
            ->whereIn('id', $requestedSourceIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        DB::transaction(
            function () use (
                $campaign,
                $allowAll,
                $validSourceIds,
                $request
            ): void {
                DB::table('campaigns')
                    ->where('id', $campaign->id)
                    ->update([
                        'allow_all_homebrew_spells' =>
                            $allowAll,
                        'updated_at' =>
                            now(),
                    ]);

                DB::table(
                    'campaign_homebrew_spell_sources'
                )
                    ->where(
                        'campaign_id',
                        $campaign->id
                    )
                    ->delete();

                if ($allowAll) {
                    return;
                }

                $now = now();

                foreach ($validSourceIds as $sourceId) {
                    DB::table(
                        'campaign_homebrew_spell_sources'
                    )->insert([
                        'campaign_id' =>
                            $campaign->id,

                        'homebrew_spell_source_id' =>
                            $sourceId,

                        'enabled_by_user_id' =>
                            $request->user()?->id,

                        'created_at' =>
                            $now,

                        'updated_at' =>
                            $now,
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'master.campaign.settings.index',
                $campaign
            )
            ->with(
                'success',
                $allowAll
                    ? 'Todos os homebrews foram autorizados para esta campanha.'
                    : 'As fontes homebrew autorizadas foram atualizadas.'
            );
    }
}
