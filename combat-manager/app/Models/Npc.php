<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Npc extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'nickname',
        'creature_type',
        'size',
        'alignment',
        'armor_class',
        'challenge_rating',
        'max_hp',
        'json_data',
        'image_path',
        'folder_id',
    ];

    protected $casts = [
        'json_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors para Cálculo Automático (Suporta Formato Novo e Legado)
    |--------------------------------------------------------------------------
    */

    public function getCalculatedAcAttribute()
    {
        // Se for o formato novo (npc-builder)
        if (($this->json_data['format'] ?? null) === 'npc-builder') {
            $combat = $this->json_data['combat'] ?? [];
            $abilities = $this->json_data['abilities'] ?? [];

            $acBase = $combat['ac_base'] ?? 10;
            $acBonus = $combat['ac_bonus'] ?? 0;
            
            // Modificador de Destreza: floor((dex - 10) / 2)
            $dex = $abilities['dex'] ?? 10;
            $dexMod = floor(($dex - 10) / 2);

            // Fórmula: CA base + bônus + modificador de destreza
            return $acBase + $acBonus + $dexMod;
        }

        // Formato Legado
        if (!empty($this->armor_class)) {
            return $this->armor_class;
        }

        return 10;
    }

    public function getCalculatedHpAttribute()
    {
        // Se for o formato novo (npc-builder)
        if (($this->json_data['format'] ?? null) === 'npc-builder') {
            $combat = $this->json_data['combat'] ?? [];
            $abilities = $this->json_data['abilities'] ?? [];

            $hpMode = $combat['hp_mode'] ?? 'average';

            if ($hpMode === 'custom') {
                return $combat['custom_hp'] ?? 0;
            }

            $hitDie = $combat['hit_die'] ?? 'd8';
            $diceCount = $combat['hit_dice_count'] ?? 0;
            $extra = $combat['hp_mod_extra'] ?? 0;

            // Modificador de Constituição: floor((con - 10) / 2)
            $con = $abilities['con'] ?? 10;
            $conMod = floor(($con - 10) / 2);

            $avgDie = match ($hitDie) {
                'd4'  => 2.5,
                'd6'  => 3.5,
                'd8'  => 4.5,
                'd10' => 5.5,
                'd12' => 6.5,
                'd20' => 10.5,
                default => 0
            };

            // Vida = (Dados * Média do Dado) + (Dados * Modificador de Constituição) + Bônus Extra
            return floor(
                ($diceCount * $avgDie) 
                + ($diceCount * $conMod) 
                + $extra
            );
        }

        // Formato Legado
        if (!empty($this->max_hp)) {
            return $this->max_hp;
        }

        return 0;
    }
}