<?php

declare(strict_types=1);

namespace WishgranterProject\MusicRadar\Searching;

class SearchResults implements SearchResultsInterface, \Countable
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicRadar\SearchResultItem[] $results
     *   List of search result items.
     */
    public function __construct(protected array $items = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $array = [
            'total' => $this->getTotal(),
            'average' => $this->getAverage(),
            'results' => []
        ];

        foreach ($this as $r) {
            $array['results'][] = $r->toArray();
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsScoringAtLeast(int $minScore): array
    {
        return array_filter($this->items, function ($result) use ($minScore) {
            $total = $result->getRelevance()?->getTotal() ?: 0;
            return $total >= $minScore;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function withDuplicatesRemoved(): SearchResultsInterface
    {
        $uniqueArray = [];

        foreach ($this->items as $item) {
            $resource = $item->getResource();
            $id = $resource->getId() ?: $resource->getSrc() ?: $resource->getHref() ?: $resource->getTitle();
            $key = $item->getResource()->getSourceId() . ':' . $id;
            if (isset($uniqueArray[$key])) {
                continue;
            }

            $uniqueArray[$key] = $item;
        }

        return new SearchResults(array_values($uniqueArray));
    }

    /**
     * {@inheritdoc}
     */
    public function withResultsMerged(SearchResultsInterface $newResults): SearchResultsInterface
    {
        return new SearchResults(array_merge($this->items, $newResults->getItems()));
    }

    /**
     * Returns the number of results.
     *
     * @return int
     *   Number of search results.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns the sum of all the results' punctuation.
     *
     * @return int
     *   All points summed up.
     */
    public function getTotal(): int
    {
        return array_reduce($this->items, function ($carry, $result) {
            $carry += $result?->getRelevance()?->getTotal() ?: 0;
            return $carry;
        }, 0);
    }

    /**
     * Return the average punctuation for the search result.
     *
     * @return float
     *   The average punctuation.
     */
    public function getAverage(): float
    {
        $total = $this->getTotal();
        $count = $this->count();

        return $count
            ? $total / $count
            : 0;
    }

    /**
     * \Iterator::current().
     */
    public function current(): mixed
    {
        return current($this->items);
    }

    /**
     * \Iterator::key().
     */
    public function key(): mixed
    {
        return key($this->items);
    }

    /**
     * \Iterator::next().
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * \Iterator::rewind().
     */
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * \Iterator::valid().
     */
    public function valid(): bool
    {
        return isset($this->items[$this->key()]);
    }
}
