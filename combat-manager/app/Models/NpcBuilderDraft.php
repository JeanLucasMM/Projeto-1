<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NpcBuilderDraft extends Model
{
    protected $fillable = [
        'user_id',
        'json_data',
    ];

    protected $casts = [
        'json_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}