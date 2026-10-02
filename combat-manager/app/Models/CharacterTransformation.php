<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CharacterTransformation extends Model
{
    public const TYPE_CHARACTER_SHEET =
        'character_sheet';

    public const TYPE_MONSTER_SHEET =
        'monster_sheet';

    public const CASTING_NONE =
        'none';

    public const CASTING_SPELL =
        'spell';

    public const CASTING_BONUS_ACTION =
        'bonus_action';

    public const CASTING_ACTION =
        'action';

    public const DURATION_INDEFINITE =
        'indefinite';

    public const DURATION_ROUNDS =
        'rounds';

    public const DURATION_MINUTES =
        'minutes';

    public const DURATION_HOURS =
        'hours';

    public const TRIGGER_HALF_HP =
        'half_hp';

    public const TRIGGER_ONE_OR_ZERO_HP =
        'one_or_zero_hp';

    public const RECOVERY_DAILY =
        'daily';

    public const RECOVERY_SHORT_REST =
        'short_rest';

    public const EXHAUSTION_ON_ACTIVATION =
        'activation';

    public const EXHAUSTION_ON_END =
        'end';


    protected $fillable = [
        'character_id',
        'name',
        'type',
        'form_character_id',
        'source_npc_id',
        'image_path',

        'casting_time',
        'requires_concentration',

        'duration_type',
        'duration_value',

        'is_special',
        'special_trigger',

        'uses_max',
        'uses_current',
        'recovery',

        'applies_exhaustion',
        'exhaustion_levels',
        'exhaustion_timing',

        'sort_order',
        'settings',
    ];


    protected $casts = [
        'character_id' =>
            'integer',

        'form_character_id' =>
            'integer',

        'source_npc_id' =>
            'integer',

        'requires_concentration' =>
            'boolean',

        'duration_value' =>
            'integer',

        'is_special' =>
            'boolean',

        'uses_max' =>
            'integer',

        'uses_current' =>
            'integer',

        'applies_exhaustion' =>
            'boolean',

        'exhaustion_levels' =>
            'integer',

        'sort_order' =>
            'integer',

        'settings' =>
            'array',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relações
    |--------------------------------------------------------------------------
    */

    public function character(): BelongsTo
    {
        return $this->belongsTo(
            Character::class
        );
    }


    public function formCharacter(): BelongsTo
    {
        return $this->belongsTo(
            Character::class,
            'form_character_id'
        );
    }


    public function sourceNpc(): BelongsTo
    {
        return $this->belongsTo(
            Npc::class,
            'source_npc_id'
        );
    }


    public function activeState(): HasOne
    {
        return $this->hasOne(
            CharacterTransformationState::class,
            'character_transformation_id'
        )
            ->where(
                'is_active',
                true
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCharacterSheet(): bool
    {
        return $this->type
            === self::TYPE_CHARACTER_SHEET;
    }


    public function isMonsterSheet(): bool
    {
        return $this->type
            === self::TYPE_MONSTER_SHEET;
    }


    public function hasLimitedUses(): bool
    {
        return $this->uses_max !== null;
    }


    public function hasAvailableUse(): bool
    {
        if (!$this->hasLimitedUses()) {
            return true;
        }

        return max(
            0,
            (int) $this->uses_current
        ) > 0;
    }


    public function isTimed(): bool
    {
        return $this->duration_type
            !== self::DURATION_INDEFINITE
            && $this->duration_value !== null;
    }
}