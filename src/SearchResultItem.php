<?php

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\ResourceInterface;
use WishgranterProject\MusicRadar\Sorting\RelevanceInterface;

/**
 * Represents a single search result item.
 */
class SearchResultItem
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicProbe\ResourceInterface $resource
     *   Represents a media resource, what we are actually after.
     *   Item essentially wraps around it.
     * @param WishgranterProject\MusicRadar\Sorting\RelevanceInterface|null $relevance
     *   Tallies how closely $resource matches our search criteria.
     *   It will help us order search results.
     */
    public function __construct(
        protected ResourceInterface $resource,
        protected ?RelevanceInterface $relevance = null,
    ) {
    }

    /**
     * Returns the resource.
     *
     * @return WishgranterProject\MusicProbe\ResourceInterface
     *   The resource.
     */
    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    /**
     * Returns the relevance to the search.
     *
     * @return WishgranterProject\MusicRadar\Sorting\RelevanceInterface|null
     *   The relevance.
     */
    public function getRelevance(): ?RelevanceInterface
    {
        return $this->relevance;
    }

    /**
     * Returns an array representation of the object.
     *
     * Useful to render it as a json string.
     *
     * @return array
     *   The object as an array.
     */
    public function toArray()
    {
        return [
            'resource'  => $this->resource->toArray(),
            'relevance' => $this->relevance->toArray(),
        ];
    }
}
