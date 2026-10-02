<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpellImportRun extends Model
{
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PARTIAL = 'partial';

    protected $fillable = [
        'provider',
        'status',
        'index_url',

        'source_count',
        'files_processed',
        'spells_seen',

        'created_count',
        'updated_count',
        'unchanged_count',
        'missing_count',
        'error_count',

        'manifest',
        'errors',

        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'source_count' => 'integer',
        'files_processed' => 'integer',
        'spells_seen' => 'integer',

        'created_count' => 'integer',
        'updated_count' => 'integer',
        'unchanged_count' => 'integer',
        'missing_count' => 'integer',
        'error_count' => 'integer',

        'manifest' => 'array',
        'errors' => 'array',

        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }
}
