<?php

namespace HafizhFadh\LaravelSimpleDatatable\Stages;

use Closure;
use HafizhFadh\LaravelSimpleDatatable\Contracts\Context;
use HafizhFadh\LaravelSimpleDatatable\Contracts\Stage;

class PaginateStage implements Stage
{
    protected int $perPage;
    protected int $page;

    public function __construct(int $perPage = 10, int $page = 1)
    {
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function handle(Context $context, Closure $next): mixed
    {
        // Execute pagination and return result, breaking the chain
        return $context->paginate($this->perPage, $this->page);
    }
}
