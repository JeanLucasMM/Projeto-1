<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | NPCs
    |--------------------------------------------------------------------------
    */

    public function npcs(): HasMany
    {
        return $this->hasMany(Npc::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Campanhas criadas como Mestre
    |--------------------------------------------------------------------------
    */

    public function ownedCampaigns(): HasMany
    {
        return $this->hasMany(
            Campaign::class,
            'owner_user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ParticipaÃ§Ãµes em campanhas
    |--------------------------------------------------------------------------
    */

    public function campaignMemberships(): HasMany
    {
        return $this->hasMany(
            CampaignMember::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Convites recebidos
    |--------------------------------------------------------------------------
    */

    public function campaignInvitations(): HasMany
    {
        return $this->hasMany(
            CampaignInvitation::class,
            'invited_user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Characters
    |--------------------------------------------------------------------------
    */

    public function characters(): HasMany
    {
        return $this->hasMany(
            Character::class
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}