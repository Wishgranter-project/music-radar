<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Sorting;

/**
 * {@inheritdoc}
 */
class Relevance implements RelevanceInterface
{
    /**
     * @param int|null
     *   Cache for relevance sum to avoid iterating over and over.
     */
    protected ?int $total = null;

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
        $this->total = null;
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
        if (!is_null($this->total)) {
            return $this->total;
        }

        $count = 0;

        foreach ($this->scores as $score) {
            $points = $score->getTotal();
            $count += $points;
        }

        return $this->total = $count;
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
