<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching\Sorting\Criteria;

use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicProbe\DescriptionInterface;
use WishgranterProject\MusicRadar\Searching\Sorting\RelevanceInterface;

/**
 * A set of criteria to sort search results.
 */
interface CriteriaInterface
{
    /**
     * Adds a criterion to sort the results.
     *
     * @param WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriterionInterface $criterion
     *   A criterion to help sort the search results.
     *
     * @return self
     *   Returns itself.
     */
    public function addCriterion(CriterionInterface $criterion): CriteriaInterface;

    /**
     * Return all criteria.
     *
     * @return WishgranterProject\MusicRadar\Searching\Sorting\Criteria\CriterionInterface[]
     *   The criteria.
     */
    public function getCriteria(): array;

    /**
     * Generates a relevance based on our criteria.
     *
     * @param WishgranterProject\MusicProbe\ResourceInterface $onResource
     *   The resource to tally.
     * @param WishgranterProject\MusicProbe\DescriptionInterface $accordingToDescription
     *   The description used as the base.
     *
     * @return WishgranterProject\MusicRadar\Searching\Sorting\RelevanceInterface
     *   The relevance scored by the resource.
     */
    public function generateRelevance(
        ResourceInterface $onResource,
        DescriptionInterface $accordingToDescription,
    );

    /**
     * Generates a built-in, opinionated pre-set of criteria to sort resources.
     *
     * @return self
     *   Returns itself.
     */
    public static function getDefaultConfiguration(): CriteriaInterface;
}
