<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\ScoreInterface;

/**
 * Represents a criteria to determine how close a resource matches the
 * description.
 */
interface CriterionInterface
{
    /**
     * Returns an unique string to identify the criteria.
     *
     * @return string
     *   The id of the criteria.
     */
    public function getId(): string;

    /**
     * A score multiplier, how much this criteria weights.
     *
     * Used against other criteria.
     *
     * @return int
     */
    public function getWeight(): int;

    /**
     * Compares a resource with the description of a music and returns a score.
     *
     * @param WishgranterProject\MusicProbe\ResourceInterface $forResource
     *   The resource being scrutinized.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $basedOnDescription
     *   The description used as a base.
     *
     * @return WishgranterProject\MusicRadar\Searching\Sorting\ScoreInterface
     *   A score object.
     */
    public function getScore(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): ScoreInterface;
}
