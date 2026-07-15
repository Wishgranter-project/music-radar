<?php

namespace WishgranterProject\MusicRadar\Sorting;

use WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface;

/**
 * {@inheritdoc}
 */
class Score implements ScoreInterface
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicRadar\Sorting\Criteria\CriteriaInterface $criteria
     *   The criteria used for the measurement.
     * @param int $points
     *   The points scored.
     * @param int $weight
     *   How much this score weights in the overall search.
     */
    public function __construct(
        protected CriteriaInterface $criteria,
        protected int $points,
        protected int $weight,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getCriteria(): CriteriaInterface
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
