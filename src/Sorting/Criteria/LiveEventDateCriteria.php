<?php

declare(strict_types=1);

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
        $patterns           = $this->getDateRegexPatterns();
        $descriptionMatches = $this->getDescriptionMatches($patterns, $basedOnDescription);
        $resourceMatches    = $this->getResourceMatches($patterns, $forResource);

        // It would be counterproductive to count sub-strings that make part of
        // the description, so we filter those out.
        $differences        = array_diff($resourceMatches, $descriptionMatches);

        return $resourceMatches && $differences
            ? 1
            : 0;
    }

    /**
     * Retrieves our list of regex patterns to identify dates.
     *
     * @return string[]
     *   Array of regex patterns.
     */
    protected function getDateRegexPatterns()
    {
        return [
            '#(January|February|March|April|May|June|July|August|September|October|November|December)#',
            '#[\d]+(st|th|nd|rd)#',
            '#\d+[/.-]\d+[/.-]\d+#',
            '#19\d{2}#',
            '#20\d{2}#',
        ];
    }

    /**
     * Given regex patterns, return matching sub-strings in a resource.
     *
     * @param string[] $patterns
     *   The pattern to search for.
     * @param WishgranterProject\MusicProbe\ResourceInterface $resource
     *   The playable resource.
     *
     * @return string[]
     *   Array of matching strings.
     */
    protected function getResourceMatches(array $patterns, ResourceInterface $resource)
    {
        $results = [];

        $title       = $resource->getTitle();
        $artist      = $resource->getArtist();
        $album       = $resource->getAlbum();
        $description = $resource->getDescription();

        foreach ($patterns as $pattern) {
            if ($title && $this->pregMatchAllAndStrip($pattern, $title, $matches)) {
                $results = array_merge($results, $matches);
            }

            if ($artist && $this->pregMatchAllAndStrip($pattern, $artist, $matches)) {
                $results = array_merge($results, $matches);
            }

            if ($album && $this->pregMatchAllAndStrip($pattern, $album, $matches)) {
                $results = array_merge($results, $matches);
            }

            if ($description && $this->pregMatchAllAndStrip($pattern, $description, $matches)) {
                $results = array_merge($results, $matches);
            }
        }

        return $results;
    }

    /**
     * Given regex patterns, return matching sub-strings in a description.
     *
     * @param string[] $patterns
     *   The pattern to search for.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $description
     *   The description of a music.
     *
     * @return string[]
     *   Array of matching strings.
     */
    protected function getDescriptionMatches(array $patterns, DescriptionInterface $description)
    {
        $results = [];

        $title      = $description->getTitle();
        $artist     = $description->getArtist();
        $soundtrack = $description->getSoundtrack();

        foreach ($patterns as $pattern) {
            if ($title && $this->pregMatchAllAndStrip($pattern, $title, $matches)) {
                $results = array_merge($results, $matches);
            }

            if ($artist && $this->pregMatchAllAndStrip($pattern, $artist, $matches)) {
                $results = array_merge($results, $matches);
            }

            if ($soundtrack && $this->pregMatchAllAndStrip($pattern, $soundtrack, $matches)) {
                $results = array_merge($results, $matches);
            }
        }

        return $results;
    }

    /**
     * Perform global regular expression matches.
     *
     * @param string $pattern
     *   The pattern to search for, as a string.
     * @param string|string[] $subjects
     *   The input string(s).
     * @param array $matches
     *   Array of matching sub-strings.
     *
     * @return array
     *   True if the pattern matche the subject(s).
     */
    protected function pregMatchAllAndStrip(string $pattern, &$subjects, &$matches): bool
    {
        $matches  = [];
        $subjects = (array) $subjects;

        foreach ($subjects as &$subject) {
            if (preg_match_all($pattern, $subject, $m)) {
                $matches = array_merge($matches, $m[0]);
                $subject = preg_replace($pattern, '', $subject);
            }
        }

        return (bool) $matches;
    }
}
