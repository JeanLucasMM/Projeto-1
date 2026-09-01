<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardMode
{
    /**
     * Garante que o usuário tenha escolhido um modo antes de entrar
     * em uma área do sistema.
     *
     * Exemplos:
     *
     * EnsureDashboardMode:player
     * EnsureDashboardMode:master
     * EnsureDashboardMode:master,player
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$allowedModes
    ): Response {
        $allowedModes = collect($allowedModes)
            ->map(
                fn (string $mode) =>
                    strtolower(trim($mode))
            )
            ->filter(
                fn (string $mode) =>
                    in_array(
                        $mode,
                        ['master', 'player'],
                        true
                    )
            )
            ->values()
            ->all();

        /*
        | Sem parâmetros, considera os dois modos válidos.
        */
        if (empty($allowedModes)) {
            $allowedModes = [
                'master',
                'player',
            ];
        }

        $currentMode = $request
            ->session()
            ->get('dashboard_mode');

        /*
        | O usuário ainda não escolheu nenhum modo.
        |
        | Guardamos a URL interna para que, se ele escolher o modo
        | compatível, volte exatamente ao local que tentou acessar.
        */
        if (
            ! in_array(
                $currentMode,
                ['master', 'player'],
                true
            )
        ) {
            $this->rememberIntendedArea(
                $request,
                $allowedModes
            );

            return redirect()
                ->route('dashboard')
                ->with(
                    'mode_required',
                    'Escolha como deseja entrar no SpellBound para continuar.'
                );
        }

        /*
        | O usuário está em um modo diferente daquele exigido pela rota.
        |
        | Exemplo:
        | está como Player e tenta abrir /combats.
        |
        | Nesse caso apagamos o modo atual e obrigamos uma nova escolha.
        */
        if (
            ! in_array(
                $currentMode,
                $allowedModes,
                true
            )
        ) {
            $this->rememberIntendedArea(
                $request,
                $allowedModes
            );

            $request
                ->session()
                ->forget('dashboard_mode');

            $requiredLabel =
                count($allowedModes) === 1
                    ? $this->modeLabel(
                        $allowedModes[0]
                    )
                    : 'Mestre ou Player';

            return redirect()
                ->route('dashboard')
                ->with(
                    'mode_required',
                    "Esta área exige o modo {$requiredLabel}. Escolha o modo para continuar."
                );
        }

        return $next($request);
    }

    private function rememberIntendedArea(
        Request $request,
        array $allowedModes
    ): void {
        /*
        | getRequestUri() guarda somente path + query da própria aplicação,
        | evitando usar uma URL externa como destino.
        */
        $request
            ->session()
            ->put(
                'dashboard_intended_url',
                $request->getRequestUri()
            );

        $request
            ->session()
            ->put(
                'dashboard_intended_modes',
                $allowedModes
            );
    }

    private function modeLabel(
        string $mode
    ): string {
        return match ($mode) {
            'master' => 'Mestre',
            'player' => 'Player',
            default => ucfirst($mode),
        };
    }
}