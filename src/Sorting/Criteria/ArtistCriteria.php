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
        $artist = $this->getRelevantName($basedOnDescription);
        // No artist in the description, skip it.
        if (!$artist) {
            return 0;
        }

        $points = $this->checkForArtist($forResource, $artist);

        // Let's be a bit lenient with the artist's name ...
        if ($points < 0 && ($variation = $this->getVariation($artist))) {
            $points = $this->checkForArtist($forResource, $variation);
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
        if ($forResource->getArtist() && Text::substrIntersect($forResource->getArtist(), $artist)) {
            return 2;
        }

        // Title or description will do...
        if (
            Text::substrIntersect($forResource->getTitle(), $artist) ||
            Text::substrIntersect($forResource->getDescription(), $artist)
        ) {
            return 1;
        }

        return -2;
    }

    /**
     * Given a name, returns a unpluralized variation.
     *
     * @param string|array $artist
     *   A name or array of names.
     *
     * @return string|array|null
     *   Returns the unpluralized variation, or null if it is unable to
     *   generate the variation.
     */
    protected function getVariation($artist)
    {
        $variation = $this->unpluralize($artist);
        return $variation == $artist
            ? null
            : $variation;
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

    /**
     * Given a description, returns the relevant name(s) to our criteria.
     *
     * Not gonna lie, this method only exists so it can be overwritten in child
     * classes.
     *
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   The description of the music.
     *
     * @return string|array
     *   The relevante name(s).
     */
    protected function getRelevantName(DescriptionInterface $description)
    {
        return $description->getArtist();
    }
}
