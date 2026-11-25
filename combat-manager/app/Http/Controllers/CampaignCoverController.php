<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Aparência pública da campanha. Apenas o Mestre pode alterá-la.
 * Não toca nas regras de convites, Party ou compartilhamento de fichas.
 */
class CampaignCoverController extends Controller
{
    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        abort_unless($request->user() && $campaign->isOwner($request->user()), 403);

        $data = $request->validate([
            'cover_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'remove_cover' => ['sometimes', 'boolean'],
            'cover_image_focus_y' => ['sometimes', 'integer', 'between:0,100'],
        ]);

        $remove = $request->boolean('remove_cover');
        $image = $request->file('cover_image');
        $newFocus = isset($data['cover_image_focus_y'])
            ? (int) $data['cover_image_focus_y']
            : (int) ($campaign->cover_image_focus_y ?? 50);

        if (!$remove && !$image && $newFocus === (int) ($campaign->cover_image_focus_y ?? 50)) {
            throw ValidationException::withMessages(['cover_image' => 'Escolha uma imagem ou ajuste o enquadramento.']);
        }

        $old = (string) ($campaign->cover_image_path ?? '');
        $newPath = null;

        if ($image && !$remove) {
            $newPath = $image->store('campaign-covers/' . $campaign->id, 'public');
            if (!$newPath) {
                throw ValidationException::withMessages(['cover_image' => 'Não foi possível salvar a imagem.']);
            }
        }

        try {
            $campaign->forceFill([
                'cover_image_path' => $remove ? null : ($newPath ?: ($old ?: null)),
                'cover_image_focus_y' => $newFocus,
            ])->save();
        } catch (\Throwable $error) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $error;
        }

        if (($remove || $newPath) && $old && $old !== $newPath) {
            Storage::disk('public')->delete($old);
        }

        return back()->with('success', $remove ? 'Capa da campanha removida.' : 'Capa da campanha atualizada.');
    }
}
