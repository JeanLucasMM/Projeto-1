<?php

namespace App\Http\Controllers;

use App\Builders\NpcFactory;
use App\Models\NpcBuilderDraft;
use Illuminate\Support\Facades\Auth;

class NpcBuilderController extends Controller
{
    public function index()
    {
        $builder = NpcFactory::create();

        $draft = NpcBuilderDraft::where(
            'user_id',
            Auth::id()
        )->first();

        return view('npc-builder.index', [
            'builder' => $builder,
            'draft' => $draft,
            'npc' => null,
        ]);
    }
}