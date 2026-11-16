<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\HomebrewSpellSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterCampaignSettingsController extends Controller
{
    public function index(
        Request $request,
        Campaign $campaign
    ): View {
        abort_unless(
            $campaign->isOwner($request->user()),
            403
        );

        $sources = HomebrewSpellSource::query()
            ->where('provider', '5etools-homebrew')
            ->where('is_available', true)
            ->orderByRaw("CASE WHEN name IS NULL OR name = '' THEN 1 ELSE 0 END")
            ->orderBy('name')
            ->orderBy('source_code')
            ->get([
                'id',
                'source_code',
                'name',
                'authors',
                'url',
                'spell_count',
            ]);

        $enabledSourceIds = DB::table('campaign_homebrew_spell_sources')
            ->where('campaign_id', $campaign->id)
            ->pluck('homebrew_spell_source_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $officialSpellCount = DB::table('spells')
            ->where('provider', '5etools')
            ->where('active', true)
            ->where('upstream_missing', false)
            ->count();

        $homebrewSpellCount = DB::table('spells')
            ->where('provider', '5etools-homebrew')
            ->where('active', true)
            ->where('upstream_missing', false)
            ->count();

        $enabledHomebrewSpellCount =
            (bool) $campaign->allow_all_homebrew_spells
                ? $homebrewSpellCount
                : DB::table('spells')
                    ->where('provider', '5etools-homebrew')
                    ->where('active', true)
                    ->where('upstream_missing', false)
                    ->whereIn(
                        'homebrew_spell_source_id',
                        $enabledSourceIds
                    )
                    ->count();

        return view(
            'campaigns.master.settings',
            compact(
                'campaign',
                'sources',
                'enabledSourceIds',
                'officialSpellCount',
                'homebrewSpellCount',
                'enabledHomebrewSpellCount'
            )
        );
    }
}
