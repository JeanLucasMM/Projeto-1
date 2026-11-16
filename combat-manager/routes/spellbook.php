<?php

use App\Http\Controllers\CharacterSpellbookController;
use App\Http\Controllers\MasterCombatSpellSearchController;
use App\Http\Controllers\MasterCampaignSettingsController;
use App\Http\Controllers\MasterSpellSourceController;
use App\Http\Controllers\PartyConcentrationController;
use App\Http\Middleware\EnsureDashboardMode;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Configurações do Mestre
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    EnsureDashboardMode::class . ':master',
])->group(function () {
    Route::get('/master/spells/lookup', [MasterCombatSpellSearchController::class, 'index'])
        ->name('master.spells.lookup');

    // V66.0.41 — seleção múltipla, com o lado definido em cada NPC adicionado.
    Route::post(
        '/combats/{combat}/npcs/batch',
        [\App\Http\Controllers\CombatNpcBatchController::class, 'store']
    )->name('master.combats.npcs.batch');

    Route::get(
        '/master/campaigns/{campaign}/settings',
        [MasterCampaignSettingsController::class, 'index']
    )->name('master.campaign.settings.index');

    Route::put(
        '/master/campaigns/{campaign}/spells/sources',
        [MasterSpellSourceController::class, 'update']
    )->name('master.spells.sources.update');

    Route::get(
        '/master/campaigns/{campaign}/spells/sources',
        function ($campaign) {
            return redirect()->route(
                'master.campaign.settings.index',
                ['campaign' => $campaign]
            );
        }
    )->name('master.spells.sources.index');
});

/*
|--------------------------------------------------------------------------
| Grimório do Player
|--------------------------------------------------------------------------
|
| Os endpoints continuam no CharacterSpellbookController existente.
| Confirmações visuais sobre substituir concentração pertencem ao front-end;
| a exclusividade da concentração é garantida pelo serviço no backend.
*/
Route::middleware(['auth'])->group(function () {
    Route::get(
        '/characters/{character}/spellbook/state',
        [CharacterSpellbookController::class, 'state']
    )->name('characters.spellbook.state');

    Route::get(
        '/characters/{character}/spellbook/catalog',
        [CharacterSpellbookController::class, 'catalog']
    )->name('characters.spellbook.catalog');

    Route::post(
        '/characters/{character}/spellbook/spells/{spell}',
        [CharacterSpellbookController::class, 'storeSpell']
    )->name('characters.spellbook.spells.store');

    Route::patch(
        '/characters/{character}/spellbook/spells/{spell}',
        [CharacterSpellbookController::class, 'updateSpell']
    )->name('characters.spellbook.spells.update');

    Route::delete(
        '/characters/{character}/spellbook/spells/{spell}',
        [CharacterSpellbookController::class, 'destroySpell']
    )->name('characters.spellbook.spells.destroy');

    /*
    | Imagem pessoal (V13) + exibição autenticada no Grimório (V15).
    | O GET serve imagens sem depender da URL definida em APP_URL.
    */
    Route::get(
        '/characters/{character}/spellbook/spells/{spell}/media',
        [CharacterSpellbookController::class, 'showSpellMedia']
    )->name('characters.spellbook.spells.media.show');

    Route::post(
        '/characters/{character}/spellbook/spells/{spell}/media',
        [CharacterSpellbookController::class, 'uploadSpellMedia']
    )->name('characters.spellbook.spells.media.store');

    Route::delete(
        '/characters/{character}/spellbook/spells/{spell}/media',
        [CharacterSpellbookController::class, 'deleteSpellMedia']
    )->name('characters.spellbook.spells.media.destroy');

    Route::post(
        '/characters/{character}/spellbook/profiles',
        [CharacterSpellbookController::class, 'storeProfile']
    )->name('characters.spellbook.profiles.store');

    Route::post(
        '/characters/{character}/spellbook/profiles/import-sheet',
        [CharacterSpellbookController::class, 'importProfilesFromSheet']
    )->name('characters.spellbook.profiles.import-sheet');

    Route::patch(
        '/characters/{character}/spellbook/profiles/{profile}',
        [CharacterSpellbookController::class, 'updateProfile']
    )->name('characters.spellbook.profiles.update');

    Route::delete(
        '/characters/{character}/spellbook/profiles/{profile}',
        [CharacterSpellbookController::class, 'destroyProfile']
    )->name('characters.spellbook.profiles.destroy');

    Route::patch(
        '/characters/{character}/spellbook/slots',
        [CharacterSpellbookController::class, 'updateSlot']
    )->name('characters.spellbook.slots.update');

    Route::post(
        '/characters/{character}/spellbook/spells/{spell}/cast',
        [CharacterSpellbookController::class, 'cast']
    )->name('characters.spellbook.spells.cast');

    Route::post(
        '/characters/{character}/spellbook/concentration-check',
        [CharacterSpellbookController::class, 'concentrationCheck']
    )->name('characters.spellbook.concentration.check');

    Route::delete(
        '/characters/{character}/spellbook/concentration',
        [CharacterSpellbookController::class, 'stopConcentration']
    )->name('characters.spellbook.concentration.destroy');

    // Estado das concentrações dos aliados para a gaveta Party.
    Route::get(
        '/characters/{character}/spellbook/party-concentrations',
        [PartyConcentrationController::class, 'index']
    )->name('characters.party.concentrations');

    Route::post(
        '/characters/{character}/spellbook/loadouts',
        [CharacterSpellbookController::class, 'storeLoadout']
    )->name('characters.spellbook.loadouts.store');

    Route::patch(
        '/characters/{character}/spellbook/loadouts/{loadout}',
        [CharacterSpellbookController::class, 'updateLoadout']
    )->name('characters.spellbook.loadouts.update');

    Route::post(
        '/characters/{character}/spellbook/loadouts/{loadout}/overwrite',
        [CharacterSpellbookController::class, 'overwriteLoadout']
    )->name('characters.spellbook.loadouts.overwrite');

    Route::post(
        '/characters/{character}/spellbook/loadouts/{loadout}/apply',
        [CharacterSpellbookController::class, 'applyLoadout']
    )->name('characters.spellbook.loadouts.apply');

    Route::delete(
        '/characters/{character}/spellbook/loadouts/{loadout}',
        [CharacterSpellbookController::class, 'destroyLoadout']
    )->name('characters.spellbook.loadouts.destroy');
});

