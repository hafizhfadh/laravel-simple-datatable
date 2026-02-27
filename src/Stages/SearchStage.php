<?php

namespace HafizHfadh\LaravelSimpleDatatable\Stages;

use Closure;
use HafizHfadh\LaravelSimpleDatatable\Contracts\Context;
use HafizHfadh\LaravelSimpleDatatable\Contracts\Stage;

class SearchStage implements Stage
{
    protected ?string $term;
    protected array $columns;

    public function __construct(?string $term, array $columns)
    {
        $this->term = $term;
        $this->columns = $columns;
    }

    public function handle(Context $context, Closure $next): mixed
    {
        if (!empty($this->term)) {
            $searchableColumns = array_filter($this->columns, fn($column) => $column->isSearchable);
            if (!empty($searchableColumns)) {
                $context->search($this->term, $searchableColumns);
            }
        }

        return $next($context);
    }
}
