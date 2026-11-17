<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting;

use WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriterionInterface;

/**
 * {@inheritdoc}
 */
class Score implements ScoreInterface
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriterionInterface $criteria
     *   The criteria used for the measurement.
     * @param int $points
     *   The points scored.
     * @param int $weight
     *   How much this score weights in the overall search.
     */
    public function __construct(
        protected CriterionInterface $criteria,
        protected int $points,
        protected int $weight,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getCriteria(): CriterionInterface
    {
        return $this->criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints(): int
    {
        return $this->points;
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
    public function getTotal(): int
    {
        return $this->points * $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'points'   => $this->points,
            'weight'   => $this->weight,
            'total'    => $this->getTotal(),
            'criteria' => $this->criteria->getId()
        ];
    }
}
