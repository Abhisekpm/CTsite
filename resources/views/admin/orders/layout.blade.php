<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Manage Custom Orders')</title>

    <!-- Add any specific CSS/JS for this standalone layout -->
    <!-- Example using Bootstrap (make sure it's available or use your own) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Add custom styles here if needed */
        body {
            padding-top: 1rem;
            padding-bottom: 1rem;
            background-color: #f8f9fa; /* Light gray background */
        }
        .container {
            max-width: 960px; /* Limit container width */
        }

        /* Responsive Table CSS */
        @media (max-width: 767.98px) {
            .responsive-table-wrapper .table thead {
                display: none; /* Hide table header on mobile */
            }
            .responsive-table-wrapper .table tbody,
            .responsive-table-wrapper .table tr,
            .responsive-table-wrapper .table td {
                display: block;
                width: 100%;
            }
            .responsive-table-wrapper .table tr {
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: .25rem;
            }
            .responsive-table-wrapper .table td {
                text-align: right; /* Align value to the right */
                padding-left: 50%; /* Make space for the label */
                position: relative;
                border: none;
                border-bottom: 1px solid #eee; /* Separator line */
            }
            .responsive-table-wrapper .table td:last-child {
                border-bottom: none;
            }
            .responsive-table-wrapper .table td::before {
                content: attr(data-label); /* Use data-label as the label text */
                position: absolute;
                left: .75rem; /* Padding from left */
                width: calc(50% - 1.5rem); /* Adjust width considering padding */
                padding-right: .5rem;
                text-align: left;
                font-weight: bold;
                white-space: nowrap;
            }
             /* Adjust Action button alignment */
            .responsive-table-wrapper .table td[data-label="Action"] a {
                display: inline-block; 
                width: auto; /* Allow button to size naturally */
            }
        }
    </style>

    @stack('styles')

</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
            <h1 class="h4">Cake Order Management</h1>
            {{-- Basic Logout Link - Assumes admin logout route is named 'logout' --}}
            @auth('admin')
                <form method="POST" action="{{ route('logout') }}"> {{-- Adjust route name if needed --}}
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                </form>
            @endauth
        </header>

        <main>
            {{-- Session Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <h6 class="alert-heading">Errors:</h6>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="pt-4 my-md-5 pt-md-5 border-top text-center text-muted">
            <small>&copy; {{ date('Y') }} {{ $settings->website_name ?? config('app.name') }} - Admin Orders</small>
        </footer>
    </div>

    <!-- Add any specific JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html> 