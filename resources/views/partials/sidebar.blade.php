<aside class="app-sidebar" id="appSidebar" aria-label="Main navigation">
    <a href="{{ route('dashboard') }}" class="app-sidebar-brand" title="Garage Management">
        <img
            src="{{ asset('storage/logo/logo-dark.png') }}"
            alt="Garage Management"
            class="logo-lg"
        >
        <img
            src="{{ asset('storage/logo/logo-sm.png') }}"
            alt="Garage Management"
            class="logo-sm"
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
        </ul>
    </nav>
</aside>
