<?php

namespace HafizhFadh\LaravelSimpleDatatable\Stages;

use Closure;
use HafizhFadh\LaravelSimpleDatatable\Contracts\Context;
use HafizhFadh\LaravelSimpleDatatable\Contracts\Stage;
use Illuminate\Support\Str;

class SortStage implements Stage
{
    protected ?string $column;
    protected string $direction;
    protected array $columns;

    public function __construct(?string $column, string $direction = 'asc', array $columns = [])
    {
        $this->column = $column;
        $this->direction = strtolower($direction);
        $this->columns = $columns;
    }

    public function handle(Context $context, Closure $next): mixed
    {
        if (empty($this->column)) {
            return $next($context);
        }

        // Validate sort direction
        if (! in_array($this->direction, ['asc', 'desc'])) {
            $this->direction = 'asc';
        }

        // Find the column definition
        $columnDef = null;
        foreach ($this->columns as $col) {
            if ($col->name === $this->column) {
                $columnDef = $col;

                break;
            }
        }

        // Check if column exists and is sortable
        if ($columnDef && $columnDef->isSortable) {
            $context->sort($this->column, $this->direction);
        }

        return $next($context);
    }
}
