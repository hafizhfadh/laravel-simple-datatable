<?php

namespace HafizHfadh\LaravelSimpleDatatable\Context;

use HafizHfadh\LaravelSimpleDatatable\Contracts\Context;
use Illuminate\Support\Collection;

class ClientContext implements Context
{
    protected Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function search(string $term, array $columns): self
    {
        if (empty($term) || empty($columns)) {
            return $this;
        }

        $this->collection = $this->collection->filter(function ($item) use ($term, $columns) {
            foreach ($columns as $column) {
                if ($column->isSearchable) {
                    $value = data_get($item, $column->name);
                    if (str_contains(strtolower((string) $value), strtolower($term))) {
                        return true;
                    }
                }
            }
            return false;
        });

        return $this;
    }

    public function sort(string $column, string $direction): self
    {
        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            return $this;
        }

        $this->collection = $direction === 'asc'
            ? $this->collection->sortBy($column)
            : $this->collection->sortByDesc($column);

        return $this;
    }

    public function paginate(int $perPage, int $page): array
    {
        // Client mode ignores backend pagination parameters and returns full dataset
        return [
            'data' => $this->collection->values()->all(),
            'meta' => [
                'mode' => 'client',
                'total' => $this->collection->count(),
            ],
        ];
    }
}
