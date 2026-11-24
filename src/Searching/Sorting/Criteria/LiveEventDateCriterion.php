<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting\Criteria;

use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicProbe\ResourceInterface;

/**
 * Live performances are undesirable.
 *
 * Most of the time, live recordings will feature the word "live", but other
 * times they instead feature a date and maybe a location.
 *
 * Score 1 if a date can be found, ignoring copyright and release notices that
 * would otherwise be mistaken for one.
 */
class LiveEventDateCriterion extends BaseCriterion implements CriterionInterface
{
    /**
     * Pattenr to match month names.
     *
     * @var string
     */
    protected const MONTH_NAMES = '(?:January|February|March|April|May|June|July|August|September|October|November|December)';

    /**
     * Regex to match years.
     *
     * @var string
     */
    protected const YEAR = '(?:19|20)\d{2}';

    /**
     * Regex to match dates.
     *
     * Multi alternative patterns.
     *
     * @var string
     */
    protected const DATE = '(?:' .
        '(?:19|20)\d{2}[-/.]\d{1,2}[-/.]\d{1,2}|' .
        '\d{1,2}[-/.]\d{1,2}[-/.](?:19|20)\d{2}|' .
        self::MONTH_NAMES . '\s+\d{1,2}(?:st|nd|rd|th)?,?\s+' . self::YEAR . '|' .
        '\d{1,2}(?:st|nd|rd|th)?\s+(?:of\s+)?' . self::MONTH_NAMES . ',?\s+' . self::YEAR .
    ')';

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'criterion:liveEventDate';
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
    protected function getDateRegexPatterns(): array
    {
        return [
            '#\b' . self::DATE . '\b#i',
        ];
    }

    /**
     * Retrieves the patterns of notices whose dates are not live event dates.
     *
     * Copyright notices, release dates, etc.
     *
     * @return string[]
     *   Array of regex patterns.
     */
    protected function getFalsePositivePatterns(): array
    {
        return [
            '#℗\s*' . self::YEAR . '\b#',
            '#(?:©|copyright)\s*' . self::YEAR . '(?:\s*[-–—]\s*' . self::YEAR . ')?\b#i',
            '#\ball\s+rights\s+reserved\b#i',
            '#\b(?:released|release|published|publication|uploaded|upload|available|made|posted)\b[^\d]{0,20}?' . self::DATE . '\b#i',
            '#\bout\s+(?:on\s+|worldwide\s+)*' . self::DATE . '\b#i',
            '#\bout\b[^\d]{0,25}?' . self::MONTH_NAMES . '\s+\d{1,2}(?:st|nd|rd|th)?,?\s+' . self::YEAR . '\b#i',
            '#\b(?:album|ep|single)\b[^\n()]{0,40}?\(\s*[^()]{0,30}?' . self::DATE . '\s*\)#i',
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
    protected function getResourceMatches(array $patterns, ResourceInterface $resource): array
    {
        $results = [];

        $fields = array_filter([
            $resource->getTitle(),
            $resource->getArtist(),
            $resource->getAlbum(),
            $resource->getDescription(),
        ]);

        foreach ($fields as &$field) {
            $this->stripFalsePositives($field);
        }

        foreach ($patterns as $pattern) {
            foreach ($fields as &$field) {
                if ($this->pregMatchAllAndStrip($pattern, $field, $matches)) {
                    $results = array_merge($results, $matches);
                }
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
    protected function getDescriptionMatches(array $patterns, DescriptionInterface $description): array
    {
        $results = [];

        $fields = array_filter([
            $description->getTitle(),
            $description->getArtist(),
            $description->getFeaturing(),
            $description->getCover(),
            $description->getSoundtrack(),
        ]);

        foreach ($patterns as $pattern) {
            foreach ($fields as &$field) {
                if ($this->pregMatchAllAndStrip($pattern, $field, $matches)) {
                    $results = array_merge($results, $matches);
                }
            }
        }

        return $results;
    }

    /**
     * Removes possible false positives from a string.
     *
     * Copyright notices, release dates etc.
     *
     * @param string|string[] $subject
     *   The string(s) to clean up, passed by reference.
     *
     * @return void
     */
    protected function stripFalsePositives(string|array &$subject): void
    {
        foreach ($this->getFalsePositivePatterns() as $pattern) {
            $subject = preg_replace($pattern, '', $subject) ?? $subject;
        }
    }

    /**
     * Perform global regular expression matches and remove them.
     *
     * @param string $pattern
     *   The pattern to search for, as a string.
     * @param string|string[] $subjects
     *   The input string(s).
     * @param array $matches
     *   Array of matching sub-strings.
     *
     * @return bool
     *   True if the pattern matched one of the subject(s).
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
