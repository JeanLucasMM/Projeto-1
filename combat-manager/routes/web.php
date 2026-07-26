<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NpcController;
use App\Http\Controllers\CombatController;
use App\Http\Controllers\FolderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {

    // Gerenciamento de NPCs Gerais
    Route::get('/npcs', [NpcController::class, 'index'])->name('npcs.index');
    Route::get('/npcs/{id}', [NpcController::class, 'show'])->name('npcs.show');
    Route::post('/npc/import', [NpcController::class, 'import'])->name('npc.import');
    Route::delete('/npcs/{id}', [NpcController::class, 'destroy'])->name('npcs.destroy');

    // CRUD de Combates (Exceto Edit/Update padrão)
    Route::resource('combats', CombatController::class)->except(['edit', 'update']);

    // Controle de Fluxo do Combate (Novo!)
    Route::post('/combats/{combat}/start', [CombatController::class, 'start'])->name('combats.start');
    Route::post('/combats/{combat}/reset', [CombatController::class, 'reset'])->name('combats.reset');
    Route::post('/combats/{combat}/next', [CombatController::class, 'next'])->name('combats.next');

    // NPCs dentro do Combate
    Route::post('/combats/{combat}/npcs', [CombatController::class, 'addNpc'])->name('combats.npcs.store');
    Route::delete('/combats/{combat}/npcs/{combatNpc}', [CombatController::class, 'removeNpc'])->name('combats.npcs.destroy');
    
    // Rotas de Iniciativa dos NPCs (Mantidas e Alinhadas)
    Route::put('/combats/{combat}/npcs/{combatNpc}', [CombatController::class, 'updateInitiative'])->name('combats.npcs.update-initiative');
    Route::patch('/combats/{combat}/npcs/{combatNpc}/initiative', [CombatController::class, 'updateInitiative'])->name('combats.npcs.initiative');

    // Jogadores dentro do Combate
    Route::post('/combats/{combat}/players', [CombatController::class, 'addPlayer'])->name('combats.players.store');
    Route::delete('/combats/{combat}/players/{player}', [CombatController::class, 'removePlayer'])->name('combats.players.destroy');
    
    // Rota de Iniciativa dos Jogadores (Mantida e Alinhada)
    Route::patch('/combats/{combat}/players/{player}/initiative', [CombatController::class, 'updatePlayerInitiative'])->name('combats.players.initiative');
    

Route::patch(
    '/combats/{combat}/npcs/{combatNpc}/damage',
    [CombatController::class, 'damageNpc']
)->name('combats.npcs.damage');

Route::patch(
    '/combats/{combat}/npcs/{combatNpc}/heal',
    [CombatController::class, 'healNpc']
)->name('combats.npcs.heal');

Route::post('/combat/npc/update-resource', [CombatController::class, 'updateResource']);
});

Route::patch('/combats/{combat}/npcs/{combatNpc}/temporary-hp', [CombatController::class, 'temporaryHp'])
    ->name('combats.npcs.temporaryHp');

Route::post('/api/roll', [App\Http\Controllers\DiceRollController::class, 'roll']);

Route::post(
    '/folders',
    [FolderController::class, 'store']
)->name('folders.store');

Route::resource('folders', FolderController::class)
    ->only([
        'store',
        'show',
        'update',
        'destroy',
    ]);

Route::get('/folders/{folder}', [FolderController::class, 'show'])
    ->name('folders.show');

Route::patch('/npcs/{npc}/move-folder', [NpcController::class, 'moveFolder'])
    ->name('npcs.move-folder');

Route::resource('folders', FolderController::class)
    ->only([
        'store',
        'show',
        'edit',
        'update',
        'destroy'
    ]);

Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])
    ->name('folders.destroy');

Route::patch(
    '/npcs/{npc}/remove-folder',
    [NpcController::class, 'removeFromFolder']
)->name('npcs.remove-folder');

require __DIR__.'/auth.php';