# Changelog

All notable changes to `laravel-simple-datatable` will be documented in this file.

## v1.0.1 - 2026-02-27

### Changed
- Maintenance release.

## v1.0.0 - 2026-02-27

### Added
-   Initial release of the package.
-   **Server-Side Processing**: Support for `Eloquent\Builder` with automatic pagination, sorting, and searching.
-   **Client-Side Processing**: Support for `Illuminate\Support\Collection` with in-memory filtering and sorting.
-   **Pipeline Architecture**: Modular stage system (`SearchStage`, `SortStage`, `PaginateStage`).
-   **Security**: Strict column whitelisting (`searchable()`, `sortable()`) and secure query handling.
-   **Fluent API**: `Column::make()` syntax for easy definition.
-   **TailwindCSS v4 Compatibility**: JSON response structure designed for easy integration with modern frontends.
-   **Laravel Support**: Full compatibility with Laravel 11.x and 12.x.
-   **PHP Support**: Requires PHP 8.3+.
