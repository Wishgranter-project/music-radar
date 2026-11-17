<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;

/**
 * The resource scores:
 *  0 if the description specifies no title to begin with.
 * +1 if the title can be found in the resource's title.
 * -1 if it cannot.
 */
class TitleCriterion extends BaseCriterion implements CriterionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criterion:title';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        if (!$basedOnDescription->getTitle()) {
            return 0;
        }

        return $this->containsText($forResource->getTitle(), $basedOnDescription->getTitle())
            ?  1
            : -1;
    }
}
