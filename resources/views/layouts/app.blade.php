<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Garage Management')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
        }

        .app-navbar {
            background-color: #1f2937;
        }

        .app-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            background: #fff;
        }

        .page-header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .page-header-subtitle {
            color: #6b7280;
            margin-bottom: 0;
        }

        .avatar-initial {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            background: #e8eef7;
            color: #1d4ed8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .avatar-initial-lg {
            width: 3.5rem;
            height: 3.5rem;
            font-size: 1.25rem;
        }

        .table-customers th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6b7280;
            font-weight: 600;
            background: #f9fafb;
            border-bottom-width: 1px;
            white-space: nowrap;
        }

        .table-customers td {
            vertical-align: middle;
            color: #374151;
        }

        .btn-icon {
            width: 2rem;
            height: 2rem;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
        }

        .empty-state-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: #f3f4f6;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 575.98px) {
            .page-actions {
                width: 100%;
            }

            .page-actions .btn {
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @auth
        @include('partials.admin-nav')
    @endauth

    <main class="@yield('main_class', 'container py-4')">
        @include('partials.flash')
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
