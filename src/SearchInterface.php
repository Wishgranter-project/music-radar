<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicRadar\Sorting\Criteria\CriterionInterface;

/**
 * Given the description of a music, searches for matching resources.
 *
 * Within a finite list of probes and returns results ordered by their
 * relevance, that is, how closely they match the description.
 */
interface SearchInterface
{
    /**
     * Set the description of the music we are looking for.
     *
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description.
     *   Music description.
     *
     * @return self
     *   Returns itself.
     */
    public function setDescription(DescriptionInterface $description): SearchInterface;

    /**
     * Adds a criterion to sort the results.
     *
     * @param WishgranterProject\MusicRadar\Sorting\Criteria\CriterionInterface $criteria
     *   A criterion to help sort the search results.
     *
     * @return self
     *   Returns itself.
     */
    public function addCriterion(CriterionInterface $criteria): SearchInterface;

    /**
     * Sets the minimum relevance score we are aiming for in search results.
     *
     * @param int $points
     *   The minimum score.
     *
     * @return self
     *   Returns itself.
     */
    public function setMinimumRelevanceScore(int $points): SearchInterface;

    /**
     * Sets the minimum number of adequate search results we are aiming for.
     *
     * @param int $count
     *   The minimum count.
     *
     * @return self
     *   Returns itself.
     */
    public function setMinimumRelevantResults(int $count): SearchInterface;

    /**
     * Deploy the probes and returns search results.
     *
     * Ordered by how closely they match the description.
     *
     * @return WishgranterProject\MusicRadar\SearchResultsInterface
     *   Resources that match our $description.
     */
    public function find(): SearchResultsInterface;
}
