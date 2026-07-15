<?php

namespace WishgranterProject\MusicRadar;

class SearchResults implements SearchResultsInterface
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
    protected function getTotal(): int
    {
        return array_reduce($this->items, function ($carry, $result) {
            $carry += $result->getRelevance()->getTotal();
            return $carry;
        }, 0);
    }

    /**
     * Return the average punctuation for the search result.
     *
     * @return float
     *   The average punctuation.
     */
    protected function getAverage(): float
    {
        $total = $this->getTotal();
        $count = 0;

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
