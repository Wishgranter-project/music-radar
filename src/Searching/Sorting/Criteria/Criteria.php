<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting\Criteria;

use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\Relevance;
use WishgranterProject\MusicRadar\Searching\Sorting\RelevanceInterface;

/**
 * {@inheritdoc}
 */
class Criteria implements CriteriaInterface
{
    /**
     * Array of criteria.
     */
    protected array $criteria = [];

    /**
     * {@inheritdoc}
     */
    public function addCriterion(CriterionInterface $criterion): CriteriaInterface
    {
        $this->criteria[$criterion->getId()] = $criterion;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function generateRelevance(
        ResourceInterface $onResource,
        DescriptionInterface $accordingToDescription,
    ): RelevanceInterface {
        $relevance = new Relevance();

        foreach ($this->criteria as $criteria) {
            $score = $criteria->getScore($onResource, $accordingToDescription);
            $relevance->addScore($score);
        }

        return $relevance;
    }

    /**
     * Generates a built-in, opinionated pre-set of criteria to sort resources.
     *
     * @return self
     *   Returns itself.
     */
    public static function getDefaultConfiguration(): CriteriaInterface
    {
        $criteria = new self();

        // Weight.
        $couldDoWithout = -1;
        $absolutelyNot = -40;
        $excellent = 10;

        $criteria
            ->addCriterion(new TitleCriterion($excellent))
            ->addCriterion(new ArtistCriterion($excellent))
            ->addCriterion(new ArtistFeaturedCriterion($excellent))
            ->addCriterion(new SoundtrackCriterion($excellent));

        //------------------------------------

        $indifferentTowards = [
            'lyrics',
            'official lyric video',
            'official music video',
            'official video',
        ];

        $criteria
            ->addCriterion(new LeftOverCriterion($couldDoWithout, $indifferentTowards));

        //------------------------------------

        $undesirables = [
            'acoustic'   => $couldDoWithout,
            'demotape'   => $couldDoWithout,
            'demo'       => $couldDoWithout,
            'remix'      => $couldDoWithout,
            'remixed'    => $couldDoWithout,
            'remaster'   => $couldDoWithout,
            'remastered' => $couldDoWithout,
            'tutorial'   => $absolutelyNot,
            'cover'      => $absolutelyNot,
            'live'       => $absolutelyNot,
            'tour'       => $absolutelyNot,
            'full album' => $absolutelyNot,
            'reaction'   => $absolutelyNot,
            'karaoke'    => $absolutelyNot,
        ];

        foreach ($undesirables as $term => $weight) {
            $criteria->addCriterion(new UndesirableCriterion($weight, $term));
        }

        //------------------------------------

        $criteria
            ->addCriterion(new LiveEventDateCriterion($absolutelyNot));

        return $criteria;
    }
}
