<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Builders\NpcFactory;
use App\Builders\Exporter\NpcViewModelExporter;

class NpcBuilderController extends Controller
{
    public function index()
    {
        $builder = NpcFactory::create();

        return view('npc-builder.index', compact(
            'builder',
        ));
    }

}