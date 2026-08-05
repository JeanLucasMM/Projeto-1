<?php

namespace App\Builders;

use App\Builders\Objects\Ability;
use App\Builders\Objects\CombatData;
use App\Builders\Objects\Feature;
use App\Builders\Objects\Section;
use App\Builders\Objects\Skill;
use App\Builders\Objects\Spellcasting;
use App\Support\ChallengeRatings;

class NpcBuilder
{
    public string $name = '';

    public string $size = '';

    public array $types = [];

    public string $alignment = '';

    public float $challengeRating = 0;

    

    

    public CombatData $combat;

    public Spellcasting $spellcasting;

    /** @var Ability[] */
    public array $abilities = [];

    /** @var Skill[] */
    public array $skills = [];

    /** @var Section[] */
    public array $sections = [];

    /** @var Feature[] */
    public array $features = [];

    public array $savingThrows = [];

    public function __construct()
    {
        $this->combat = new CombatData();

        $this->spellcasting = new Spellcasting();
    }

    /*
    |--------------------------------------------------------------------------
    | Identity
    |--------------------------------------------------------------------------
    */

    public function name(string $value): static
    {
        $this->name = $value;

        return $this;
    }

    public function size(string $value): static
    {
        $this->size = $value;

        return $this;
    }

 public function types(array $values): static
{
    $this->types = [];

    foreach ($values as $value) {

        $this->addType($value);

    }

    return $this;
}

public function addType(string $value): static
{
    $value = trim($value);

    if ($value === '') {

        return $this;

    }

    if (!in_array($value, $this->types, true)) {

        $this->types[] = $value;

    }

    return $this;
}

public function removeType(string $value): static
{
    $this->types = array_values(

        array_filter(

            $this->types,

            fn($type) => $type !== $value

        )

    );

    return $this;
}

    public function alignment(string $value): static
    {
        $this->alignment = $value;

        return $this;
    }

    public function challengeRating(
    float|int|string $value
): static {

    $this->challengeRating = (float) $value;

    return $this;
}

    public function xp(): int
    {
        return ChallengeRatings::xp(
            $this->challengeRating
        );
    }

    public function proficiencyBonus(): int
    {
        return ChallengeRatings::proficiency(
            $this->challengeRating
    );
    }

    /*
    |--------------------------------------------------------------------------
    | Complex Objects
    |--------------------------------------------------------------------------
    */

    public function combat(): CombatData
    {
        return $this->combat;
    }

    public function spellcasting(): Spellcasting
    {
        return $this->spellcasting;
    }

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    */

    public function section(string $title): Section
    {
        foreach ($this->sections as $section) {

            if (strcasecmp($section->title, $title) === 0) {
                return $section;
            }

        }

        $section = new Section($title);

        $this->sections[] = $section;

        return $section;
    }

    public function addSection(string $title): Section
    {
        return $this->section($title);
    }

    /*
    |--------------------------------------------------------------------------
    | Abilities
    |--------------------------------------------------------------------------
    */

public function ability(string $name): Ability
{
    $key = strtolower($name);

    foreach ($this->abilities as $ability) {

        if ($ability->name === $key) {
            return $ability;
        }

    }

    $ability = new Ability($key);

    $this->abilities[] = $ability;

    return $ability;
}

    public function addAbility(
        string $name,
        int $value
    ): Ability {

        return $this
            ->ability($name)
            ->value($value);

    }

    /*
    |--------------------------------------------------------------------------
    | Skills
    |--------------------------------------------------------------------------
    */

    public function skill(
        string $name,
        string $ability = 'dex'
    ): Skill {

        $key = strtolower($name);

        foreach ($this->skills as $skill) {

            if ($skill->name === $key) {
                return $skill;
            }

        }

        $skill = new Skill(
            $key,
            $ability
        );

        $this->skills[] = $skill;

        return $skill;
    }




    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */

    public function feature(string $title): Feature
    {
        foreach ($this->features as $feature) {

            if (strcasecmp($feature->title, $title) === 0) {
                return $feature;
            }

        }

        $feature = new Feature($title);

        $this->features[] = $feature;

        return $feature;
    }

    public function addFeature(
        string $title,
        string $text = ''
    ): Feature {

        return $this
            ->feature($title)
            ->text($text);

    }
}