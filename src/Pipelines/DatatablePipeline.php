<?php

namespace HafizhFadh\LaravelSimpleDatatable\Pipelines;

use HafizhFadh\LaravelSimpleDatatable\Contracts\Context;
use HafizhFadh\LaravelSimpleDatatable\Contracts\Stage;
use Illuminate\Pipeline\Pipeline;

class DatatablePipeline
{
    protected Context $context;
    protected array $stages = [];

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function addStage(Stage $stage): self
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function run(): mixed
    {
        return app(Pipeline::class)
            ->send($this->context)
            ->through($this->stages)
            ->then(fn ($context) => $context);
    }
}
