<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ProbeInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\Criteria;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriteriaInterface;

/**
 * {@inheritdoc}
 */
class Search implements SearchInterface
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicProbe\ProbeInterface[] $withProbes
     *   The probes we'll use to find our music.
     * @param null|WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriteriaInterface $criteria
     *   The criteria to sort search results.
     * @param null|WishgranterProject\MusicRadar\Searching\ResultsSatisfactionStrategyInterface $strategy
     *   Strategy to decide when our search results are adequate.
     */
    public function __construct(
        protected array $withProbes = [],
        protected ?CriteriaInterface $criteria = null,
        protected ?ResultsSatisfactionStrategyInterface $strategy = null,
    ) {
        $this->criteria = $criteria
            ? $criteria
            : Criteria::getDefaultConfiguration();

        $this->strategy = $strategy
            ? $strategy
            : new DefaultStrategy();
    }

    /**
     * {@inheritdoc}
     */
    public function find(DescriptionInterface $description): SearchResultsInterface
    {
        $findings = new SearchResults();

        /**
         * Iterates through the probes, though not all probes may be deployed.
         * If enough adequate results are found, we return earlier.
         */
        foreach ($this->withProbes as $k => $probe) {
            $newResults = $this->deployProbe($probe, $description);
            $findings   = $findings->withResultsMerged($newResults);
            $findings   = $k > 0
                ? $findings->withDuplicatesRemoved()
                : $findings;

            // No results, next source...
            if ($findings->count() == 0) {
                continue;
            }

            // We found enough results adequately matching our search, let's stop here.
            if ($this->strategy->areTheResultsSatisfying($findings)) {
                break;
            }

            // Next source it is then.
        }

        $findings->sort();
        return $findings;
    }

    /**
     * Deploy the probe and returns the results.
     *
     * @param WishgranterProject\MusicProbe\ProbeInterface $probe
     *   The probe to search music with.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   The description of the music we are looking for.
     *
     * @return WishgranterProject\MusicRadar\SearchResultsInterface
     *   The search results.
     */
    protected function deployProbe(ProbeInterface $probe, DescriptionInterface $description): SearchResultsInterface
    {
        $resources = $probe->search($description);

        $results = [];
        foreach ($resources as $resource) {
            $relevance = $this->criteria->generateRelevance($resource, $description);
            $results[] = new SearchResultItem($resource, $relevance);
        }

        return new SearchResults($results);
    }
}
