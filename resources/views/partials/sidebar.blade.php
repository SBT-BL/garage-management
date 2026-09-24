<aside class="app-sidebar d-none d-lg-flex" id="appSidebar" aria-label="Main navigation">
    <a href="{{ route('dashboard') }}" class="app-sidebar-brand" title="Garage Management">
        <img
            src="{{ asset('storage/logo/logo-dark.png') }}"
            alt="Garage Management"
            class="logo-lg"
            width="160"
            height="28"
            style="max-height: 28px; max-width: 160px; width: auto; height: auto; object-fit: contain;"
        >
        <img
            src="{{ asset('storage/logo/logo-sm.png') }}"
            alt="Garage Management"
            class="logo-sm"
            width="28"
            height="28"
            style="width: 28px; height: 28px; max-width: 28px; max-height: 28px; object-fit: contain;"
        >
    </a>

    <nav class="app-sidebar-menu">
        <p class="app-menu-label">Menu</p>
        <ul class="app-menu">
            <li>
                <a
                    href="{{ route('dashboard') }}"
                    class="app-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                >
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('admin.customers.index') }}"
                    class="app-menu-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-people"></i>
                    <span class="menu-text">Customers</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('admin.services.index') }}"
                    class="app-menu-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-wrench-adjustable"></i>
                    <span class="menu-text">Services</span>
                </a>
            </li>
            <li>
                <a
                    href="{{ route('admin.job-cards.index') }}"
                    class="app-menu-link {{ request()->routeIs('admin.job-cards.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-clipboard2-check"></i>
                    <span class="menu-text">Job Cards</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
