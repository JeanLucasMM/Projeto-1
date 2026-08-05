<?php

namespace App\Builders\Objects;

use App\ViewModels\TrackerViewModel;

class Feature
{
    public string $title;

    public string $text = '';

    /** @var TrackerViewModel[] */
    public array $trackers = [];

    /** @var string[] */
    public array $tags = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function tag(string $tag): static
    {
        if (!in_array($tag, $this->tags, true)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function tracker(TrackerViewModel $tracker): static
    {
        $this->trackers[] = $tracker;

        return $this;
    }
}