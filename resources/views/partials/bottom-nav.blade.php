@php
    $isCustomers = request()->routeIs('admin.customers.*');
    $isServices = request()->routeIs('admin.services.*');
    $isJobCards = request()->routeIs('admin.job-cards.*');
@endphp

<nav class="app-bottom-nav d-lg-none" aria-label="Primary mobile navigation">
    <div class="app-bottom-nav-inner">
        <a
            href="{{ route('admin.customers.index') }}"
            class="app-bottom-nav-item {{ $isCustomers ? 'active' : '' }}"
            @if ($isCustomers) aria-current="page" @endif
        >
            <i class="bi bi-people" aria-hidden="true"></i>
            <span>Customers</span>
        </a>

        <a
            href="{{ route('admin.job-cards.index') }}"
            class="app-bottom-nav-item app-bottom-nav-fab {{ $isJobCards ? 'active' : '' }}"
            @if ($isJobCards) aria-current="page" @endif
            title="Job Card Form"
        >
            <span class="app-bottom-nav-fab-btn" aria-hidden="true">
                <i class="bi bi-plus-lg"></i>
            </span>
            <span class="app-bottom-nav-fab-label">Job Card</span>
        </a>

        <a
            href="{{ route('admin.services.index') }}"
            class="app-bottom-nav-item {{ $isServices ? 'active' : '' }}"
            @if ($isServices) aria-current="page" @endif
        >
            <i class="bi bi-wrench-adjustable" aria-hidden="true"></i>
            <span>Services</span>
        </a>
    </div>
</nav>
