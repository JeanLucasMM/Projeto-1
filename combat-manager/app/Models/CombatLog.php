<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatLog extends Model
{
    protected $fillable = [
        'combat_id',
        'message',
    ];

    public function combat(): BelongsTo
    {
        return $this->belongsTo(Combat::class);
    }
}