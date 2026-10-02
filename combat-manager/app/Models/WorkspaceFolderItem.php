<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkspaceFolderItem extends Model
{
    protected $fillable = [
        'folder_id',
        'folderable_type',
        'folderable_id',
        'position',
        'metadata',
    ];

    protected $casts = [
        'folder_id' => 'integer',
        'folderable_id' => 'integer',
        'position' => 'integer',
        'metadata' => 'array',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(
            WorkspaceFolder::class,
            'folder_id'
        );
    }

    public function folderable(): MorphTo
    {
        return $this->morphTo();
    }
}
