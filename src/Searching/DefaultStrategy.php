<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

/**
 * {@inheritdoc}
 */
class DefaultStrategy implements ProbeDeployStrategyInterface
{
    /**
     * Constructor.
     *
     * @param int $minimumScore
     *   The minimum relevance score we are aiming for in our search results.
     * @param int $minimumRelevantResults
     *   The minimum number of adequate search results that will satisfy.
     */
    public function __construct(
        protected int $minimumScore = 20,
        protected int $minimumRelevantResults = 1,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function areTheResultsSatisfying(SearchResultsInterface $searchResults): bool
    {
        return $searchResults->getItemsScoringAtLeast($this->minimumScore) >= $this->minimumRelevantResults;
    }
}
