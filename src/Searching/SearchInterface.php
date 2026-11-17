<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriterionInterface;

/**
 * Given the description of a music, searches for matching resources.
 *
 * Within a finite list of probes and returns results ordered by their
 * relevance, that is, how closely they match the description.
 */
interface SearchInterface
{
    /**
     * Deploy the probes and returns search results.
     *
     * Ordered by how closely they match the description.
     *
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   Description of a music.
     *
     * @return WishgranterProject\MusicRadar\Searching\SearchResultsInterface
     *   Resources that match our $description.
     */
    public function find(DescriptionInterface $description): SearchResultsInterface;
}
