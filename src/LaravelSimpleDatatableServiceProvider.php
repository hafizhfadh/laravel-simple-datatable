<?php

namespace HafizhFadh\LaravelSimpleDatatable;

use Illuminate\Support\ServiceProvider;

class LaravelSimpleDatatableServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('laravel-simple-datatable', function () {
            return new SimpleDatatableFactory();
        });
    }

    public function boot()
    {
        //
    }
}
