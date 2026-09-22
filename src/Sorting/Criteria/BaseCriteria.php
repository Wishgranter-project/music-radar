<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicRadar\Sorting\ScoreInterface;
use WishgranterProject\MusicRadar\Sorting\Score;

abstract class BaseCriteria implements CriteriaInterface
{
    /**
     * Constructor.
     *
     * @param int $weight
     *   A score multiplier, how much this criteria weights when tallying the
     *   resource's relevance to the search.
     */
    public function __construct(protected int $weight = 1)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:abstract';
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getScore(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): ScoreInterface
    {
        $points = $this->getPoints($forResource, $basedOnDescription);
        return new Score($this, $points, $this->weight);
    }

    /**
     * Returns how many points the resource gains.
     *
     * @return int
     *   Points.
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        return 0;
    }
}
