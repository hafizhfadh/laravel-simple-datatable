<?php

namespace HafizHfadh\LaravelSimpleDatatable;

class SimpleDatatableFactory
{
    public function make($source): SimpleDatatable
    {
        return new SimpleDatatable($source);
    }
}
