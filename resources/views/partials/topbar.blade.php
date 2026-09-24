<header class="app-topbar">
    <div class="app-topbar-left">
        <a href="{{ route('dashboard') }}" class="app-topbar-brand d-lg-none" title="Dashboard">
            <img
                src="{{ asset('storage/logo/logo-sm.png') }}"
                alt="Garage Management"
                class="app-topbar-brand-logo"
                width="28"
                height="28"
                style="width: 28px; height: 28px; max-width: 28px; max-height: 28px; object-fit: contain;"
            >
        </a>
        <button
            type="button"
            class="app-sidebar-toggle d-none d-lg-inline-flex"
            id="sidebarToggle"
            aria-label="Toggle sidebar"
            aria-controls="appSidebar"
        >
            <i class="bi bi-list"></i>
        </button>
        <h1 class="app-topbar-title d-none d-sm-block">@yield('page_title', 'Garage Management')</h1>
    </div>

    <div class="app-topbar-right">
        <div class="dropdown">
            <button
                class="btn btn-link text-decoration-none text-dark d-flex align-items-center gap-2 p-0"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="avatar-initial avatar-initial-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email ?? 'A', 0, 1)) }}
                </span>
                <span class="d-none d-md-flex flex-column align-items-start text-start">
                    <span class="app-user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <span class="app-user-role">Administrator</span>
                </span>
                <i class="bi bi-chevron-down small text-muted d-none d-md-inline"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item d-lg-none" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="d-lg-none"><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
