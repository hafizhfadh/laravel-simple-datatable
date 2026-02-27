<?php

namespace HafizHfadh\LaravelSimpleDatatable;

use HafizHfadh\LaravelSimpleDatatable\Context\ClientContext;
use HafizHfadh\LaravelSimpleDatatable\Context\ServerContext;
use HafizHfadh\LaravelSimpleDatatable\Contracts\Context;
use HafizHfadh\LaravelSimpleDatatable\Pipelines\DatatablePipeline;
use HafizHfadh\LaravelSimpleDatatable\Response\ResponseBuilder;
use HafizHfadh\LaravelSimpleDatatable\Stages\PaginateStage;
use HafizHfadh\LaravelSimpleDatatable\Stages\SearchStage;
use HafizHfadh\LaravelSimpleDatatable\Stages\SortStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SimpleDatatable
{
    protected Builder|Collection $source;
    protected array $columns = [];
    protected ?Request $request = null;
    protected ?string $mode = null;

    public function __construct(Builder|Collection $source)
    {
        $this->source = $source;
        $this->request = request(); // Default to current request
        $this->mode = $source instanceof Builder ? 'server' : 'client';
    }

    public static function make(Builder|Collection $source): self
    {
        return new self($source);
    }

    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function request(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function serverSide(): self
    {
        $this->mode = 'server';
        return $this;
    }

    public function clientSide(): self
    {
        $this->mode = 'client';
        return $this;
    }

    protected function resolveContext(): Context
    {
        if ($this->mode === 'server' && $this->source instanceof Builder) {
            return new ServerContext($this->source);
        }

        if ($this->mode === 'client' && $this->source instanceof Collection) {
            return new ClientContext($this->source);
        }

        // Handle mixed cases (e.g. Builder forced to Client mode -> get Collection first)
        if ($this->mode === 'client' && $this->source instanceof Builder) {
            return new ClientContext($this->source->get());
        }

        // Handle Collection forced to Server mode (Not possible/supported efficiently)
        // Throw exception or fallback?
        // Spec says: "Backward compatibility with older Laravel versions is explicitly out of scope."
        // Spec says: "Server mode (default), Client mode (fallback)"
        // If Collection is passed but Server mode requested -> Logic error?
        // Let's assume user knows what they are doing.
        // But we can't do server-side pagination on a Collection without loading it all anyway.
        // So we fallback to ClientContext if source is Collection.

        return new ClientContext($this->source instanceof Builder ? $this->source->get() : $this->source);
    }

    public function process(): JsonResponse
    {
        $context = $this->resolveContext();
        $pipeline = new DatatablePipeline($context);

        // Add Stages
        // 1. Search
        $pipeline->addStage(new SearchStage(
            $this->request->get('search'),
            $this->columns
        ));

        // 2. Sort
        $pipeline->addStage(new SortStage(
            $this->request->get('sort'),
            $this->request->get('direction', 'asc'),
            $this->columns
        ));

        // 3. Paginate (Final Stage)
        $perPage = (int) $this->request->get('per_page', 10);
        $page = (int) $this->request->get('page', 1);
        
        // Ensure perPage is positive
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $pipeline->addStage(new PaginateStage($perPage, $page));

        // Run Pipeline
        $result = $pipeline->run();

        // Return Response
        return ResponseBuilder::success($result);
    }
}
