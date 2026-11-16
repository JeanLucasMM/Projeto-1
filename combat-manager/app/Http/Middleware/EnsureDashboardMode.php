<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardMode
{
    /**
     * Seleção de área do Spellbound (não substitui autenticação, policies
     * nem autorizações de recursos). O modo é lido da sessão uma vez por
     * requisição, sem polling ou consultas extras ao banco de dados.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$allowedModes
    ): Response {
        $allowedModes = array_values(array_unique(array_filter(
            array_map(static fn (string $mode): string => strtolower(trim($mode)), $allowedModes),
            static fn (string $mode): bool => in_array($mode, ['master', 'player'], true)
        )));

        if ($allowedModes === []) {
            $allowedModes = ['master', 'player'];
        }

        $currentMode = $request->session()->get('dashboard_mode');
        $hasValidMode = in_array($currentMode, ['master', 'player'], true);

        if ($hasValidMode && in_array($currentMode, $allowedModes, true)) {
            return $next($request);
        }

        $requiredLabel = count($allowedModes) === 1
            ? ($allowedModes[0] === 'master' ? 'Mestre' : 'Player')
            : 'Mestre ou Player';

        $message = $hasValidMode
            ? "Esta área exige o modo {$requiredLabel}. Escolha o modo para continuar."
            : 'Escolha como deseja entrar no Spellbound para continuar.';

        /*
         * Requisições de atualização em segundo plano não são navegação.
         * Não as redirecionamos para uma página HTML nem alteramos o modo
         * selecionado: a aplicação pode tratar o erro 403 normalmente.
         */
        // Fetch comum pode usar Accept: */* em vez de application/json.
        // Sec-Fetch-Dest: empty identifica essa chamada sem confundi-la
        // com uma navegação normal do navegador (dest=document).
        if ($request->expectsJson() || $request->header('Sec-Fetch-Dest') === 'empty') {
            return response()->json([
                'message' => $message,
                'mode_required' => true,
                'allowed_modes' => $allowedModes,
            ], 403);
        }

        /*
         * Preserve o modo atual. O usuário decide se deseja alterná-lo.
         * A implementação anterior apagava dashboard_mode ao acessar uma
         * rota incompatível, podendo interromper outras abas e requests.
         */
        $this->rememberIntendedArea($request, $allowedModes);

        return redirect()->route('dashboard')->with('mode_required', $message);
    }

    private function rememberIntendedArea(Request $request, array $allowedModes): void
    {
        /*
         * Apenas páginas acessadas por GET/HEAD podem virar destino após
         * a seleção. Redirecionar um GET para o endpoint de um POST/PATCH
         * armazenado na sessão resulta em 405 e comportamento confuso.
         */
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            $request->session()->forget(['dashboard_intended_url', 'dashboard_intended_modes']);

            return;
        }

        $request->session()->put('dashboard_intended_url', $request->getRequestUri());
        $request->session()->put('dashboard_intended_modes', $allowedModes);
    }
}
