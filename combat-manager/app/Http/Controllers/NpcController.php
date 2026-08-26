<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportNpcRequest;
use App\Services\ImportNpcService;
use App\Services\NpcService;
use Illuminate\Support\Facades\Auth;
use App\Factories\NpcViewModelFactory;
use App\Services\FolderService;
use Illuminate\Http\Request;
use App\Models\Npc;
use App\Builders\Importers\Native\NativeNpcImportService;

class NpcController extends Controller
{
    public function __construct(
        private ImportNpcService $importService,
        private NpcService $npcService,
        private NpcViewModelFactory $viewModelFactory,
        private FolderService $folderService,
        private NativeNpcImportService $nativeImporter,
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
        $request->input('search'),
        $request->input('sort', 'name_asc'),
        $request->input('folder')
    );

    $folders = $this->folderService->getAllByUser(
        Auth::id()
    );

    return view('npcs.index', compact(
        'npcs',
        'folders'
    ));
}

    /**
     * Importa um NPC a partir de um arquivo JSON.
     */
    public function import(ImportNpcRequest $request)
    {
        $file = $request->file('npc_file');

        $image = $request->file('npc_image');

        $content = file_get_contents(
            $file->getRealPath()
        );

        $json = json_decode(
            $content,
            true
        );

        if (!$json) {
            throw new \Exception(
                'JSON inválido.'
            );
        }

        if (
            ($json['format'] ?? null)
            === 'npc-builder'
        ) {

            $this->nativeImporter->import(
                Auth::user(),
                $json,
                $image
            );

        } else {

            $this->importService->import(
                Auth::user(),
                $content,
                $image
            );
        }

        return redirect()
            ->route('npcs.index')
            ->with(
                'success',
                'NPC importado com sucesso!'
            );
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
            ->with(
                'success',
                'NPC removido com sucesso.'
            );
    }

    public function removeFromFolder(Npc $npc)
    {
        abort_if(
            $npc->user_id !== Auth::id(),
            403
        );

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
        $npc->deceased_at = $npc->deceased_at
            ? null
            : now();

        $npc->save();

        return response()->json([
            'is_deceased' => !is_null($npc->deceased_at),
            'deceased_at' => $npc->deceased_at
                ? $npc->deceased_at->format('d/m/Y')
                : null,
        ]);
    }
    public function downloadJson(int $id)
{
    $npc = $this->npcService->findByIdAndUser(
        $id,
        Auth::id()
    );

    abort_if(!$npc, 404);

    $json = $npc->json_data ?? [];

    $name = trim($npc->name ?: 'npc');

    $filename = preg_replace(
        '/[\\\\\/:*?"<>|]+/',
        '_',
        $name
    );

    $filename = $filename ?: 'npc';

    return response()->streamDownload(
        function () use ($json) {
            echo json_encode(
                $json,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );
        },
        $filename . '.json',
        [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]
    );
}
}