// Aliados NPC ativos para a gaveta Party. O controller verifica a ficha e a campanha.
Route::middleware(['auth'])->get(
    '/characters/{character}/party/combat-allies',
    [\App\Http\Controllers\CharacterPartyCombatAlliesController::class, 'index']
)->name('characters.party.combat-allies');

/* V66.0.49 — Arquivo de batalhas do mestre; mantido separado do Grimório. */
Route::middleware(['auth', EnsureDashboardMode::class . ':master'])->group(function (): void {
    Route::post('/master/combat-archives/{combat}/clock', [\App\Http\Controllers\CombatArchiveController::class, 'clock'])
        ->name('master.combat.archives.clock');
    Route::get('/master/combat-archives', [\App\Http\Controllers\CombatArchiveController::class, 'index'])
        ->name('master.combat.archives.index');
    Route::post('/master/combat-archives/{combat}/save', [\App\Http\Controllers\CombatArchiveController::class, 'save'])
        ->name('master.combat.archives.save');
    Route::post('/master/combats/{combat}/rename', [\App\Http\Controllers\CombatArchiveController::class, 'rename'])
        ->name('master.combats.rename');
    Route::delete('/master/combat-archives/{archive}', [\App\Http\Controllers\CombatArchiveController::class, 'destroy'])
        ->name('master.combat.archives.destroy');
    Route::post('/master/combat-archives/{archive}/restore-portraits', [\App\Http\Controllers\CombatArchiveController::class, 'restorePortraits'])
        ->name('master.combat.archives.restore-portraits');
    Route::get('/master/combat-archives/images/{archive}', [\App\Http\Controllers\CombatArchiveController::class, 'image'])
        ->name('master.combat.archives.image');
});
