<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterTransformationState extends Model
{
    protected $fillable = [
        'character_id',
        'character_transformation_id',
        'is_active',

        'activated_at',

        'combat_id',
        'activated_round',
        'activated_turn',

        'duration_remaining',

        'monster_state',
        'runtime_state',
    ];


    protected $casts = [
        'character_id' =>
            'integer',

        'character_transformation_id' =>
            'integer',

        'is_active' =>
            'boolean',

        'activated_at' =>
            'datetime',

        'combat_id' =>
            'integer',

        'activated_round' =>
            'integer',

        'activated_turn' =>
            'integer',

        'duration_remaining' =>
            'integer',

        'monster_state' =>
            'array',

        'runtime_state' =>
            'array',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relações
    |--------------------------------------------------------------------------
    */

    public function character(): BelongsTo
    {
        return $this->belongsTo(
            Character::class
        );
    }


    public function transformation(): BelongsTo
    {
        return $this->belongsTo(
            CharacterTransformation::class,
            'character_transformation_id'
        );
    }


    public function combat(): BelongsTo
    {
        return $this->belongsTo(
            Combat::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasActiveTransformation(): bool
    {
        return (
            (bool) $this->is_active
            && $this->character_transformation_id !== null
        );
    }
}