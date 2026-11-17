<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicRadar\Sorting\ScoreInterface;
use WishgranterProject\MusicRadar\Sorting\Score;

abstract class BaseCriterion implements CriterionInterface
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
        return 'criterion:abstract';
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

    /**
     * Checks for the presence of a substring.
     *
     * @todo Move this to WishgranterProject\MusicProbe\Helper\Text.
     *
     * @param string $haystack
     *   The string to search in.
     * @param string|string[] $needles
     *   The substring(s) to search for.
     *
     * @return bool
     *   True if $haystack contains one or more $needles.
     */
    protected function containsText(string $haystack, $needles): bool
    {
        $needles = (array) $needles;

        foreach ($needles as $needle) {
            $pattern = '/(?<![\p{L}\p{N}_])' . preg_quote($needle, '~') . '(?![\p{L}\p{N}_])/iu';

            if (preg_match($pattern, $haystack) === 1) {
                return true;
            }
        }

        return false;
    }
}
