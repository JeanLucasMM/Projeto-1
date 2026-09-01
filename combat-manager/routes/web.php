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
use App\Http\Controllers\CharacterPartyController;
use App\Http\Controllers\CharacterFeatureController;
use App\Http\Controllers\CharacterWalletController;
use App\Http\Controllers\CharacterCustomizationController;
use App\Http\Controllers\CharacterProgressionController;

use App\Http\Controllers\NpcController;
use App\Http\Controllers\NpcBuilderController;
use App\Http\Controllers\NpcBuilderDraftController;

use App\Http\Controllers\CombatController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\DiceRollController;
use App\Http\Middleware\EnsureDashboardMode;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignInvitationController;
use App\Http\Controllers\CampaignCharacterController;
use App\Http\Controllers\CampaignMasterController;


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


    
    Route::middleware([
        EnsureDashboardMode::class . ':player',
    ])->group(function () {

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


    Route::patch(
        '/characters/{character}/stats/{ability}',
        [CharacterSheetStatsController::class, 'updateAbility']
    )->name('characters.stats.ability.update');

    Route::patch(
        '/characters/{character}/customization',
        [
            CharacterCustomizationController::class,
            'update',
        ]
    )->name(
        'characters.customization.update'
    );


    Route::post(
        '/characters/{character}/customization/image',
        [
            CharacterCustomizationController::class,
            'updateImage',
        ]
    )->name(
        'characters.customization.image'
    );


    /*
    |--------------------------------------------------------------------------
    | ProgressÃ£o do Personagem
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/characters/{character}/progression/level-up',
        [
            CharacterProgressionController::class,
            'levelUp',
        ]
    )->name(
        'characters.progression.level-up'
    );

    Route::patch(
        '/characters/{character}/progression/level-down',
        [
            CharacterProgressionController::class,
            'levelDown',
        ]
    )->name(
        'characters.progression.level-down'
    );

    Route::patch(
        '/characters/{character}/progression/proficiency',
        [
            CharacterProgressionController::class,
            'updateProficiency',
        ]
    )->name(
        'characters.progression.proficiency'
    );


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


    /*
    |--------------------------------------------------------------------------
    | Party do Personagem
    |--------------------------------------------------------------------------
    |
    | Estas rotas ficam dentro do modo Player.
    | O CharacterPolicy continua sendo a autorização real e o controller
    | também exige Gate::authorize('update', $character).
    |
    */

    Route::get(
        '/characters/{character}/party',
        [CharacterPartyController::class, 'index']
    )->name('characters.party.index');

    Route::get(
        '/characters/{character}/party/states',
        [CharacterPartyController::class, 'states']
    )->name('characters.party.states');

    Route::get(
        '/characters/{character}/party/pokes',
        [CharacterPartyController::class, 'pokes']
    )->name('characters.party.pokes');

    Route::get(
        '/characters/{character}/party/{campaign}/members/{member}/image',
        [CharacterPartyController::class, 'memberImage']
    )->name('characters.party.members.image');

    Route::patch(
        '/characters/{character}/party/{campaign}/notes',
        [CharacterPartyController::class, 'updateNotes']
    )->name('characters.party.notes.update');

    Route::post(
        '/characters/{character}/party/{campaign}/poke',
        [CharacterPartyController::class, 'sendPoke']
    )->name('characters.party.poke');

    Route::post(
        '/characters/{character}/party/{campaign}/transfer-item',
        [CharacterPartyController::class, 'transferItem']
    )->name('characters.party.items.transfer');


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


    Route::post(
        '/characters/{character}/morquen/determination-final',
        [
            CharacterCombatController::class,
            'determinationFinal',
        ]
    )->name(
        'characters.morquen.determination-final'
    );


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




    
    }); // fim das rotas Player


    Route::middleware([
        EnsureDashboardMode::class . ':master,player',
    ])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Visualização da ficha
    |--------------------------------------------------------------------------
    |
    | Pode ser acessada tanto em modo Mestre quanto em modo Player.
    | A autorização da ficha continua sendo feita pelo CharacterPolicy
    | dentro do CharacterSheetController.
    |
    */

    Route::get(
        '/characters/{character}',
        [CharacterSheetController::class, 'show']
    )->name('characters.show');


    /*
    |--------------------------------------------------------------------------
    | Imagem de item na visualização da ficha
    |--------------------------------------------------------------------------
    |
    | A imagem também precisa ser acessível no modo Mestre para que o
    | inventário da ficha compartilhada seja exibido corretamente.
    |
    | O CharacterItemController valida a permissão de visualização.
    |
    */

    Route::get(
        '/characters/{character}/items/{item}/image',
        [CharacterItemController::class, 'image']
    )->name('characters.items.image');


