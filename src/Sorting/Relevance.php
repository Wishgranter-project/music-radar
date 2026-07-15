<?php

namespace WishgranterProject\MusicRadar\Sorting;

/**
 * {@inheritdoc}
 */
class Relevance implements RelevanceInterface
{
    /**
     * @param WishgranterProject\MusicRadar\Sorting\ScoreInterface[]
     *   A list of scores with the points earned and weight.
     */
    protected array $scores = [];

    /**
     * {@inheritdoc}
     */
    public function addScore(ScoreInterface $score): void
    {
        $this->scores[] = $score;
    }

    /**
     * {@inheritdoc}
     */
    public function getScores(): array
    {
        return $this->scores;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(): int
    {
        $count = 0;

        foreach ($this->scores as $score) {
            $points = $score->getTotal();
            $count += $points;
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $cr = [];
        foreach ($this->scores as $score) {
            $cr[] = $score->toArray();
        }

        return [
            'scores' => $cr,
            'total' => $this->getTotal()
        ];
    }
}
