<?php

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\ProbeInterface;
use WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface;
use WishgranterProject\MusicRadar\Sorting\Criteria\TitleCriteria;
use WishgranterProject\MusicRadar\Sorting\Criteria\SoundtrackCriteria;
use WishgranterProject\MusicRadar\Sorting\Criteria\ArtistCriteria;
use WishgranterProject\MusicRadar\Sorting\Criteria\ArtistFeaturedCriteria;
use WishgranterProject\MusicRadar\Sorting\Criteria\UndesirableCriteria;
use WishgranterProject\MusicRadar\Sorting\Criteria\LeftOverCriteria;
use WishgranterProject\MusicRadar\Sorting\Criteria\LiveEventDateCriteria;
use WishgranterProject\MusicRadar\Sorting\RelevanceInterface;
use WishgranterProject\MusicRadar\Sorting\Relevance;

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
     * @param int $averaging
     *   The average relevance score we are aiming for.
     */
    public function __construct(
        protected DescriptionInterface $description,
        protected array $withProbes = [],
        protected array $criteria = [],
        protected int $averaging = 20,
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
    public function addCriteria(CriteriaInterface $criteria): SearchInterface
    {
        $this->criteria[$criteria->getId()] = $criteria;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAverageRelevance(int $points): SearchInterface
    {
        $this->averaging = $points;
        return $this;
    }

    /**
     * A built-in pre set of criteria to sort resources.
     *
     * @return self
     *   Returns itself.
     */
    public function addDefaultCriteria(): SearchInterface
    {
        // Weight.
        $couldDoWithout = -1;
        $absolutelyNot = -40;
        $excelent = 10;

        $this
            ->addCriteria(new TitleCriteria($excelent))
            ->addCriteria(new ArtistCriteria($excelent))
            ->addCriteria(new ArtistFeaturedCriteria($excelent))
            ->addCriteria(new SoundtrackCriteria($excelent));

        //------------------------------------

        $indifferentTowards = [
            'lyrics',
            'official lyric video',
            'official music video'
        ];

        $this
            ->addCriteria(new LeftOverCriteria($couldDoWithout, $indifferentTowards));

        //------------------------------------

        $undesirables = [
            'cover'      => $couldDoWithout,
            'acoustic'   => $couldDoWithout,
            'demotape'   => $couldDoWithout,
            'demo'       => $couldDoWithout,
            'remixed'    => $couldDoWithout,
            'remastered' => $couldDoWithout,
            'remix'      => $couldDoWithout,
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
            $this->addCriteria(new UndesirableCriteria($weight, $term));
        }

        //------------------------------------

        $this
            ->addCriteria(new LiveEventDateCriteria($absolutelyNot));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function find(): SearchResultsInterface
    {
        $findings = new SearchResults();

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

            // 1 result scoring the avarege is good enough, let's stop here.
            if ($this->countResultsScoringAtLeast($findings, $this->averaging) >= 1) {
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

        $score1 = $result1->getRelevance()->getTotal();
        $score2 = $result2->getRelevance()->getTotal();

        if ($score1 == $score2) {
            return 0;
        }

        return $score1 > $score2
            ? -1
            :  1;
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
            $carry += $result->getRelevance()->getTotal() >= $minScore ? 1 : 0;
            return $carry;
        });
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
            $key = $item->getResource()->getSourceId() . ':' . $item->getResource()->getId();
            if (isset($uniqueArray[$key])) {
                continue;
            }

            $uniqueArray[$key] = $item;
        }

        return new SearchResults(array_values($uniqueArray));
    }

    /**
     * Merge multiple search results into a single object.
     */
    protected function mergeSearchResults()
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
     * @param WishgranterProject\MusicRadar\Sorting\CriteriaInterface[] $accordingToCriteria
     *   The list criteria.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $andDescription
     *   The description used as the base.
     *
     * @return WishgranterProject\MusicRadar\Sorting\RelevanceInterface
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
