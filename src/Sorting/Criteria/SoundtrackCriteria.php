<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\Helper\Text;

/**
 * The resource scores:
 *  0 if the description specifies no soundtrack to begin with.
 * +1 if the soundtrack can be found in the resource.
 * -1 if it cannot.
 */
class SoundtrackCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:soundtrack';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        if (!$basedOnDescription->getSoundtrack()) {
            return 0;
        }

        foreach (['title', 'description'] as $property) {
            if (
                $forResource->{$property} &&
                Text::substrCountArray($forResource->{$property}, $basedOnDescription->getSoundtrack())
            ) {
                return 1;
            }
        }

        return -1;
    }
}
