<?php

use HafizHfadh\LaravelSimpleDatatable\SimpleDatatable;
use HafizHfadh\LaravelSimpleDatatable\Support\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Model {
    protected $guarded = [];
}

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->timestamps();
    });
});

it('can handle client mode with collection', function () {
    $data = collect([
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane'],
    ]);

    $response = SimpleDatatable::make($data)
        ->columns([
            Column::make('name')->searchable()->sortable()
        ])
        ->process();

    $content = $response->getData(true);

    expect($content['meta']['mode'])->toBe('client');
    expect($content['data'])->toHaveCount(2);
});

it('can search in client mode', function () {
    $data = collect([
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane'],
    ]);

    request()->merge(['search' => 'Jane']);

    $response = SimpleDatatable::make($data)
        ->columns([
            Column::make('name')->searchable()
        ])
        ->process();

    $content = $response->getData(true);
    expect($content['data'])->toHaveCount(1);
    expect($content['data'][0]['name'])->toBe('Jane');
});

it('can sort in client mode', function () {
    $data = collect([
        ['id' => 1, 'name' => 'Beta'],
        ['id' => 2, 'name' => 'Alpha'],
    ]);

    request()->merge(['sort' => 'name', 'direction' => 'asc']);

    $response = SimpleDatatable::make($data)
        ->columns([
            Column::make('name')->sortable()
        ])
        ->process();

    $content = $response->getData(true);
    expect($content['data'][0]['name'])->toBe('Alpha');
    expect($content['data'][1]['name'])->toBe('Beta');
});

it('can handle server mode with builder', function () {
    User::create(['name' => 'John', 'email' => 'john@example.com']);
    User::create(['name' => 'Jane', 'email' => 'jane@example.com']);

    $query = User::query();

    $response = SimpleDatatable::make($query)
        ->columns([
            Column::make('name')->searchable()->sortable()
        ])
        ->process();

    $content = $response->getData(true);

    expect($content['meta']['total'])->toBe(2);
    expect($content['meta']['current_page'])->toBe(1);
    expect($content['data'])->toHaveCount(2);
});

it('can search in server mode', function () {
    User::create(['name' => 'John', 'email' => 'john@example.com']);
    User::create(['name' => 'Jane', 'email' => 'jane@example.com']);

    request()->merge(['search' => 'Jane']);

    $response = SimpleDatatable::make(User::query())
        ->columns([
            Column::make('name')->searchable()
        ])
        ->process();

    $content = $response->getData(true);
    expect($content['data'])->toHaveCount(1);
    expect($content['data'][0]['name'])->toBe('Jane');
});

it('can paginate in server mode', function () {
    User::create(['name' => 'A', 'email' => 'a@example.com']);
    User::create(['name' => 'B', 'email' => 'b@example.com']);
    User::create(['name' => 'C', 'email' => 'c@example.com']);

    request()->merge(['per_page' => 2, 'page' => 1]);

    $response = SimpleDatatable::make(User::query())
        ->columns([
            Column::make('name')
        ])
        ->process();

    $content = $response->getData(true);
    expect($content['data'])->toHaveCount(2);
    expect($content['meta']['total'])->toBe(3);
    expect($content['meta']['last_page'])->toBe(2);
});
