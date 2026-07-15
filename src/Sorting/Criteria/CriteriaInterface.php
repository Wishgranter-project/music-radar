<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicRadar\Sorting\ScoreInterface;

/**
 * Represents a criteria to determine how close a resource matches the
 * description.
 */
interface CriteriaInterface
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
     * @return WishgranterProject\MusicRadar\Sorting\ScoreInterface
     *   A score object.
     */
    public function getScore(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): ScoreInterface;
}
