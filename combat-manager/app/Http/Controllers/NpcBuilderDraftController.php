<?php

namespace App\Http\Controllers;

use App\Models\NpcBuilderDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NpcBuilderDraftController extends Controller
{
    public function show()
    {
        $draft = NpcBuilderDraft::where(
            'user_id',
            Auth::id()
        )->first();

        return response()->json([
            'draft' => $draft?->json_data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'json_data' => ['required', 'array'],
            'json_data.format' => ['required', 'in:npc-builder'],
            'json_data.version' => ['required', 'integer'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Salva o JSON completo
        |--------------------------------------------------------------------------
        |
        | A validação acima garante que o payload possui a estrutura mínima
        | esperada, mas aqui precisamos salvar todo o conteúdo enviado pelo
        | Builder, e não somente os campos explicitamente validados.
        |
        */

        $jsonData = $request->input('json_data');

        $draft = NpcBuilderDraft::updateOrCreate(
            [
                'user_id' => Auth::id(),
            ],
            [
                'json_data' => $jsonData,
            ]
        );

        return response()->json([
            'success' => true,
            'draft_id' => $draft->id,
            'updated_at' => $draft->updated_at,
        ]);
    }

    public function destroy()
    {
        NpcBuilderDraft::where(
            'user_id',
            Auth::id()
        )->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}