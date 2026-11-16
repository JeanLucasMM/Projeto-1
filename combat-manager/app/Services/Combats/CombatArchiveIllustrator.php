<?php

namespace App\Services\Combats;


/** SVG autossuficiente: incorpora apenas imagens raster pequenas, não usa URLs externas. */
class CombatArchiveIllustrator
{
    private function esc(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function portrait(array $entry, int $x, int $y, string $color): string
    {
        $name = (string) ($entry['name'] ?? 'Combatente');
        $picture = '';
        $data = (string) ($entry['portrait_data'] ?? '');
        if (preg_match('~^data:image/(?:jpeg|png|webp|gif);base64,[A-Za-z0-9+/=]+$~', $data)) {
            $picture = '<image x="'.$x.'" y="'.$y.'" width="101" height="137" preserveAspectRatio="xMidYMin slice" href="'.$data.'" clip-path="url(#cut'.$x.'-'.$y.')"/>';
        }
        // Arquivos da V66.0.49 podem ter somente image_path. O caminho é
        // resolvido apenas no servidor (nunca carregado remotamente por SVG).
        if ($picture === '' && !empty($entry['image'])) {
            $data = app(CombatPortraitSnapshotService::class)->freeze((string) $entry['image']);
            if ($data) $picture = '<image x="'.$x.'" y="'.$y.'" width="101" height="137" preserveAspectRatio="xMidYMin slice" href="'.$data.'" clip-path="url(#cut'.$x.'-'.$y.')"/>';
        }
        $initial = $this->esc((function_exists('mb_substr') ? mb_strtoupper(mb_substr($name, 0, 1)) : strtoupper(substr($name, 0, 1))));
        $hp = isset($entry['current_hp']) && isset($entry['max_hp'])
            ? (string) $entry['current_hp'] . '/' . (string) $entry['max_hp']
            : '—';
        return '<clipPath id="cut'.$x.'-'.$y.'"><rect x="'.$x.'" y="'.$y.'" width="101" height="137" rx="12"/></clipPath>'
            .'<rect x="'.$x.'" y="'.$y.'" width="101" height="137" rx="12" fill="'.$color.'"/>'
            .($picture ?: '<text x="'.($x+50).'" y="'.($y+84).'" text-anchor="middle" font-family="Georgia" font-size="48" fill="#fff9ef">'.$initial.'</text>')
            .'<rect x="'.$x.'" y="'.($y+111).'" width="101" height="26" rx="3" fill="#241f1ecc"/>'
            .'<text x="'.($x+50).'" y="'.($y+129).'" text-anchor="middle" font-size="13" font-weight="bold" fill="#fffaf0" font-family="sans-serif">'.$this->esc($hp).' PV</text>'
            .'<rect x="'.$x.'" y="'.$y.'" width="101" height="137" rx="12" fill="none" stroke="#f5ead5" stroke-width="3"/>';
    }

    public function render(array $snapshot, array $stats): string
    {
        $allies = array_slice($snapshot['allies'] ?? [], 0, 5);
        $enemies = array_slice($snapshot['enemies'] ?? [], 0, 5);
        $title = $this->esc((string) ($snapshot['name'] ?? 'Combate'));
        $campaign = $this->esc((string) ($snapshot['campaign'] ?? 'Encontro'));
        $startAllies = 570 - (count($allies) * 81);
        $startEnemies = 710;
        $tiles = '';
        foreach ($allies as $i => $entry) $tiles .= $this->portrait($entry, $startAllies + ($i * 81), 193, '#738a78');
        foreach ($enemies as $i => $entry) $tiles .= $this->portrait($entry, $startEnemies + ($i * 81), 193, '#98685d');
        $start = $this->esc((string) ($snapshot['started_label'] ?? 'Não registrado'));
        $end = $this->esc((string) ($snapshot['ended_label'] ?? 'Não registrado'));
        $round = (int) ($stats['rounds'] ?? 0);
        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="1280" height="420" viewBox="0 0 1280 420">'
            .'<defs><linearGradient id="ground" x1="0" x2="1"><stop stop-color="#e3e9df"/><stop offset=".51" stop-color="#f9f2e7"/><stop offset="1" stop-color="#edddda"/></linearGradient></defs>'
            .'<rect width="1280" height="420" rx="22" fill="#fcf8f1"/><rect x="14" y="14" width="1252" height="392" rx="16" fill="url(#ground)" stroke="#d9c5ac"/>'
            .'<text x="640" y="66" fill="#601e18" font-size="33" font-family="Georgia" font-weight="bold" text-anchor="middle">'.$title.'</text>'
            .'<text x="640" y="97" fill="#83664e" font-size="17" text-anchor="middle" font-family="Georgia">'.$campaign.' · Rodada '.$round.'</text>'
            .'<text x="503" y="158" fill="#22775b" font-family="Georgia" text-anchor="end" font-size="18" font-weight="bold">ALIADOS · '.count($snapshot['allies'] ?? []).'</text>'
            .'<text x="777" y="158" fill="#98473b" font-family="Georgia" text-anchor="start" font-size="18" font-weight="bold">INIMIGOS · '.count($snapshot['enemies'] ?? []).'</text>'
            .$tiles
            .'<circle cx="640" cy="261" r="47" fill="#fff8eb" stroke="#c9aa80" stroke-width="2"/><text x="640" y="272" font-family="Georgia" font-weight="bold" font-style="italic" font-size="33" text-anchor="middle" fill="#76271e">VS</text>'
            .'<text x="640" y="377" text-anchor="middle" font-size="16" fill="#80664c" font-family="Georgia">Início: '.$start.'    •    Término: '.$end.'</text></svg>';
    }
}
