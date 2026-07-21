<?php

namespace App\DTOs\Combat;

class CombatParticipant
{
    public function __construct(

        public int $id,

        public string $type,

        public string $name,

        public int $initiative,

        public ?int $currentHp,

        public ?int $maxHp,

        public bool $dead,

        public bool $active = false,

        public mixed $model = null

    ) {
    }

    public function isNpc(): bool
    {
        return $this->type === 'npc';
    }

    public function isPlayer(): bool
    {
        return $this->type === 'player';
    }

    public function lifePercent(): int
    {
        if (!$this->maxHp) {
            return 100;
        }

        return (int) round(
            ($this->currentHp / $this->maxHp) * 100
        );
    }
}