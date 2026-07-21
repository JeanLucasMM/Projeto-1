<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportNpcRequest;
use App\Services\ImportNpcService;
use App\Services\NpcService;
use Illuminate\Support\Facades\Auth;
use App\Services\Interpreters\NpcInterpreter;
use App\Factories\NpcViewModelFactory;

class NpcController extends Controller
{
    public function __construct(
        private ImportNpcService $importService,
        private NpcService $npcService,
        private NpcViewModelFactory $viewModelFactory
) {}

    /**
     * Lista os NPCs do usuário logado.
     */
    public function index()
    {
        $npcs = $this->npcService->getAllByUser(Auth::id());
        

        return view('npcs.index', compact('npcs'));
        
    }

    /**
     * Importa um NPC a partir de um arquivo JSON.
     */
public function import(ImportNpcRequest $request)
{
    $file = $request->file('npc_file');

    $image = $request->file('npc_image');

    $this->importService->import(
        Auth::user(),
        file_get_contents($file->getRealPath()),
        $image
        
    );

    return redirect()
        ->route('npcs.index')
        ->with('success', 'NPC importado com sucesso!');
}

public function show(int $id)
{
    
    $npc = $this->npcService->findByIdAndUser(
        $id,
        Auth::id()
    );

    abort_if(!$npc, 404);

    

    $viewModel = $this->viewModelFactory->make($npc);

    
    
    return view('npcs.show', [
        'npc' => $viewModel
    ]);
}

public function destroy(int $id)
{
    $npc = $this->npcService->findByIdAndUser(
        $id,
        Auth::id()
    );

    abort_if(!$npc, 404);

    $this->npcService->delete($npc);

    return redirect()
        ->route('npcs.index')
        ->with('success', 'NPC removido com sucesso.');
}
}