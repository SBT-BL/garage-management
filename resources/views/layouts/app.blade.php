<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Garage Management')</title>

    <link rel="icon" type="image/png" href="{{ asset('storage/logo/logo-sm.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('storage/logo/logo-sm.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="@auth app-body @else app-body-guest @endauth">
    @auth
        @include('partials.sidebar')

        <div class="app-overlay" id="sidebarOverlay" aria-hidden="true"></div>

        <div class="app-wrapper">
            @include('partials.topbar')

            <main class="app-content">
                @include('partials.flash')
                @yield('content')
            </main>

            @include('partials.footer')
        </div>

        @include('partials.common-modal')
    @else
        <main class="@yield('main_class', 'container py-4')">
            @include('partials.flash')
            @yield('content')
        </main>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    @auth
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script>
            (function () {
                const body = document.body;
                const toggle = document.getElementById('sidebarToggle');
                const overlay = document.getElementById('sidebarOverlay');
                const storageKey = 'gm-sidebar-collapsed';

                function isDesktop() {
                    return window.matchMedia('(min-width: 992px)').matches;
                }

                if (isDesktop() && localStorage.getItem(storageKey) === '1') {
                    body.classList.add('sidebar-collapsed');
                }

                function closeMobileSidebar() {
                    body.classList.remove('sidebar-open');
                }

                toggle?.addEventListener('click', function () {
                    if (isDesktop()) {
                        body.classList.toggle('sidebar-collapsed');
                        localStorage.setItem(
                            storageKey,
                            body.classList.contains('sidebar-collapsed') ? '1' : '0'
                        );
                    } else {
                        body.classList.toggle('sidebar-open');
                    }
                });

                overlay?.addEventListener('click', closeMobileSidebar);

                window.addEventListener('resize', function () {
                    if (isDesktop()) {
                        closeMobileSidebar();
                    }
                });
            })();
        </script>
    @endauth
    @stack('scripts')
</body>
</html>
