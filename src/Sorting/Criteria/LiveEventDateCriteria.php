<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\Helper\Text;

/**
 * Live performances are undesirable.
 *
 * Most of the time, live recordings will feature the word "live", but other
 * times they instead feature a date.
 *
 * Score 1 if a date can be found.
 */
class LiveEventDateCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:liveEventDate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        $datePattern  = '/(January|February|March|April|May|June|July|August|September|October|November|December)[ ,]+[\d]+(st|th|nd|rd|)(of|)[ ,]\d*/';
        $shortPattern = '#\d+[/.]\d+[/.]\d+#';
        if (
            $this->dateInResource($forResource, $datePattern) ||
            $this->dateInResource($forResource, $shortPattern)
        ) {
            return 1;
        }

        return 0;
    }

    /**
     * Checks if there is a date in the resource.
     *
     * More likely to be the audio from a live event if there is one.
     *
     * @param WishgranterProject\MusicProbe\ResourceInterface $forResource
     *   The resource to check.
     * @param string $datePattern
     *   Regex pattern to use.
     *
     * @return bool
     *   True if there is.
     */
    protected function dateInResource(ResourceInterface $forResource, string $datePattern): bool
    {
        if (preg_match($datePattern, $forResource->getTitle())) {
            return true;
        }

        if ($forResource->getDescription() && preg_match($datePattern, $forResource->getDescription())) {
            return true;
        }

        return false;
    }
}
