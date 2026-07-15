<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\Helper\Text;

/**
 * Scores points on the presence of specific terms.
 * The more it appears, greater the score.
 */
class UndesirableCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Constructor.
     *
     * @param int $weight
     *   How much this criteria weights when tallying the resource's likeness
     *   to the description.
     * @param string $term
     *   A term we rather not have in our search results.
     */
    public function __construct(
        protected int $weight = -1,
        protected string $term = '',
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:undesirable:' . $this->term;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        $score = 0;

        // If the term actually makes part of of the description, counting it would be contraproductive.
        if ($this->isTermInDescription($this->term, $basedOnDescription)) {
            return 0;
        }

        $score = Text::substrIntersect($forResource->getTitle(), $this->term);

        if (!$score && $forResource->getDescription()) {
            $score = Text::substrIntersect($forResource->getDescription(), $this->term);
        }

        return $score;
    }

    /**
     * Checks the presence of $term in $description.
     *
     * @param string $term
     *   The undesirable term.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   The description.
     *
     * @return bool
     *   Whether or not $term is present in $description.
     */
    protected function isTermInDescription(string $term, DescriptionInterface $description): bool
    {
        if ($description->getTitle() && Text::substrIntersect($description->getTitle(), $term)) {
            return true;
        }

        if ($description->getArtist() && Text::substrIntersect($description->getArtist(), $term)) {
            return true;
        }

        if ($description->getSoundtrack() && Text::substrIntersect($description->getSoundtrack(), $term)) {
            return true;
        }

        return false;
    }
}