/*
    |--------------------------------------------------------------------------
    | Campanhas
    |--------------------------------------------------------------------------
    |
    | Aceitar um convite não compartilha automaticamente todas as fichas.
    | O próprio jogador escolhe explicitamente quais Characters entram
    | em campaign_characters.
    |
    */

    Route::get(
        '/campaigns',
        [CampaignController::class, 'index']
    )->name('campaigns.index');

    Route::post(
        '/campaigns',
        [CampaignController::class, 'store']
    )->name('campaigns.store');

    Route::get(
        '/campaigns/{campaign}',
        [CampaignController::class, 'show']
    )->name('campaigns.show');

    Route::get(
        '/campaigns/{campaign}/master/live',
        [CampaignMasterController::class, 'live']
    )->name('campaigns.master.live');


    Route::get(
        '/campaigns/{campaign}/master/characters/{character}/image',
        [CampaignMasterController::class, 'characterImage']
    )->name('campaigns.master.characters.image');


    Route::delete(
        '/campaigns/{campaign}',
        [CampaignController::class, 'destroy']
    )->name('campaigns.destroy');


    /*
    |--------------------------------------------------------------------------
    | Convites de campanha
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/campaigns/{campaign}/invitations',
        [CampaignInvitationController::class, 'store']
    )->name('campaigns.invitations.store');

    Route::delete(
        '/campaigns/{campaign}/invitations/{invitation}',
        [CampaignInvitationController::class, 'destroy']
    )->name('campaigns.invitations.destroy');

    Route::post(
        '/campaign-invitations/{invitation}/accept',
        [CampaignInvitationController::class, 'accept']
    )->name('campaign-invitations.accept');

    Route::post(
        '/campaign-invitations/{invitation}/decline',
        [CampaignInvitationController::class, 'decline']
    )->name('campaign-invitations.decline');


    /*
    |--------------------------------------------------------------------------
    | Personagens compartilhados com a campanha
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/campaigns/{campaign}/characters',
        [CampaignCharacterController::class, 'store']
    )->name('campaigns.characters.store');

    Route::patch(
        '/campaigns/{campaign}/characters/{character}',
        [CampaignCharacterController::class, 'update']
    )->name('campaigns.characters.update');

    Route::delete(
        '/campaigns/{campaign}/characters/{character}',
        [CampaignCharacterController::class, 'destroy']
    )->name('campaigns.characters.destroy');


    
    }); // fim das rotas compartilhadas Mestre/Player


    Route::middleware([
        EnsureDashboardMode::class . ':master',
    ])->group(function () {

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

    Route::patch(
        '/combats/{combat}/campaign',
        [CombatController::class, 'updateCampaign']
    )->name('combats.campaign.update');


    Route::get(
        '/combats/{combat}/players/states',
        [CombatController::class, 'playerStates']
    )->name('combats.players.states');


    Route::post(
        '/combats/{combat}/players',
        [CombatController::class, 'addPlayer']
    )->name('combats.players.store');


    /*
    |--------------------------------------------------------------------------
    | Character compartilhada da campanha
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/combats/{combat}/characters',
        [CombatController::class, 'addCharacter']
    )->name('combats.characters.store');


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
    }); // fim das rotas Mestre

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