<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $mode = $request->session()->get('dashboard_mode');

        return view('dashboard', [
            'mode' => in_array($mode, ['master', 'player'], true) ? $mode : null,
            'intendedModes' => $request->session()->get('dashboard_intended_modes', []),
        ]);
    }

    public function setMode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:master,player'],
        ]);

        $request->session()->put('dashboard_mode', $validated['mode']);

        // A troca conclui na home. O usuário navega pelo menu lateral.
        // Intenções antigas não podem provocar um redirecionamento-surpresa.
        $request->session()->forget([
            'dashboard_intended_url',
            'dashboard_intended_modes',
        ]);

        return redirect()->route('dashboard');
    }

    public function clearMode(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'dashboard_mode',
            'dashboard_intended_url',
            'dashboard_intended_modes',
        ]);

        return redirect()->route('dashboard');
    }
}
