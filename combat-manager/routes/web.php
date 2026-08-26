<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterSheetController;
use App\Http\Controllers\CharacterCombatController;
use App\Http\Controllers\CharacterRestController;
use App\Http\Controllers\CharacterSheetStatsController;
use App\Http\Controllers\CharacterAttackController;
use App\Http\Controllers\CharacterItemController;
use App\Http\Controllers\CharacterFeatureController;
use App\Http\Controllers\CharacterWalletController;

use App\Http\Controllers\NpcController;
use App\Http\Controllers\NpcBuilderController;
use App\Http\Controllers\NpcBuilderDraftController;

use App\Http\Controllers\CombatController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\DiceRollController;


/*
|--------------------------------------------------------------------------
| PÃ¡gina inicial
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| Antes havia duas rotas /dashboard.
| Agora usamos somente o DashboardController.
|
*/

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)
    ->middleware([
        'auth',
        'verified',
    ])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Rotas autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/dashboard/mode',
        [DashboardController::class, 'setMode']
    )->name('dashboard.mode');

    Route::post(
        '/dashboard/mode/clear',
        [DashboardController::class, 'clearMode']
    )->name('dashboard.mode.clear');


    /*
    |--------------------------------------------------------------------------
    | Personagens
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/characters',
        [CharacterController::class, 'index']
    )->name('characters.index');


    Route::post(
        '/characters',
        [CharacterController::class, 'store']
    )->name('characters.store');


    Route::delete(
        '/characters/{character}',
        [CharacterController::class, 'destroy']
    )->name('characters.destroy');


    Route::get(
        '/characters/{character}',
        [CharacterSheetController::class, 'show']
    )->name('characters.show');

    Route::patch(
        '/characters/{character}/stats/{ability}',
        [CharacterSheetStatsController::class, 'updateAbility']
    )->name('characters.stats.ability.update');




    /*
    |--------------------------------------------------------------------------
    | Ataques Personalizados
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/characters/{character}/attacks',
        [CharacterAttackController::class, 'store']
    )->name('characters.attacks.store');

    Route::patch(
        '/characters/{character}/attacks/{attack}',
        [CharacterAttackController::class, 'update']
    )->name('characters.attacks.update');

    Route::delete(
        '/characters/{character}/attacks/{attack}',
        [CharacterAttackController::class, 'destroy']
    )->name('characters.attacks.destroy');

    Route::patch(
        '/characters/{character}/attacks/{attack}/uses',
        [CharacterAttackController::class, 'updateUses']
    )->name('characters.attacks.uses.update');


    /*
    |--------------------------------------------------------------------------
    | Habilidades do Personagem
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/characters/{character}/features',
        [CharacterFeatureController::class, 'store']
    )->name('characters.features.store');

    Route::patch(
        '/characters/{character}/features/{feature}',
        [CharacterFeatureController::class, 'update']
    )->name('characters.features.update');

    Route::delete(
        '/characters/{character}/features/{feature}',
        [CharacterFeatureController::class, 'destroy']
    )->name('characters.features.destroy');

    Route::patch(
        '/characters/{character}/features/{feature}/uses',
        [CharacterFeatureController::class, 'updateUses']
    )->name('characters.features.uses.update');


    /*
    |--------------------------------------------------------------------------
    | InventÃ¡rio do Personagem
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/characters/{character}/items',
        [CharacterItemController::class, 'store']
    )->name('characters.items.store');

    Route::get(
        '/characters/{character}/items/{item}/image',
        [CharacterItemController::class, 'image']
    )->name('characters.items.image');

    Route::patch(
        '/characters/{character}/items/{item}',
        [CharacterItemController::class, 'update']
    )->name('characters.items.update');

    Route::delete(
        '/characters/{character}/items/{item}',
        [CharacterItemController::class, 'destroy']
    )->name('characters.items.destroy');

    Route::patch(
        '/characters/{character}/items/{item}/equipped',
        [CharacterItemController::class, 'updateEquipped']
    )->name('characters.items.equipped.update');

    Route::patch(
        '/characters/{character}/items/{item}/attunement',
        [CharacterItemController::class, 'updateAttuned']
    )->name('characters.items.attunement.update');


    Route::patch(
        '/characters/{character}/items/{item}/features/{feature}/uses',
        [CharacterItemController::class, 'updateFeatureUses']
    )->name('characters.items.features.uses.update');


    Route::patch(
        '/characters/{character}/wallet',
        [CharacterWalletController::class, 'update']
    )->name('characters.wallet.update');


    /*
    |--------------------------------------------------------------------------
    | Combate do Personagem
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/characters/{character}/combat',
        [CharacterCombatController::class, 'update']
    )->name('characters.combat.update');


    /*
    |--------------------------------------------------------------------------
    | Descanso do Personagem
    |--------------------------------------------------------------------------
    |
    | Recebe:
    |
    | {
    |     "type": "short"
    | }
    |
    | ou:
    |
    | {
    |     "type": "long"
    | }
    |
    */

    Route::post(
        '/characters/{character}/rest',
        [CharacterRestController::class, 'store']
    )->name('characters.rest');


    /*
    |--------------------------------------------------------------------------
    | NPCs
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/npcs',
        [NpcController::class, 'index']
    )->name('npcs.index');


    Route::get(
        '/npcs/{id}',
        [NpcController::class, 'show']
    )->name('npcs.show');


    Route::post(
        '/npc/import',
        [NpcController::class, 'import']
    )->name('npc.import');


    Route::post(
        '/npcs',
        [NpcController::class, 'store']
    )->name('npc.store');


    Route::delete(
        '/npcs/{id}',
        [NpcController::class, 'destroy']
    )->name('npcs.destroy');


    Route::patch(
        '/npcs/{npc}/move-folder',
        [NpcController::class, 'moveFolder']
    )->name('npcs.move-folder');


    Route::patch(
        '/npcs/{npc}/remove-folder',
        [NpcController::class, 'removeFromFolder']
    )->name('npcs.remove-folder');


    Route::patch(
        '/npcs/{npc}/toggle-deceased',
        [NpcController::class, 'toggleDeceased']
    )->name('npcs.toggle-deceased');


    Route::get(
        '/npcs/{npc}/download-json',
        [NpcController::class, 'downloadJson']
    )->name('npcs.download-json');


    /*
    |--------------------------------------------------------------------------
    | NPC Builder
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/npc-builder',
        [NpcBuilderController::class, 'index']
    )->name('npc-builder.index');


    Route::post(
        '/npc-builder/preview',
        [NpcBuilderController::class, 'preview']
    )->name('npc-builder.preview');


    /*
    |--------------------------------------------------------------------------
    | NPC Builder Draft
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/npc-builder/draft',
        [NpcBuilderDraftController::class, 'show']
    )->name('npc-builder.draft.show');


    Route::post(
        '/npc-builder/draft',
        [NpcBuilderDraftController::class, 'store']
    )->name('npc-builder.draft.store');


    Route::delete(
        '/npc-builder/draft',
        [NpcBuilderDraftController::class, 'destroy']
    )->name('npc-builder.draft.destroy');


    /*
    |--------------------------------------------------------------------------
    | Combates
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'combats',
        CombatController::class
    )->except([
        'edit',
        'update',
    ]);


    Route::post(
        '/combats/{combat}/start',
        [CombatController::class, 'start']
    )->name('combats.start');


    Route::post(
        '/combats/{combat}/reset',
        [CombatController::class, 'reset']
    )->name('combats.reset');


    Route::post(
        '/combats/{combat}/next',
        [CombatController::class, 'next']
    )->name('combats.next');


    /*
    |--------------------------------------------------------------------------
    | NPCs dentro do combate
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/combats/{combat}/npcs',
        [CombatController::class, 'addNpc']
    )->name('combats.npcs.store');


    Route::delete(
        '/combats/{combat}/npcs/{combatNpc}',
        [CombatController::class, 'removeNpc']
    )->name('combats.npcs.destroy');


    Route::put(
        '/combats/{combat}/npcs/{combatNpc}',
        [CombatController::class, 'updateInitiative']
    )->name('combats.npcs.update-initiative');


    Route::patch(
        '/combats/{combat}/npcs/{combatNpc}/initiative',
        [CombatController::class, 'updateInitiative']
    )->name('combats.npcs.initiative');


    Route::patch(
        '/combats/{combat}/npcs/{combatNpc}/damage',
        [CombatController::class, 'damageNpc']
    )->name('combats.npcs.damage');


    Route::patch(
        '/combats/{combat}/npcs/{combatNpc}/heal',
        [CombatController::class, 'healNpc']
    )->name('combats.npcs.heal');


    Route::patch(
        '/combats/{combat}/npcs/{combatNpc}/temporary-hp',
        [CombatController::class, 'temporaryHp']
    )->name('combats.npcs.temporaryHp');


    Route::post(
        '/combat/npc/update-resource',
        [CombatController::class, 'updateResource']
    );


    /*
    |--------------------------------------------------------------------------
    | Jogadores dentro do combate
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/combats/{combat}/players',
        [CombatController::class, 'addPlayer']
    )->name('combats.players.store');


    Route::delete(
        '/combats/{combat}/players/{player}',
        [CombatController::class, 'removePlayer']
    )->name('combats.players.destroy');


    Route::patch(
        '/combats/{combat}/players/{player}/initiative',
        [CombatController::class, 'updatePlayerInitiative']
    )->name('combats.players.initiative');


    /*
    |--------------------------------------------------------------------------
    | Folders
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'folders',
        FolderController::class
    )->only([
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ]);
});


/*
|--------------------------------------------------------------------------
| Dice Roller
|--------------------------------------------------------------------------
*/

Route::post(
    '/api/roll',
    [DiceRollController::class, 'roll']
);


/*
|--------------------------------------------------------------------------
| AutenticaÃ§Ã£o
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';