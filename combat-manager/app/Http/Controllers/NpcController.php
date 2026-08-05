<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportNpcRequest;
use App\Services\ImportNpcService;
use App\Services\NpcService;
use Illuminate\Support\Facades\Auth;
use App\Services\Interpreters\NpcInterpreter;
use App\Factories\NpcViewModelFactory;
use App\Services\FolderService;
use Illuminate\Http\Request;
use App\Models\Npc;

class NpcController extends Controller
{
    public function __construct(
        private ImportNpcService $importService,
        private NpcService $npcService,
        private NpcViewModelFactory $viewModelFactory,
        private FolderService $folderService,
) {}


public function moveFolder(Request $request, Npc $npc)
{
    $request->validate([
        'folder_id' => ['nullable', 'exists:folders,id']
    ]);

    $npc->update([
        'folder_id' => $request->folder_id
    ]);

    return response()->json([
        'success' => true
    ]);
}



    /**
     * Lista os NPCs do usuário logado.
     */
public function index(Request $request)
{
    $npcs = $this->npcService->getAllByUser(

        Auth::id(),
        $request->search,
        $request->sort

    );

    $folders = $this->folderService->getAllByUser(
        Auth::id()

    );

    return view('npcs.index',compact('npcs','folders'));
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

public function removeFromFolder(Npc $npc)
{
    abort_if($npc->user_id !== Auth::id(), 403);

    $npc->update([
        'folder_id' => null,
    ]);

    return back()->with(
        'success',
        'NPC removido da pasta.'
    );
}
public function toggleDeceased(Npc $npc)
{
    $npc->deceased_at = $npc->deceased_at ? null : now();
    $npc->save();

    return response()->json([
        'is_deceased' => !is_null($npc->deceased_at),
        'deceased_at' => $npc->deceased_at ? $npc->deceased_at->format('d/m/Y') : null,
    ]);
}
}