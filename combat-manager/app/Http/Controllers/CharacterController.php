<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Services\Characters\CharacterCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CharacterController extends Controller
{
    public function index(): View
    {
        $characters = Character::query()
            ->where('user_id', Auth::id())
            ->with([
                'classes',
                'combat',
            ])
            ->latest()
            ->get();

        return view('characters.index', [
            'characters' => $characters,
        ]);
    }

    public function store(
        Request $request,
        CharacterCreationService $characterCreationService
    ): RedirectResponse {
        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | Identidade
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'species' => [
                'required',
                'string',
                'max:80',
            ],

            'background' => [
                'required',
                'string',
                'max:120',
            ],

            'alignment' => [
                'required',
                'string',
                'max:50',
            ],

            /*
            |--------------------------------------------------------------------------
            | Classes
            |--------------------------------------------------------------------------
            |
            | A primeira classe será criada como principal.
            | As demais serão multiclasses.
            |
            */

            'classes' => [
                'required',
                'array',
                'min:1',
                'max:6',
            ],

            'classes.*.class' => [
                'required',
                'string',
                'max:80',
            ],

            'classes.*.subclass' => [
                'nullable',
                'string',
                'max:80',
            ],

            'classes.*.level' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Multiclasse
            |--------------------------------------------------------------------------
            */

            'multi_class_enabled' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Experiência
            |--------------------------------------------------------------------------
            |
            | XP é opcional porque nem toda campanha usa o sistema.
            |
            */

            'xp_enabled' => [
                'nullable',
                'boolean',
            ],

            'experience_points' => [
                'nullable',
                'integer',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | Proficiência
            |--------------------------------------------------------------------------
            */

            'custom_prof_enabled' => [
                'nullable',
                'boolean',
            ],

            'proficiency_bonus' => [
                'nullable',
                'integer',
            ],

            /*
            |--------------------------------------------------------------------------
            | Imagem
            |--------------------------------------------------------------------------
            */

            'image' => [
                'nullable',
                'image',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Normalização dos controles opcionais
        |--------------------------------------------------------------------------
        */

        $validated['multi_class_enabled'] = $request->boolean(
            'multi_class_enabled'
        );

        $validated['xp_enabled'] = $request->boolean(
            'xp_enabled'
        );

        $validated['custom_prof_enabled'] = $request->boolean(
            'custom_prof_enabled'
        );

        /*
        |--------------------------------------------------------------------------
        | XP
        |--------------------------------------------------------------------------
        |
        | Se o sistema de XP estiver desligado, não carregamos um valor
        | acidentalmente do formulário.
        |
        */

        if (! $validated['xp_enabled']) {
            $validated['experience_points'] = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Proficiência
        |--------------------------------------------------------------------------
        |
        | Se não houver override, o Service calcula automaticamente
        | com base no nível total.
        |
        */

        if (! $validated['custom_prof_enabled']) {
            $validated['proficiency_bonus'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Multiclasse desativada
        |--------------------------------------------------------------------------
        |
        | O formulário pode mandar apenas uma classe. Neste caso mantemos
        | apenas a primeira entrada.
        |
        */

        if (! $validated['multi_class_enabled']) {
            $validated['classes'] = [
                $validated['classes'][0],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Normalização dos nomes das classes
        |--------------------------------------------------------------------------
        |
        | Além de validar duplicação, limpamos espaços desnecessários.
        |
        */

        $validated['classes'] = collect($validated['classes'])
            ->map(function (array $class) {
                return [
                    'class' => trim($class['class']),
                    'subclass' => isset($class['subclass'])
                        ? trim((string) $class['subclass'])
                        : null,
                    'level' => (int) $class['level'],
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Classes duplicadas
        |--------------------------------------------------------------------------
        |
        | A migration usa unique(character_id, class).
        |
        */

        $classNames = collect($validated['classes'])
            ->pluck('class')
            ->map(
                fn (string $class) =>
                    mb_strtolower(trim($class))
            )
            ->values();

        if ($classNames->duplicates()->isNotEmpty()) {
            return back()
                ->withErrors([
                    'classes' => 'Uma mesma classe não pode ser adicionada duas vezes.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Nível total
        |--------------------------------------------------------------------------
        |
        | Exemplo:
        |
        | Clérigo 5 + Guerreiro 3 = nível 8
        |
        */

        $totalLevel = collect($validated['classes'])
            ->sum(
                fn (array $class) =>
                    (int) $class['level']
            );

        if ($totalLevel < 1 || $totalLevel > 20) {
            return back()
                ->withErrors([
                    'classes' => 'A soma dos níveis das classes deve estar entre 1 e 20.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Multiclasse coerente com o total
        |--------------------------------------------------------------------------
        |
        | Se houver mais de uma classe, o modo precisa estar ativo.
        |
        */

        if (
            count($validated['classes']) > 1 &&
            ! $validated['multi_class_enabled']
        ) {
            return back()
                ->withErrors([
                    'classes' => 'Ative a opção de multiclasse para adicionar mais de uma classe.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Imagem
        |--------------------------------------------------------------------------
        */

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('characters', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Criação
        |--------------------------------------------------------------------------
        */

        try {
            $characterCreationService->create(
                [
                    ...$validated,
                    'user_id' => Auth::id(),
                    'level' => $totalLevel,
                ],
                $imagePath
            );
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }

        return redirect()
            ->route('characters.index')
            ->with(
                'success',
                'Personagem criado com sucesso.'
            );
    }

    public function destroy(
        Character $character
    ): RedirectResponse {
        abort_unless(
            $character->user_id === Auth::id(),
            403
        );

        if ($character->image_path) {
            Storage::disk('public')->delete(
                $character->image_path
            );
        }

        $character->delete();

        return redirect()
            ->route('characters.index')
            ->with(
                'success',
                'Personagem removido.'
            );
    }
}