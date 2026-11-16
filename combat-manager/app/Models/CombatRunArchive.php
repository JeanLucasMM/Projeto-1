<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombatRunArchive extends Model
{
    protected $fillable = ['combat_run_id', 'snapshot', 'statistics', 'image_svg', 'saved_at'];

    protected $casts = [
        'snapshot' => 'array',
        'statistics' => 'array',
        'saved_at' => 'datetime',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(CombatRun::class, 'combat_run_id');
    }
}
