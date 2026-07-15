<?php

namespace WishgranterProject\MusicRadar\Sorting;

/**
 * Represents how relevant an individual result is to the search.
 */
interface RelevanceInterface
{
    /**
     * Adds a score to the relevance.
     *
     * @var WishgranterProject\MusicRadar\Sorting\ScoreInterface $score
     *   The new score.
     */
    public function addScore(ScoreInterface $score): void;

    /**
     * Returns the scores.
     *
     * @return WishgranterProject\MusicRadar\Sorting\ScoreInterface[]
     *   The scores.
     */
    public function getScores(): array;

    /**
     * Sums the points of all scores.
     *
     * @return int
     *   The total points scored by the result.
     */
    public function getTotal(): int;

    /**
     * Returns an array representation of the object.
     *
     * Useful for data transfer.
     *
     * @return array
     *   The object as an array.
     */
    public function toArray(): array;
}
