<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

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
     * Returns an array representation of the object.
     *
     * Useful for data transfer.
     *
     * @return array
     *   The object as an array.
     */
    public function toArray(): array;
}
