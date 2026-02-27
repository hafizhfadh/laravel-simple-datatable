# Laravel Simple Datatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hafizhfadh/laravel-simple-datatable.svg?style=flat-square)](https://packagist.org/packages/hafizhfadh/laravel-simple-datatable)
[![Tests](https://img.shields.io/github/actions/workflow/status/hafizhfadh/laravel-simple-datatable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hafizhfadh/laravel-simple-datatable/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/hafizhfadh/laravel-simple-datatable.svg?style=flat-square)](https://packagist.org/packages/hafizhfadh/laravel-simple-datatable)

**Laravel Simple Datatable** is a lightweight, framework-native engine designed to bridge Laravel with [simple-datatables](https://github.com/fiduswriter/Simple-DataTables) (and similar frontend libraries). It provides a fluent, secure, and pipeline-driven approach to handling server-side and client-side data processing without the bloat of jQuery or heavy dependencies.

Built with **TailwindCSS v4** compatibility in mind and engineered for **Laravel 11.x & 12.x**, this package offers enterprise-grade performance with a developer-friendly API.

## 🚀 Key Features

-   **Dual Processing Modes**: Seamlessly switch between **Server-side** (Eloquent Builder) and **Client-side** (Collection) modes.
-   **Pipeline-Driven Architecture**: Modular execution flow using Laravel's pipeline pattern for Search, Sort, and Pagination stages.
-   **Secure by Default**: Explicit column whitelisting, strict sort direction validation, and parameterized queries to prevent SQL injection.
-   **Fluent API**: Define columns and configuration using a clean, chainable syntax.
-   **No Frontend Dependencies**: Pure PHP backend logic that outputs standard JSON, giving you complete freedom over your frontend stack.
-   **PHP 8.3+ & Strict Typing**: Leveraging the latest PHP features for reliability and performance.

## 📦 Installation

You can install the package via composer:

```bash
composer require hafizhfadh/laravel-simple-datatable
```

## 🔧 Usage

### 1. Basic Setup

The simplest way to use the datatable is by passing an Eloquent query or a Collection to the `make` method.

#### Server-Side (Recommended for Large Datasets)

```php
use HafizhFadh\LaravelSimpleDatatable\Facades\SimpleDatatable;
use HafizhFadh\LaravelSimpleDatatable\Support\Column;
use App\Models\User;

public function index()
{
    // Automatically detects Builder and enables Server Mode
    return SimpleDatatable::make(User::query())
        ->columns([
            Column::make('name')->searchable()->sortable(),
            Column::make('email')->searchable(),
            Column::make('created_at')->sortable(),
        ])
        ->process();
}
```

#### Client-Side (Fallback for Small Datasets)

```php
use HafizhFadh\LaravelSimpleDatatable\Facades\SimpleDatatable;
use HafizhFadh\LaravelSimpleDatatable\Support\Column;

public function index()
{
    $users = User::all(); // Returns a Collection

    // Automatically detects Collection and enables Client Mode
    return SimpleDatatable::make($users)
        ->columns([
            Column::make('name')->searchable()->sortable(),
            Column::make('email')->searchable(),
        ])
        ->process();
}
```

### 2. Column Configuration

Columns must be explicitly defined to enable interaction. This is a security feature to prevent arbitrary sorting or searching on sensitive fields.

```php
Column::make('username')
    ->searchable() // Allows filtering by this column
    ->sortable();  // Allows sorting by this column
```

### 3. Frontend Integration

This package outputs a JSON response compatible with most datatable libraries. Here is an example response structure:

**Server Mode Response:**
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "last_page": 5,
        "total": 42
    }
}
```

**Client Mode Response:**
```json
{
    "data": [...],
    "meta": {
        "mode": "client",
        "total": 42
    }
}
```

## 🎨 Frontend Integration

For a comprehensive guide on integrating this package with **simple-datatables** and styling it with **TailwindCSS v4**, please refer to our [Frontend Integration Guide](docs/frontend-integration.md).

## ⚙️ Advanced Configuration

### Manual Mode Selection

You can force a specific mode if needed:

```php
SimpleDatatable::make(User::query())
    ->clientSide() // Force fetching all data and processing in memory
    ->process();
```

### Request Injection

By default, the engine uses the current global request. You can inject a custom request instance:

```php
SimpleDatatable::make($query)
    ->request($customRequest)
    ->process();
```

## 🧪 Testing

We use **Pest** for testing. To run the test suite:

```bash
composer test
```

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

1.  Fork the repository.
2.  Create a new feature branch.
3.  Commit your changes.
4.  Push to the branch.
5.  Open a Pull Request.

## 🔒 Security

If you discover any security related issues, please email info@hafizhfadh.id instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

**HafizhFadh/LaravelSimpleDatatable** — Simple, Secure, Scalable.
