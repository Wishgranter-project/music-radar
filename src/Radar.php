<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ProbeInterface;
use WishgranterProject\MusicRadar\Searching\Search;
use WishgranterProject\MusicRadar\Searching\SearchInterface;
use WishgranterProject\MusicRadar\Searching\CriteriaInterface;
use WishgranterProject\MusicRadar\Searching\ProbeDeployStrategyInterface;

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
     * @param null|WishgranterProject\MusicRadar\Searching\CriteriaInterface $criteria
     *   The criteria to sort search results.
     * @param null|WishgranterProject\MusicRadar\Searching\ProbeDeployStrategyInterface $strategy
     *   Strategy.
     *
     * @return WishgranterProject\MusicRadar\Searching\SearchInterface
     *   New search object.
     */
    public function newSearch(
        ?CriteriaInterface $criteria = null,
        ?ProbeDeployStrategyInterface $strategy = null,
    ): SearchInterface {
        $probes = array_map(function ($slot) {
            return $slot->probe;
        }, $this->probes);

        return new Search($probes, $criteria, $strategy);
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
