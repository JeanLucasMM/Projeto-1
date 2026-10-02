<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkspaceFolder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_user_id',
        'scope_type',
        'scope_id',
        'parent_id',
        'name',
        'description',
        'content',
        'color',
        'text_color',
        'icon',
        'image_path',
        'position',
        'metadata',
        'is_system',
    ];

    protected $casts = [
        'owner_user_id' => 'integer',
        'scope_id' => 'integer',
        'parent_id' => 'integer',
        'position' => 'integer',
        'metadata' => 'array',
        'is_system' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_user_id'
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )->orderBy('position')->orderBy('name');
    }

    public function placements(): HasMany
    {
        return $this->hasMany(
            WorkspaceFolderItem::class,
            'folder_id'
        )->orderBy('position')->orderBy('id');
    }

    public function scopeForContext(
        Builder $query,
        string $scopeType,
        int $scopeId
    ): Builder {
        return $query
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId);
    }
}
