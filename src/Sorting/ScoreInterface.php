<?php

namespace WishgranterProject\MusicRadar\Sorting;

use WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface;

/**
 * Represents how a resource compares to a description given a criteria.
 */
interface ScoreInterface
{
    /**
     * Returns the criteria.
     *
     * @return WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface
     *   The criteria.
     */
    public function getCriteria(): CriteriaInterface;

    /**
     * Returns the points.
     *
     * @return int
     *   The points.
     */
    public function getPoints(): int;

    /**
     * Returns the weight.
     *
     * @return int
     *   The weight.
     */
    public function getWeight(): int;

    /**
     * Returns the total score.
     *
     * @return int
     *   The score.
     */
    public function getTotal(): int;

    /**
     * Returns an array representation of the object.
     *
     * Useful to render it as a json string.
     *
     * @return array
     *   The object as an array.
     */
    public function toArray(): array;
}
