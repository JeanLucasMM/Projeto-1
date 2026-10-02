<?php


use Illuminate\Support\Facades\Route;


use App\Http\Controllers\ProfileController;


use App\Http\Controllers\DashboardController;


use App\Http\Controllers\CharacterController;


use App\Http\Controllers\CharacterSheetController;


use App\Http\Controllers\CharacterCombatController;


use App\Http\Controllers\CharacterCombatInvitationController;


use App\Http\Controllers\CharacterRestController;


use App\Http\Controllers\CharacterSheetStatsController;


use App\Http\Controllers\CharacterAttackController;


use App\Http\Controllers\CharacterItemController;


use App\Http\Controllers\CharacterPartyController;


use App\Http\Controllers\CharacterFeatureController;


use App\Http\Controllers\CharacterWalletController;


use App\Http\Controllers\CharacterCustomizationController;


use App\Http\Controllers\CharacterProgressionController;


use App\Http\Controllers\CharacterTransformationController;


use App\Http\Controllers\NpcController;


use App\Http\Controllers\NpcBuilderController;


use App\Http\Controllers\NpcBuilderDraftController;


use App\Http\Controllers\CombatController;


use App\Http\Controllers\FolderController;


use App\Http\Controllers\WorkspaceFolderController;


use App\Http\Controllers\DiceRollController;


use App\Http\Middleware\EnsureDashboardMode;


use App\Http\Controllers\CampaignController;


use App\Http\Controllers\CampaignInvitationController;


use App\Http\Controllers\CampaignCharacterController;


use App\Http\Controllers\CampaignMasterController;


use App\Http\Controllers\MasterVaultController;


use App\Http\Controllers\MasterLootController;


