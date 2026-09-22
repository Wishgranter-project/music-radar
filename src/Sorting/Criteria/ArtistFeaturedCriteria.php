<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\Helper\Text;
use WishgranterProject\MusicRadar\Helper\English;

/**
 * The resource scores:
 *  0 if the description specifies no artist to begin with.
 * +2 if the artist can be found in the resource's artist property.
 * +1 if the artist can be found in the resource's other properties.
 * -2 if the artist cannot be found.
 */
class ArtistFeaturedCriteria extends ArtistCriteria implements CriteriaInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:featured-artist';
    }

    protected function getRelevantName(DescriptionInterface $description)
    {
        return $description->getFeaturing();
    }
}
