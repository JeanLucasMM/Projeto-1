<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Services\FolderService;
use Illuminate\Support\Facades\Auth;
use App\Models\Folder;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\Npc;

class FolderController extends Controller
{
    public function __construct(
        private FolderService $folderService
    ) {}

    public function store(StoreFolderRequest $request)
    {
        $this->folderService->create([

            'user_id' => Auth::id(),

            'name' => $request->name,

            'subtitle' => $request->subtitle,

            'color' => $request->color,

        ]);

        return back()->with(
            'success',
            'Pasta criada com sucesso.'
        );
    }

public function destroy(Folder $folder)
{
    

    abort_if($folder->user_id !== Auth::id(), 403);

    // Remove todos os NPCs da pasta
    $folder->npcs()->update([
        'folder_id' => null,
    ]);

    // Exclui a pasta
    $folder->delete();

    return redirect()
        ->route('npcs.index')
        ->with('success', 'Pasta removida com sucesso.');
}

public function show(Folder $folder)
{
    abort_if($folder->user_id !== Auth::id(), 403);

    $folder->load('npcs');

    return view('npcs.folders.show', compact('folder'));
}
public function edit(Folder $folder)
{
    abort_if($folder->user_id !== Auth::id(), 403);

    return response()->json($folder);
}

public function update(UpdateFolderRequest $request, Folder $folder)
{
    abort_if($folder->user_id !== Auth::id(), 403);

    $folder->update([
        'name' => $request->name,
        'subtitle' => $request->subtitle,
        'color' => $request->color,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Pasta atualizada com sucesso.');
}



}