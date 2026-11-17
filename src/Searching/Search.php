<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\ProbeInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriterionInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\TitleCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\SoundtrackCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\ArtistCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\ArtistFeaturedCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\UndesirableCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\LeftOverCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\LiveEventDateCriterion;
use WishgranterProject\MusicRadar\Searching\Sorting\RelevanceInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\Relevance;

/**
 * {@inheritdoc}
 */
class Search implements SearchInterface
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   The description of a music.
     * @param WishgranterProject\MusicProbe\ProbeInterface[] $withProbes
     *   The probes we'll use to find our music.
     * @param array $criteria
     *   Criteria to judge how closely each resource matches the description.
     * @param int $minimumScore
     *   The minimum relevance score we are aiming for.
     * @param int $minimumResults
     *   The minimum number of relevant results we are aiming for.
     */
    public function __construct(
        protected DescriptionInterface $description,
        protected array $withProbes = [],
        protected array $criteria = [],
        protected int $minimumScore = 20,
        protected int $minimumResults = 1,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(DescriptionInterface $description): SearchInterface
    {
        $this->description = $description;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCriterion(CriterionInterface $criteria): SearchInterface
    {
        $this->criteria[$criteria->getId()] = $criteria;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinimumRelevanceScore(int $points): SearchInterface
    {
        $this->minimumScore = $points;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinimumRelevantResults(int $count): SearchInterface
    {
        $this->minimumResults = $count;
        return $this;
    }

    /**
     * Adds a built-in, opinionated pre-set of criteria to sort resources.
     *
     * @return self
     *   Returns itself.
     */
    public function addDefaultCriteria(): SearchInterface
    {
        // Weight.
        $couldDoWithout = -1;
        $absolutelyNot = -40;
        $excellent = 10;

        $this
            ->addCriterion(new TitleCriterion($excellent))
            ->addCriterion(new ArtistCriterion($excellent))
            ->addCriterion(new ArtistFeaturedCriterion($excellent))
            ->addCriterion(new SoundtrackCriterion($excellent));

        //------------------------------------

        $indifferentTowards = [
            'lyrics',
            'official lyric video',
            'official music video',
            'official video',
        ];

        $this
            ->addCriterion(new LeftOverCriterion($couldDoWithout, $indifferentTowards));

        //------------------------------------

        $undesirables = [
            'acoustic'   => $couldDoWithout,
            'demotape'   => $couldDoWithout,
            'demo'       => $couldDoWithout,
            'remix'      => $couldDoWithout,
            'remixed'    => $couldDoWithout,
            'remaster'   => $couldDoWithout,
            'remastered' => $couldDoWithout,
            'tutorial'   => $absolutelyNot,
            'cover'      => $absolutelyNot,
            'live'       => $absolutelyNot,
            'tour'       => $absolutelyNot,
            'full album' => $absolutelyNot,
            'reaction'   => $absolutelyNot,
            'karaoke'    => $absolutelyNot,
        ];

        if ($this->description->getCover()) {
            unset($undesirables['cover']);
        }

        foreach ($undesirables as $term => $weight) {
            $this->addCriterion(new UndesirableCriterion($weight, $term));
        }

        //------------------------------------

        $this
            ->addCriterion(new LiveEventDateCriterion($absolutelyNot));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function find(): SearchResultsInterface
    {
        $findings = new SearchResults();

        /**
         * Iterates through the probes, though not all probes may be deployed.
         * If enough adequate results are found, we return earlier.
         */
        foreach ($this->withProbes as $k => $probe) {
            $newResults = $this->deployProbe($probe);
            $findings   = $this->mergeSearchResults($findings, $newResults);
            $findings   = $k > 0
                ? $this->removeDuplicates($findings)
                : $findings;

            // No results, next source...
            if ($findings->count() == 0) {
                continue;
            }

            // We found enough results adequately matching our search, let's stop here.
            if ($this->countResultsScoringAtLeast($findings, $this->minimumScore) >= $this->minimumResults) {
                break;
            }

            // Next source it is then.
        }

        $findings = $this->sortResults($findings);
        return $findings;
    }

    /**
     * Sort search results by comparing their relevance with each other's.
     *
     * @param WishgranterProject\MusicRadar\SearchResultsInterface $searchResults
     *   Sorted search result.
     */
    protected function sortResults(SearchResultsInterface $searchResults): SearchResultsInterface
    {
        $items = $searchResults->getItems();
        usort($items, [$this, 'sort']);
        return new SearchResults($items);
    }

    /**
     * Compare the relevance score of search result items.
     *
     * @param WishgranterProject\MusicRadar\SearchResultItem $result1
     *   Search result.
     * @param WishgranterProject\MusicRadar\SearchResultItem $result2
     *   Search result.
     *
     * @return int
     *   0 if equivalent.
     *   -1 if $result1 scores higher.
     *   1 if $result2 scores higher.
     */
    protected function sort(SearchResultItem $result1, SearchResultItem $result2): int
    {
        $resource1 = $result1->getResource();
        $resource2 = $result2->getResource();

        if (
            $resource1->getId()       == $resource2->getId() &&
            $resource1->getSourceId() == $resource2->getSourceId()
        ) {
            return 0;
        }

        $score1 = $result1->getRelevance()?->getTotal() ?: 0;
        $score2 = $result2->getRelevance()?->getTotal() ?: 0;

        return $score2 <=> $score1;
    }

    /**
     * Returns the number of results that scored $minScore or more.
     *
     * @param int $minScore
     *   Minimum score.
     * @param WishgranterProject\MusicRadar\SearchResultsInterface $searchResults
     *   Search results.
     *
     * @return int
     *   Number of matching results.
     */
    protected function countResultsScoringAtLeast(SearchResultsInterface $searchResults, int $minScore): int
    {
        return array_reduce($searchResults->getItems(), function ($carry, $result) use ($minScore) {
            $total = $result->getRelevance()?->getTotal() ?: 0;
            $carry += $total >= $minScore ? 1 : 0;
            return $carry;
        }, 0);
    }

    /**
     * Remove duplicated results.
     *
     * @param WishgranterProject\MusicRadar\SearchResultsInterface $searchResults
     *   Search results.
     *
     * @return WishgranterProject\MusicRadar\SearchResultsInterface
     *   Search results with duplicated results removed.
     */
    protected function removeDuplicates(SearchResultsInterface $searchResults): SearchResultsInterface
    {
        $uniqueArray = [];

        $items = $searchResults->getItems();
        foreach ($items as $item) {
            $resource = $item->getResource();
            $id = $resource->getId() ?: $resource->getSrc() ?: $resource->getHref() ?: $resource->getTitle();
            $key = $item->getResource()->getSourceId() . ':' . $id;
            if (isset($uniqueArray[$key])) {
                continue;
            }

            $uniqueArray[$key] = $item;
        }

        return new SearchResults(array_values($uniqueArray));
    }

    /**
     * Merge multiple search results into a single object.
     *
     * @param array|WishgranterProject\MusicRadar\SearchResultsInterface ...$args
     *   Results to merge.
     *
     * @return WishgranterProject\MusicRadar\SearchResultsInterface
     *   The results merged.
     */
    protected function mergeSearchResults($args): SearchResultsInterface
    {
        $args = func_get_args();
        $array = [];

        foreach ($args as $arg) {
            if ($arg instanceof SearchResultsInterface) {
                $array = array_merge($array, $arg->getItems());
            } else {
                $array = array_merge($array, $arg);
            }
        }

        return new SearchResults($array);
    }

    /**
     * Deploy the probe and returns the results.
     *
     * @param WishgranterProject\MusicProbe\ProbeInterface
     *   The probe to search music with.
     *
     * @return WishgranterProject\MusicRadar\SearchResultsInterface
     *   The search results.
     */
    protected function deployProbe(ProbeInterface $probe): SearchResultsInterface
    {
        $resources = $probe->search($this->description);

        $results = [];
        foreach ($resources as $resource) {
            $relevance = $this->generateRelevance($resource, $this->criteria, $this->description);
            $results[] = new SearchResultItem($resource, $relevance);
        }

        return new SearchResults($results);
    }

    /**
     * Generates a relevance based on our criteria.
     *
     * @param WishgranterProject\MusicProbe\ResourceInterface $onResource
     *   The resource to tally.
     * @param WishgranterProject\MusicRadar\Searching\Sorting\CriterionInterface[] $accordingToCriteria
     *   The list criteria.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $andDescription
     *   The description used as the base.
     *
     * @return WishgranterProject\MusicRadar\Searching\Sorting\RelevanceInterface
     *   The relevance scored by the resource.
     */
    protected function generateRelevance(
        ResourceInterface $onResource,
        array $accordingToCriteria,
        DescriptionInterface $andDescription,
    ): RelevanceInterface {
        $relevance = new Relevance();

        foreach ($accordingToCriteria as $criteria) {
            $score = $criteria->getScore($onResource, $andDescription);
            $relevance->addScore($score);
        }

        return $relevance;
    }
}
