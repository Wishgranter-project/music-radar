<?php

namespace WishgranterProject\MusicRadar;

interface SearchResultsInterface extends \Iterator
{
    /**
     * Returns all result items.
     *
     * @return WishgranterProject\MusicRadar\SearchResultItem[]
     *   Array of items.
     */
    public function getItems(): array;

    /**
     * Returns the number of results.
     *
     * @return int
     *   Number of search results.
     */
    public function count(): int;

    /**
     * Returns an array representation of the object.
     *
     * Useful for data transfer.
     *
     * @return array
     *   The object as an array.
     */
    public function toArray(): array;
}
