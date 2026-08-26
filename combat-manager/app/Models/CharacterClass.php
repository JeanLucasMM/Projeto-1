<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterClass extends Model
{
    protected $fillable = [
        'character_id',
        'class',
        'subclass',
        'level',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'level' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}