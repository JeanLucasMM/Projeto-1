<?php

namespace App\Http\Controllers;

use App\Builders\Importers\Native\NativeNpcMapper;
use App\Builders\Importers\Native\NativeNpcValidator;
use App\Models\Npc;
use App\Services\Calculators\StatisticCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use JsonException;
use Throwable;

/**
 * Atualiza o mesmo NPC do Cofre. Não cria cópias e preserva pasta, id,
 * falecimento e vínculos existentes; somente os campos escolhidos mudam.
 */
class NpcEditController extends Controller
{
    public function update(Request $request, Npc $npc, StatisticCalculator $calculator): JsonResponse
    {
        abort_unless((int) $npc->user_id === (int) Auth::id(), 403);

        $validated = $request->validate([
            'npc_file' => ['nullable', 'file', 'mimes:json,txt', 'max:4096'],
            'npc_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'image_zoom' => ['required', 'numeric', 'between:1,3'],
            'image_horizontal' => ['required', 'integer', 'between:0,100'],
            'image_position' => ['required', 'in:auto,top,center,bottom'],
        ]);

        $replacements = [];
        if ($request->hasFile('npc_file')) {
            $contents = file_get_contents($request->file('npc_file')->getRealPath());
            try {
                $json = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                throw ValidationException::withMessages([
                    'npc_file' => 'O arquivo JSON não pôde ser interpretado.',
                ]);
            }
            if (!is_array($json) || array_is_list($json)) {
                throw ValidationException::withMessages([
                    'npc_file' => 'O arquivo precisa conter um objeto JSON de NPC.',
                ]);
            }

            if (($json['format'] ?? null) === 'npc-builder') {
                if (!is_array($json['header'] ?? null)
                    || !is_array($json['combat'] ?? null)
                    || !is_array($json['header']['types'] ?? [])
                    || !is_array($json['header']['alignments'] ?? [])) {
                    throw ValidationException::withMessages([
                        'npc_file' => 'O JSON do NPC Builder precisa conter header, combat, types e alignments válidos.',
                    ]);
                }
                try {
                    NativeNpcValidator::validate($json);
                } catch (Throwable $exception) {
                    throw ValidationException::withMessages([
                        'npc_file' => 'JSON do NPC Builder inválido: ' . $exception->getMessage(),
                    ]);
                }
                $replacements = NativeNpcMapper::map($json);
                if (!is_string($replacements['name'] ?? null) || trim($replacements['name']) === '') {
                    throw ValidationException::withMessages(['npc_file' => 'O NPC precisa ter um nome.']);
                }
                $replacements['max_hp'] = ($json['combat']['hp_mode'] ?? '') === 'custom'
                    ? max(0, (int) ($json['combat']['custom_hp'] ?? 0))
                    : 1;
            } else {
                $hp = $json['HP'] ?? null;
                if (!is_string($json['name'] ?? null) || trim($json['name']) === ''
                    || !is_array($hp)
                    || !is_numeric($hp['HD'] ?? null)
                    || !is_numeric($hp['type'] ?? null)
                    || !is_numeric($hp['modifier'] ?? null)
                    || !is_numeric($json['AC'] ?? null)
                    || !is_numeric($json['CR'] ?? null)) {
                    throw ValidationException::withMessages([
                        'npc_file' => 'JSON legado incompatível. Confira name, AC, CR e HP (HD, type, modifier).',
                    ]);
                }
                $replacements = [
                    'name' => $json['name'],
                    'nickname' => $json['nickname'] ?? null,
                    'creature_type' => $json['type'] ?? null,
                    'size' => $json['size'] ?? null,
                    'alignment' => $json['alignment'] ?? null,
                    'armor_class' => $json['AC'],
                    'challenge_rating' => $json['CR'],
                    'max_hp' => $calculator->hitPoints($hp),
                    'json_data' => $json,
                ];
            }
        }

        $oldImage = $npc->image_path;
        $newImage = null;
        try {
            if ($request->hasFile('npc_image')) {
                $newImage = $request->file('npc_image')->store('npcs', 'public');
                if (!$newImage) {
                    throw ValidationException::withMessages([
                        'npc_image' => 'Não foi possível armazenar a imagem.',
                    ]);
                }
            }

            // Atribuição direta: não altera as regras de mass-assignment do Model.
            foreach ($replacements as $field => $value) {
                $npc->{$field} = $value;
            }
            if ($newImage !== null) {
                $npc->image_path = $newImage;
            }
            $npc->image_zoom = round((float) $validated['image_zoom'], 2);
            $npc->image_horizontal = (int) $validated['image_horizontal'];
            $npc->image_position = $validated['image_position'];
            $npc->save();
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }
            throw $exception;
        }

        // Nunca apaga imagens externas nem arquivos de outro módulo.
        if ($newImage !== null && is_string($oldImage)
            && str_starts_with($oldImage, 'npcs/') && $oldImage !== $newImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return response()->json([
            'success' => true,
            'message' => 'NPC atualizado com sucesso.',
            'npc_id' => (int) $npc->id,
        ]);
    }
}
