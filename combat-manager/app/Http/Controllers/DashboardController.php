<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard', [
            'mode' => $request->session()->get('dashboard_mode'),
        ]);
    }

    public function setMode(Request $request)
    {
        $mode = $request->validate([
            'mode' => ['required', 'in:master,player'],
        ])['mode'];

        $request->session()->put('dashboard_mode', $mode);

        return redirect()->route('dashboard');
    }

    public function clearMode(Request $request)
    {
        $request->session()->forget('dashboard_mode');

        return redirect()->route('dashboard');
    }
}