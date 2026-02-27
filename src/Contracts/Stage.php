<?php

namespace HafizhFadh\LaravelSimpleDatatable\Contracts;

use Closure;

interface Stage
{
    /**
     * Handle the pipeline stage.
     *
     * @param Context $context
     * @param Closure $next
     * @return mixed
     */
    public function handle(Context $context, Closure $next): mixed;
}
