<?php

namespace HafizhFadh\LaravelSimpleDatatable;

class SimpleDatatableFactory
{
    public function make($source): SimpleDatatable
    {
        return new SimpleDatatable($source);
    }
}
