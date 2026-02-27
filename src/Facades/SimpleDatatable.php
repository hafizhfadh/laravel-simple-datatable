<?php

namespace HafizHfadh\LaravelSimpleDatatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \HafizHfadh\LaravelSimpleDatatable\SimpleDatatable make(mixed $source)
 *
 * @see \HafizHfadh\LaravelSimpleDatatable\SimpleDatatableFactory
 */
class SimpleDatatable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-simple-datatable';
    }
}