use App\Http\Controllers\CharacterFoundLootController;


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


    | Transformações do Personagem


    |--------------------------------------------------------------------------


    */


    Route::post(


        '/characters/{character}/transformations',


        [


            CharacterTransformationController::class,


            'store',


        ]


    )->name(


        'characters.transformations.store'


    );


    Route::patch(


        '/characters/{character}/transformations/{transformation}',


        [


            CharacterTransformationController::class,


            'update',


        ]


    )->name(


        'characters.transformations.update'


    );


    Route::delete(


        '/characters/{character}/transformations/{transformation}',


        [


            CharacterTransformationController::class,


            'destroy',


        ]


    )->name(


        'characters.transformations.destroy'


    );


    Route::post(


        '/characters/{character}/transformations/{transformation}/sheet',


        [


            CharacterTransformationController::class,


            'prepareCharacterSheet',


        ]


    )->name(


        'characters.transformations.sheet.prepare'


    );


    Route::post(


        '/characters/{character}/transformations/{transformation}/activate',


        [


            CharacterTransformationController::class,


            'activate',


        ]


    )->name(


        'characters.transformations.activate'


    );


    Route::post(


        '/characters/{character}/transformations/deactivate',


        [


            CharacterTransformationController::class,


            'deactivate',


        ]


    )->name(


        'characters.transformations.deactivate'


    );


    Route::post(


        '/characters/{character}/transformations/concentration-check',


        [


            CharacterTransformationController::class,


            'concentrationCheck',


        ]


    )->name(


        'characters.transformations.concentration.check'


    );


    Route::post(


        '/characters/{character}/transformations/monster/damage',


        [


            CharacterTransformationController::class,


            'damageMonster',


        ]


    )->name(


        'characters.transformations.monster.damage'


    );


    Route::post(


        '/characters/{character}/transformations/monster/heal',


        [


            CharacterTransformationController::class,


            'healMonster',


        ]


    )->name(


        'characters.transformations.monster.heal'


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


    | Itens Encontrados / Espólios do Personagem


    |--------------------------------------------------------------------------


    |


    | O backend filtra os itens e moedas pela campanha ativa e pela visibilidade


    | definida pelo Mestre. Itens privados nunca são enviados a personagens que


    | não estejam entre os destinatários.


    |


    */


    Route::get(


        '/characters/{character}/found-loot',


        [CharacterFoundLootController::class, 'index']


    )->name('characters.found-loot.index');


    Route::post(


        '/characters/{character}/found-loot/items/{lootItem}/claim',


        [CharacterFoundLootController::class, 'claimItem']


    )->name('characters.found-loot.items.claim');


    Route::get(


        '/characters/{character}/found-loot/items/{lootItem}/image',


        [CharacterFoundLootController::class, 'itemImage']


    )->name('characters.found-loot.items.image');


    Route::post(


        '/characters/{character}/found-loot/currency/{currency}/claim',


        [CharacterFoundLootController::class, 'claimCurrency']


    )->name('characters.found-loot.currency.claim');


    /*


    |--------------------------------------------------------------------------


    | Lojas do Personagem


    |--------------------------------------------------------------------------


    |


    | Estas rotas já faziam parte do fluxo de loja do Player.


    | Elas precisam permanecer dentro do grupo Player porque o show\\.blade


    | usa characters.shops.index para consultar lojas abertas.


    |


    */


    Route::get(


        '/characters/{character}/shops',


        [CharacterFoundLootController::class, 'shops']


    )->name('characters.shops.index');


    Route::post(


        '/characters/{character}/shops/items/{lootItem}/purchase',


        [CharacterFoundLootController::class, 'purchaseShopItem']


    )->name('characters.shops.items.purchase');


    Route::get(


        '/characters/{character}/shops/{lootDrop}/image',


        [CharacterFoundLootController::class, 'shopImage']


    )->name('characters.shops.image');


    Route::get(


        '/characters/{character}/shops/{lootDrop}/background',


        [CharacterFoundLootController::class, 'shopBackgroundImage']


    )->name('characters.shops.background');


    Route::get(


        '/characters/{character}/shops/items/{lootItem}/image',


        [CharacterFoundLootController::class, 'shopItemImage']


    )->name('characters.shops.items.image');


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


    | Chamado de Combate da Campanha


    |--------------------------------------------------------------------------


    |


    | A rota está no grupo compartilhado porque a ficha também pode ser aberta


    | nos dois modos. O controller só permite resposta do dono do Character.


    |


    */


    Route::get(


        '/characters/{character}/combat-call',


        [


            CharacterCombatInvitationController::class,


            'show',


        ]


    )->name(


        'characters.combat-call.show'


    );


    Route::post(


        '/characters/{character}/combat-call/respond',


        [


            CharacterCombatInvitationController::class,


            'respond',


        ]


    )->name(


        'characters.combat-call.respond'


    );


    Route::post(


        '/characters/{character}/combat-call/initiative',


        [


            CharacterCombatInvitationController::class,


            'initiative',


        ]


    )->name(


        'characters.combat-call.initiative'


    );


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


    Route::patch(


        '/campaigns/{campaign}/master/notes',


        [CampaignMasterController::class, 'updateNotes']


    )->name('campaigns.master.notes.update');


    Route::patch(


        '/campaigns/{campaign}/master/settings',


        [CampaignMasterController::class, 'updateSettings']


    )->name('campaigns.master.settings.update');


    Route::get(


        '/campaigns/{campaign}/master/characters/{character}/image',


        [CampaignMasterController::class, 'characterImage']


    )->name('campaigns.master.characters.image');


    /*


    |--------------------------------------------------------------------------


    | Exclusão segura de campanha


    |--------------------------------------------------------------------------


    |


    | Não existe mais DELETE /campaigns/{campaign}.


    | A exclusão exige uma página de confirmação dedicada, uma rota específica


    | e confirmação textual. O controller usa SoftDeletes.


    |


    */


    Route::get(


        '/campaigns/{campaign}/danger/delete',


        [CampaignController::class, 'confirmDestroy']


    )->name('campaigns.destroy.confirm');


    Route::post(


        '/campaigns/{campaign}/danger/delete',


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


    | Party da campanha — ações exclusivas do Mestre


    |--------------------------------------------------------------------------


    */


    Route::patch(


        '/master/campaigns/{campaign}/members/{user}/visibility',


        [CampaignMasterController::class, 'updateMemberVisibility']


    )->name('campaigns.master.members.visibility');


    Route::delete(


        '/master/campaigns/{campaign}/members/{user}',


        [CampaignMasterController::class, 'removeMember']


    )->name('campaigns.master.members.destroy');


    /*


    |--------------------------------------------------------------------------


    | Vault do Mestre


    |--------------------------------------------------------------------------


    |


    | Biblioteca permanente do Mestre. Os itens do Vault são reutilizáveis e


    | independentes das campanhas. Ao entrar em um Espólio, o item é copiado


    | como snapshot para preservar a versão usada naquela sessão.


    |


    */


    Route::get(


        '/master/vault/items',


        [MasterVaultController::class, 'index']


    )->name('master.vault.items.index');


    Route::post(


        '/master/vault/items',


        [MasterVaultController::class, 'store']


    )->name('master.vault.items.store');


    Route::post(


        '/master/vault/items/{vaultItem}',


        [MasterVaultController::class, 'update']


    )->name('master.vault.items.update');


    Route::post(


        '/master/vault/items/{vaultItem}/duplicate',


        [MasterVaultController::class, 'duplicate']


    )->name('master.vault.items.duplicate');


    Route::delete(


        '/master/vault/items/{vaultItem}',


        [MasterVaultController::class, 'destroy']


    )->name('master.vault.items.destroy');


    Route::get(


        '/master/vault/items/{vaultItem}/image',


        [MasterVaultController::class, 'image']


    )->name('master.vault.items.image');


    /*


    |--------------------------------------------------------------------------


    | Espólios do Mestre


    |--------------------------------------------------------------------------


    |


    | Cada Espólio pertence a uma campanha e pode opcionalmente registrar um


    | combate de origem. Itens e moedas podem ser públicos para a Party ou


    | restritos a personagens específicos.


    |


    */


    Route::get(


        '/master/campaigns/{campaign}/loot',


        [MasterLootController::class, 'index']


    )->name('master.loot.index');


    Route::post(


        '/master/campaigns/{campaign}/loot',


        [MasterLootController::class, 'storeDrop']


    )->name('master.loot.store');


    Route::post(

        '/master/campaigns/{campaign}/loot/{lootDrop}/presentation',

        [MasterLootController::class, 'updateDropPresentation']

    )->name('master.loot.presentation.update');



    Route::get(

        '/master/campaigns/{campaign}/loot/{lootDrop}/image',

        [MasterLootController::class, 'dropImage']

    )->name('master.loot.image');


    Route::delete(



        '/master/campaigns/{campaign}/loot/{lootDrop}',



        [MasterLootController::class, 'destroyDrop']



    )->name('master.loot.destroy');



    Route::post(


        '/master/campaigns/{campaign}/loot/quick-release',


        [MasterLootController::class, 'quickReleaseItem']


    )->name('master.loot.quick-release');


    Route::post(


        '/master/campaigns/{campaign}/loot/quick-currency-release',


        [MasterLootController::class, 'quickReleaseCurrency']


    )->name('master.loot.quick-currency-release');


    /*


    |--------------------------------------------------------------------------


    | Lojas do Mestre


    |--------------------------------------------------------------------------


    */


    Route::post(


        '/master/campaigns/{campaign}/shops',


        [MasterLootController::class, 'storeShop']


    )->name('master.shops.store');


    Route::get(


        '/master/campaigns/{campaign}/shops/npcs/{npc}/image',


        [MasterLootController::class, 'shopkeeperImage']


    )->name('master.shops.npcs.image');


    Route::post(


        '/master/campaigns/{campaign}/shops/{lootDrop}/open',


        [MasterLootController::class, 'openShop']


    )->name('master.shops.open');


    Route::post(


        '/master/campaigns/{campaign}/shops/{lootDrop}/close',


        [MasterLootController::class, 'closeDrop']


    )->name('master.shops.close');


    Route::post(


        '/master/campaigns/{campaign}/shops/{lootDrop}/presentation',


        [MasterLootController::class, 'updateShopPresentation']


    )->name('master.shops.presentation.update');


    Route::get(


        '/master/campaigns/{campaign}/shops/{lootDrop}/image',


        [MasterLootController::class, 'shopImage']


    )->name('master.shops.image');


    Route::get(


        '/master/campaigns/{campaign}/shops/{lootDrop}/background',


        [MasterLootController::class, 'shopBackgroundImage']


    )->name('master.shops.background');


    Route::post(


        '/master/campaigns/{campaign}/shops/{lootDrop}/items',


        [MasterLootController::class, 'addShopItem']


    )->name('master.shops.items.store');


    Route::patch(


        '/master/campaigns/{campaign}/shops/{lootDrop}/items/{lootItem}',


        [MasterLootController::class, 'updateShopItem']


    )->name('master.shops.items.update');



    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items',


        [MasterLootController::class, 'addVaultItem']


    )->name('master.loot.items.store');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items/release-all',


        [MasterLootController::class, 'releaseAllItems']


    )->name('master.loot.items.release-all');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items/{lootItem}/release',


        [MasterLootController::class, 'releaseItem']


    )->name('master.loot.items.release');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items/{lootItem}/withdraw',


        [MasterLootController::class, 'withdrawItem']


    )->name('master.loot.items.withdraw');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items/{lootItem}/claims/{claim}/reverse',


        [MasterLootController::class, 'reverseItemClaim']


    )->name('master.loot.items.claims.reverse');


    Route::delete(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items/{lootItem}',


        [MasterLootController::class, 'destroyItem']


    )->name('master.loot.items.destroy');


    Route::get(


        '/master/campaigns/{campaign}/loot/{lootDrop}/items/{lootItem}/image',


        [MasterLootController::class, 'itemImage']


    )->name('master.loot.items.image');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/currency',


        [MasterLootController::class, 'addCurrency']


    )->name('master.loot.currency.store');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/currency/{currency}/release',


        [MasterLootController::class, 'releaseCurrency']


    )->name('master.loot.currency.release');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/currency/{currency}/withdraw',


        [MasterLootController::class, 'withdrawCurrency']


    )->name('master.loot.currency.withdraw');


    Route::post(


        '/master/campaigns/{campaign}/loot/{lootDrop}/close',


        [MasterLootController::class, 'closeDrop']


    )->name('master.loot.close');


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


    /*


    |--------------------------------------------------------------------------


    | Workspace Folders — sistema genérico


    |--------------------------------------------------------------------------


    |


    | Não depende do modo Mestre/Player. O próprio controller valida o escopo:


    | campaign.shops, character.inventory, master.vault etc.


    |


    */


    Route::get(


        '/workspace-folders',


        [WorkspaceFolderController::class, 'index']


    )->name('workspace-folders.index');


    Route::post(


        '/workspace-folders',


        [WorkspaceFolderController::class, 'store']


    )->name('workspace-folders.store');


    Route::post(


        '/workspace-folders/{workspaceFolder}',


        [WorkspaceFolderController::class, 'update']


    )->name('workspace-folders.update');


    Route::delete(


        '/workspace-folders/{workspaceFolder}',


        [WorkspaceFolderController::class, 'destroy']


    )->name('workspace-folders.destroy');


    Route::get(


        '/workspace-folders/{workspaceFolder}/image',


        [WorkspaceFolderController::class, 'image']


    )->name('workspace-folders.image');


    Route::patch(


        '/workspace-folder-items/move',


        [WorkspaceFolderController::class, 'moveItem']


    )->name('workspace-folder-items.move');


    Route::patch(


        '/workspace-folders/reorder',


        [WorkspaceFolderController::class, 'reorder']


    )->name('workspace-folders.reorder');


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