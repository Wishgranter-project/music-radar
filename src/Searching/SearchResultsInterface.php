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
     * Returns results that scored $minScore or more.
     *
     * @param int $minScore
     *   Minimum score.
     *
     * @return WishgranterProject\MusicRadar\SearchResultItem[]
     *   Array of matching items.
     */
    public function getItemsScoringAtLeast(int $minScore): array;

    /**
     * Return a new set of results with duplicates removed.
     *
     * @return WishgranterProject\MusicRadar\SearchResultsInterface
     *   The new set of results.
     */
    public function withDuplicatesRemoved(): SearchResultsInterface;

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
