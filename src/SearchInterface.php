<?php

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface;

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
     * Adds a criteria to sort the results.
     *
     * @param WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface $criteria
     *   A criteria to help sort the search results.
     *
     * @return self
     *   Returns itself.
     */
    public function addCriteria(CriteriaInterface $criteria): SearchInterface;

    /**
     * Sets the average relevance we are aiming for in search results.
     *
     * @param int $points
     *   The average punctuation.
     *
     * @return self
     *   Returns itself.
     */
    public function setAverageRelevance(int $points): SearchInterface;

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
