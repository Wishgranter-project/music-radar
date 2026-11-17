<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;

/**
 * Scores on things that do not make part of the description.
 *
 * Substrings inside parenthesis, commas and other fluff.
 * The more there are, greater the score.
 */
class LeftOverCriterion extends BaseCriterion implements CriterionInterface
{
    /**
     * Constructor.
     *
     * @param int $weight
     *   A score multiplier, how much this criteria weights when tallying the
     *   resource's relevance to the search.
     * @param array string[] $indifferent
     *   List of terms to be ignored.
     */
    public function __construct(
        protected int $weight = -1,
        protected array $indifferent = [],
    ) {
        parent::__construct($weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criterion:leftover';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        $titleMinusDescription = strtolower($forResource->getTitle());

        if ($basedOnDescription->getTitle()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getTitle(), '', $titleMinusDescription);
        }

        if ($basedOnDescription->getArtist()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getArtist(), '', $titleMinusDescription);
        }

        if ($basedOnDescription->getFeaturing()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getFeaturing(), '', $titleMinusDescription);
        }

        if ($basedOnDescription->getAlbum()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getAlbum(), '', $titleMinusDescription);
        }

        if ($basedOnDescription->getCover()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getCover(), '', $titleMinusDescription);
        }

        if ($basedOnDescription->getGenre()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getGenre(), '', $titleMinusDescription);
        }

        if ($basedOnDescription->getSoundtrack()) {
            $titleMinusDescription = str_ireplace($basedOnDescription->getSoundtrack(), '', $titleMinusDescription);
        }

        $titleMinusDescription = trim($titleMinusDescription);

        $split = preg_split('/[^\w\'\. ]/', $titleMinusDescription);

        $leftOvers = [];
        foreach ($split as $p) {
            $leftOvers[] = trim($p);
        }
        $leftOvers = array_filter($leftOvers);

        if ($this->indifferent && $leftOvers) {
            $intersect = array_intersect($this->indifferent, $leftOvers);
            $leftOvers = array_diff($leftOvers, $intersect);
        }

        return count($leftOvers);
    }
}
