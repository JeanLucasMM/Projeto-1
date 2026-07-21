<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatPlayer extends Model
{
    protected $fillable = [

        'combat_id',

        'name',

        'initiative',

    ];

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }
}