# Frontend Integration Guide: simple-datatables + TailwindCSS v4

This guide demonstrates how to integrate the **[simple-datatables](https://github.com/fiduswriter/Simple-DataTables)** JavaScript library with **HafizhFadh/LaravelSimpleDatatable**, styled using **TailwindCSS v4**.

## 1. Installation

First, install the `simple-datatables` package via npm:

```bash
npm install simple-datatables
```

Make sure you have TailwindCSS v4 installed and configured in your project.

## 2. Basic Setup

Create a new JavaScript file (e.g., `resources/js/datatable.js`) and import the library.

```javascript
import { DataTable } from "simple-datatables";

// Optional: Import CSS if you want the base styles, 
// but we will be overriding them with Tailwind classes.
// import "simple-datatables/dist/style.css"; 
```

## 3. Configuration & TailwindCSS v4 Styling

To achieve a seamless look with TailwindCSS v4, we need to configure the `classes` option of `simple-datatables`. This replaces the default styles with utility classes.

Here is a robust configuration object:

```javascript
const tailwindConfig = {
    classes: {
        active: "bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-semibold",
        disabled: "cursor-not-allowed opacity-50 text-gray-400",
        selector: "block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:text-white dark:ring-gray-700",
        paginationList: "flex items-center gap-1",
        paginationListItem: "block",
        paginationListItemLink: "px-3 py-1 rounded-md border border-gray-300 dark:border-gray-700 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors",
        input: "block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:text-white dark:ring-gray-700",
        table: "min-w-full divide-y divide-gray-300 dark:divide-gray-700",
        thead: "bg-gray-50 dark:bg-gray-800",
        tbody: "divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900",
        tr: "hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors",
        th: "px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white",
        td: "whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300",
        top: "flex justify-between items-center mb-4",
        bottom: "flex justify-between items-center mt-4",
        info: "text-sm text-gray-700 dark:text-gray-400",
        sorter: "ml-1 inline-block w-4 h-4", // Icon container
    },
    labels: {
        placeholder: "Search...",
        perPage: "{select} entries per page",
        noRows: "No entries found",
        info: "Showing {start} to {end} of {rows} entries",
    }
};
```

## 4. Initialization (Server-Side Mode)

When using `HafizhFadh/LaravelSimpleDatatable` in **Server Mode**, you need to fetch data asynchronously.

### HTML Structure

```html
<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
    <table id="my-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be injected here -->
        </tbody>
    </table>
</div>
```

### JavaScript Implementation

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const tableElement = document.querySelector('#my-table');

    if (tableElement) {
        new DataTable(tableElement, {
            ...tailwindConfig, // Spread our Tailwind classes
            searchable: true,
            fixedHeight: true,
            perPage: 10,
            
            // Server-side Integration
            ajax: {
                url: "/api/users", // Your Laravel Endpoint
                load: function(xhr) {
                    // Adapt the response from Laravel Simple Datatable
                    // to what simple-datatables expects.
                    const response = JSON.parse(xhr.responseText);
                    
                    // Check if it's Server Mode response structure
                    if (response.meta && response.meta.current_page) {
                        return JSON.stringify({
                            data: response.data.map(item => [
                                // Map your data fields to columns explicitly
                                // This order MUST match your <thead>
                                item.name,
                                item.email,
                                new Date(item.created_at).toLocaleDateString()
                            ]),
                            // Map pagination metadata
                            total: response.meta.total,
                            currentPage: response.meta.current_page,
                            perPage: response.meta.per_page,
                            lastPage: response.meta.last_page
                        });
                    }
                    
                    // Fallback or Client Mode response
                    return JSON.stringify({
                        data: response.data,
                        total: response.meta.total
                    });
                }
            },
            
            // Debounce search requests to reduce server load
            debounceSearch: 300
        });
    }
});
```

## 5. Initialization (Client-Side Mode)

For smaller datasets where Laravel returns all data at once (Client Mode), the setup is simpler.

```javascript
// In Client Mode, Laravel returns: { data: [...], meta: { mode: 'client', ... } }

new DataTable("#my-table", {
    ...tailwindConfig,
    data: {
        // You can fetch this via fetch() or embed it in a blade script
        // headings: ["Name", "Email", "Date"], 
        // data: ... 
    }
});
```

## 6. Responsive Design

The configuration above handles basic responsiveness. However, for mobile devices, you might want to wrap the table in a container with horizontal scroll.

```html
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <!-- Table Container -->
                <table id="my-table">...</table>
            </div>
        </div>
    </div>
</div>
```

## 7. Customizing Icons (Optional)

`simple-datatables` uses default SVG icons. You can override them using CSS or by replacing the inner HTML of the sort headers after initialization if needed, but the default SVGs usually work well with Tailwind's text colors.

To color the sort icons with Tailwind:

```css
/* In your CSS */
.dataTable-sorter::before,
.dataTable-sorter::after {
    /* Adjust opacity/color as needed */
    opacity: 0.5;
}
.dataTable-sorter.asc::before,
.dataTable-sorter.desc::after {
    opacity: 1;
    /* You can use @apply text-indigo-600; here if using PostCSS */
}
```

## 8. Full Example (Blade Component)

Here is a reusable Blade component example:

```html
<!-- resources/views/components/datatable.blade.php -->
@props(['id', 'endpoint', 'columns'])

<div class="w-full">
    <table id="{{ $id }}">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
    </table>
</div>

<script type="module">
    import { DataTable } from "simple-datatables";
    
    // Import your tailwindConfig here or define it globally
    
    new DataTable("#{{ $id }}", {
        ...tailwindConfig,
        ajax: {
            url: "{{ $endpoint }}",
            load: function(xhr) {
                const response = JSON.parse(xhr.responseText);
                return JSON.stringify({
                    data: response.data.map(item => Object.values(item)),
                    total: response.meta.total
                });
            }
        }
    });
</script>
```

---
**Note**: Ensure your `composer.json` and `package.json` are in sync regarding versions. This guide assumes `simple-datatables` v9+ and TailwindCSS v4.
