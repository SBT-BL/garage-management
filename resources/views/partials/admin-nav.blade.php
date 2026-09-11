<nav class="navbar navbar-expand-lg navbar-dark app-navbar mb-0">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Garage Management</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar"
                aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                       href="{{ route('admin.customers.index') }}">Customers</a>
                </li>
            </ul>

            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>
