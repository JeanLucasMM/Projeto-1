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
    ];

    protected $casts = [
        'json_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}