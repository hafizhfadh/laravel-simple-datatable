<?php

namespace HafizHfadh\LaravelSimpleDatatable\Context;

use HafizHfadh\LaravelSimpleDatatable\Contracts\Context;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ServerContext implements Context
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function search(string $term, array $columns): self
    {
        if (empty($term) || empty($columns)) {
            return $this;
        }

        $this->query->where(function (Builder $query) use ($term, $columns) {
            foreach ($columns as $column) {
                if ($column->isSearchable) {
                    $query->orWhere($column->name, 'like', "%{$term}%");
                }
            }
        });

        return $this;
    }

    public function sort(string $column, string $direction): self
    {
        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            return $this;
        }

        $this->query->orderBy($column, $direction);

        return $this;
    }

    public function paginate(int $perPage, int $page): array
    {
        $paginator = $this->query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
