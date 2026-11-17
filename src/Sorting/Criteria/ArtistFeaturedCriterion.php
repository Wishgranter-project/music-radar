<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;

/**
 * The resource scores:
 *  0 if the description specifies no artist to begin with.
 * +2 if the artist can be found in the resource's artist property.
 * +1 if the artist can be found in the resource's other properties.
 * -2 if the artist cannot be found.
 */
class ArtistFeaturedCriterion extends ArtistCriterion implements CriterionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criterion:featured-artist';
    }

    protected function getRelevantName(DescriptionInterface $description)
    {
        return $description->getFeaturing();
    }
}
