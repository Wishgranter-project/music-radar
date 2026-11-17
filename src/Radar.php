<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ProbeInterface;
use WishgranterProject\MusicRadar\Searching\Search;
use WishgranterProject\MusicRadar\Searching\SearchInterface;

class Radar
{
    /**
     * The probes we'll be deploying to find our music.
     *
     * @var WishgranterProject\MusicRadar\ProbeSlot[]
     */
    protected array $probes = [];

    /**
     * Adds a probe.
     *
     * @param WishgranterProject\MusicProbe\ProbeInterface $probe
     *   A probe to search for music.
     * @param int $priority
     *   The priority, probes with higher priority will be deployed first.
     *
     * @return self
     *   Returns itself.
     */
    public function addProbe(ProbeInterface $probe, int $priority = 0): Radar
    {
        $this->probes[] = new ProbeSlot($probe, $priority);
        if (count($this->probes) > 1) {
            $this->sortProbes();
        }

        return $this;
    }

    /**
     * Instantiate a Search object.
     *
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   The description of a music.
     *
     * @return WishgranterProject\MusicRadar\Searching\SearchInterface
     *   The new search object.
     */
    public function searchFor(DescriptionInterface $description): SearchInterface
    {
        $probes = array_map(function ($slot) {
            return $slot->probe;
        }, $this->probes);

        return new Search($description, $probes);
    }

    /**
     * Sorts the probes based on their priority.
     *
     * In ascending order.
     */
    protected function sortProbes(): void
    {
        usort($this->probes, function ($p1, $p2) {
            return $p1->priority <=> $p2->priority;
        });
    }
}
