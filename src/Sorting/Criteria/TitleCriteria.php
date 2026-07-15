<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\Helper\Text;

/**
 * The resource scores:
 *  0 if the description specifies no title to begin with.
 * +1 if the title can be found in the resource's title.
 * -1 if it cannot.
 */
class TitleCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:title';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        if (!$basedOnDescription->getTitle()) {
            return 0;
        }

        return Text::substrCountArray($forResource->getTitle(), $basedOnDescription->getTitle())
            ?  1
            : -1;
    }
}
