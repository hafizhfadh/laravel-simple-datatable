<?php

namespace HafizhFadh\LaravelSimpleDatatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \HafizhFadh\LaravelSimpleDatatable\SimpleDatatable make(mixed $source)
 *
 * @see \HafizhFadh\LaravelSimpleDatatable\SimpleDatatableFactory
 */
class SimpleDatatable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-simple-datatable';
    }
}
