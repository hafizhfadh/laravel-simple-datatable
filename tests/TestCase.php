<?php

namespace HafizHfadh\LaravelSimpleDatatable\Tests;

use HafizHfadh\LaravelSimpleDatatable\LaravelSimpleDatatableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelSimpleDatatableServiceProvider::class,
        ];
    }
}
