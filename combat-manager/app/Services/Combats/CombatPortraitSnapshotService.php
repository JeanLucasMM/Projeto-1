<?php

namespace App\Services\Combats;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/** Converte o retrato no instante do encerramento em JPG embutido no arquivo. */
class CombatPortraitSnapshotService
{
    /** Busca foto de personagem manual sem vínculo apenas se houver nome ÚNICO na campanha. */
    public function uniqueCampaignPortrait(?int $campaignId, string $name): ?string
    {
        if (!$campaignId || trim($name) === '') return null;
        $paths = DB::table('campaign_characters as cc')
            ->join('characters as c', 'c.id', '=', 'cc.character_id')
            ->where('cc.campaign_id', $campaignId)
            ->where('c.name', trim($name))
            ->pluck('c.image_path');
        return $paths->count() === 1 ? ($paths->first() ?: null) : null;
    }

    public function freeze(?string $original): ?string
    {
        $path = trim((string) $original);
        if ($path === '' || str_contains($path, '..')) return null;
        // Caminhos criados com Storage::url(), asset('/storage/...') ou paths simples.
        $path = (string) (parse_url($path, PHP_URL_PATH) ?: $path);
        $path = ltrim(preg_replace('~^(?:/)?(?:storage/)+~i', '', $path) ?? $path, '/');
        if ($path === '' || str_contains($path, '..')) return null;
        try {
            if (Storage::disk('public')->exists($path)) {
                $bytes = Storage::disk('public')->get($path);
            } elseif (is_file(public_path('storage/'.$path))) {
                $bytes = file_get_contents(public_path('storage/'.$path));
            } else return null;
        } catch (\Throwable) { return null; }
        if (!is_string($bytes) || strlen($bytes) > 8000000 || strlen($bytes) < 10) return null;
        $info = @getimagesizefromstring($bytes);
        if (!$info || !in_array($info['mime'] ?? '', ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) return null;
        // Padroniza imagens enormes, GIFs, PNGs e WEBPs; reduz o peso do JSON/ SVG.
        if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
            $source = @imagecreatefromstring($bytes);
            if ($source !== false) {
                $w = imagesx($source); $h = imagesy($source);
                $targetWidth = min(360, $w);
                $targetHeight = max(1, (int) round($h * $targetWidth / max(1, $w)));
                $targetHeight = min(540, $targetHeight);
                $out = imagecreatetruecolor($targetWidth, $targetHeight);
                $bg = imagecolorallocate($out, 242, 232, 214);
                imagefill($out, 0, 0, $bg);
                imagecopyresampled($out, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $w, $h);
                ob_start(); imagejpeg($out, null, 82); $small = ob_get_clean();
                imagedestroy($out); imagedestroy($source);
                if ($small !== false) return 'data:image/jpeg;base64,'.base64_encode($small);
            }
        }
        // Sem GD, ainda salva os formatos raster suportados diretamente.
        return 'data:'.$info['mime'].';base64,'.base64_encode($bytes);
    }
}
