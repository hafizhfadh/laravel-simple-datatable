<?php

namespace HafizHfadh\LaravelSimpleDatatable\Contracts;

interface Context
{
    /**
     * Apply search logic.
     *
     * @param string $term
     * @param array<int, \HafizHfadh\LaravelSimpleDatatable\Support\Column> $columns
     * @return self
     */
    public function search(string $term, array $columns): self;

    /**
     * Apply sort logic.
     *
     * @param string $column
     * @param string $direction
     * @return self
     */
    public function sort(string $column, string $direction): self;

    /**
     * Apply pagination logic and return the result.
     *
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function paginate(int $perPage, int $page): array;
}
