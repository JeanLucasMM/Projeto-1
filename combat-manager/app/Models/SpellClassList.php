<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpellClassList extends Model
{
    protected $fillable = [
        'spell_id',
        'provider',
        'class_code',
        'class_name',
        'class_source',
        'subclass_name',
        'subclass_source',
        'list_type',
        'normalized_key',
    ];

    protected $casts = [
        'spell_id' => 'integer',
    ];

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }
}
