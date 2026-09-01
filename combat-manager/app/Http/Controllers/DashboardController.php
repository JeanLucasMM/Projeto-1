<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(
        Request $request
    ): View {
        return view('dashboard', [
            'mode' => $request
                ->session()
                ->get('dashboard_mode'),

            'intendedModes' => $request
                ->session()
                ->get(
                    'dashboard_intended_modes',
                    []
                ),
        ]);
    }

    public function setMode(
        Request $request
    ): RedirectResponse {
        $mode = $request->validate([
            'mode' => [
                'required',
                'in:master,player',
            ],
        ])['mode'];

        $intendedUrl = $request
            ->session()
            ->pull(
                'dashboard_intended_url'
            );

        $intendedModes = $request
            ->session()
            ->pull(
                'dashboard_intended_modes',
                []
            );

        $request
            ->session()
            ->put(
                'dashboard_mode',
                $mode
            );

        /*
        | Se o usuário chegou ao Dashboard porque tentou abrir uma rota
        | protegida e escolheu o modo correto, continua de onde parou.
        */
        if (
            $intendedUrl
            && (
                empty($intendedModes)
                || in_array(
                    $mode,
                    $intendedModes,
                    true
                )
            )
        ) {
            return redirect(
                $intendedUrl
            );
        }

        /*
        | Se escolheu outro modo, entra normalmente na área principal
        | daquele contexto.
        */
        return $mode === 'master'
            ? redirect()->route('combats.index')
            : redirect()->route('characters.index');
    }

    public function clearMode(
        Request $request
    ): RedirectResponse {
        $request
            ->session()
            ->forget([
                'dashboard_mode',
                'dashboard_intended_url',
                'dashboard_intended_modes',
            ]);

        return redirect()
            ->route('dashboard');
    }
}