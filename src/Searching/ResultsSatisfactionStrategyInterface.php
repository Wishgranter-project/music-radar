<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

/**
 * Defines a strategy to decide when search results are adequate.
 */
interface ResultsSatisfactionStrategyInterface
{
    /**
     * Checks if the current search results are satisfying.
     *
     * @param WishgranterProject\MusicRadar\Searching\SearchResultsInterface $searchResults
     *   Search results.
     *
     * @return bool
     *   True if the results are satisfying, false if we should keep looking.
     */
    public function areTheResultsSatisfying(SearchResultsInterface $searchResults): bool;
}
