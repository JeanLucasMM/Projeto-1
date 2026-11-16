<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCharacter extends Model
{
    protected $fillable = [
        'campaign_id',
        'character_id',
        'is_active',
        'experience_points',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'experience_points' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
