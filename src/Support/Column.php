<?php

namespace HafizHfadh\LaravelSimpleDatatable\Support;

class Column
{
    public string $name;
    public bool $isSearchable = false;
    public bool $isSortable = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function searchable(bool $condition = true): self
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function sortable(bool $condition = true): self
    {
        $this->isSortable = $condition;

        return $this;
    }
}
