<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatNpc extends Model
{
    protected $fillable = [
        'combat_id',
        'npc_id',
        'initiative',
        'current_hp',
        'max_hp',
        'temporary_hp',
        'is_dead',
        'resource_trackers',
    ];

    protected $casts = [
        'is_dead' => 'boolean',
        'resource_trackers' => 'array',
    ];

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }

    /**
     * Retorna a quantidade atual de usos de uma habilidade.
     * Caso ainda não exista registro, utiliza o valor padrão.
     */
public function getResource(string $feature, int $default): int
{
    $resources = $this->resource_trackers ?? [];

    return $resources[$feature] ?? $default;
}

    /**
     * Atualiza a quantidade de usos de uma habilidade.
     */
public function setResource(string $feature, int $value): void
{
    $resources = $this->resource_trackers ?? [];

    $resources[$feature] = $value;

    $this->resource_trackers = $resources;
    $this->save();
}
}