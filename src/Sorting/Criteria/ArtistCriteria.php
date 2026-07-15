<?php

namespace WishgranterProject\MusicRadar\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\Helper\Text;
use WishgranterProject\MusicRadar\Helper\English;

/**
 * The resource scores:
 *  0 if the description specifies no artist to begin with.
 * +2 if the artist can be found in the resource's artist description.
 * +1 if the artist can be found in the resource's other properties.
 * -1 if the artist cannot be found.
 */
class ArtistCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criteria:artist';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPoints(ResourceInterface $forResource, DescriptionInterface $basedOnDescription): int
    {
        // No artist in the description, skip.
        if (!$basedOnDescription->getArtist()) {
            return 0;
        }

        $artist = $basedOnDescription->getArtist();
        $points = $this->checkForArtist($forResource, $artist);

        // Let's be a bit lenient with the artist's name ...
        if ($points < 0) {
            $variation = $this->unpluralize($artist);
            $points = $artist != $variation
                ? $this->checkForArtist($forResource, $variation)
                : $points;
        }

        return $points;
    }

    /**
     * Checks if the artist's name is present in the resource.
     *
     * @param WishgranterProject\MusicProbe\ResourceInterface
     *   The playable resource to check.
     * @param string|array $artist
     *   The artist's name.
     *
     * @return int
     *   Negative number if it isn't. Positive if it is.
     */
    protected function checkForArtist(ResourceInterface $forResource, mixed $artist): int
    {
        if ($forResource->getArtist()) {
            // Resource has an artist!
            return Text::substrIntersect($forResource->getArtist(), $artist)
                ?  2
                : -2;
        }

        // Title or description will do...
        return Text::substrIntersect($forResource->getTitle(), $artist) ||
               Text::substrIntersect($forResource->getDescription(), $artist)
            ?  1
            : -1;
    }

    /**
     * Unpluralize one or more strings.
     *
     * @param string|array $strings
     *   A string or array of strings.
     *
     * @return string|array
     *   The parameter now in singular.
     */
    protected function unpluralize($strings)
    {
        if (! is_array($strings)) {
            return English::unpluralize($strings);
        }

        foreach ($strings as $k => $v) {
            $strings[$k] = English::unpluralize($v);
        }

        return $strings;
    }
}
