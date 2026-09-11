@extends('layouts.app')

@section('title', 'Customers — Garage Management')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div>
        <h1 class="page-header-title">Customers</h1>
        <p class="page-header-subtitle">Manage your garage customers</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Customer
        </a>
    </div>
</div>

<div class="app-card">
    <div class="p-3 border-bottom">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        class="form-control border-start-0"
                        placeholder="Search customers..."
                        aria-label="Search customers"
                    >
                </div>
            </div>
            <div class="col-6 col-md-auto">
                <select name="per_page" class="form-select" aria-label="Results per page">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }} / page</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <button type="submit" class="btn btn-outline-secondary w-100">Search</button>
            </div>
            @if ($search !== '')
                <div class="col-12 col-md-auto">
                    <a href="{{ route('admin.customers.index', ['per_page' => $perPage]) }}" class="btn btn-link text-decoration-none px-0">
                        Clear
                    </a>
                </div>
            @endif
        </form>
    </div>

    @if ($customers->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-people"></i>
            </div>
            @if ($search !== '')
                <h2 class="h5 mb-2">No customers found</h2>
                <p class="text-muted mb-3">No results matched “{{ $search }}”. Try a different search.</p>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">Clear search</a>
            @else
                <h2 class="h5 mb-2">No customers yet</h2>
                <p class="text-muted mb-3 mx-auto" style="max-width: 28rem;">
                    Start adding your garage customers to manage their vehicles and service history.
                </p>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Customer
                </a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-customers mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-3">Customer</th>
                        <th>WhatsApp</th>
                        <th class="d-none d-md-table-cell">Address</th>
                        <th class="d-none d-lg-table-cell">Added</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-initial">{{ $customer->initial() }}</span>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="fw-semibold text-decoration-none text-dark text-truncate d-inline-block">
                                            {{ $customer->name }}
                                        </a>
                                        <div class="small text-muted d-md-none text-truncate">{{ $customer->whatsapp_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="text-nowrap">{{ $customer->whatsapp_number }}</span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="text-muted">{{ $customer->address ?: '—' }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell text-nowrap text-muted">
                                {{ $customer->created_at->format('M j, Y') }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       class="btn btn-outline-secondary btn-icon"
                                       title="View"
                                       aria-label="View {{ $customer->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}"
                                       class="btn btn-outline-secondary btn-icon"
                                       title="Edit"
                                       aria-label="Edit {{ $customer->name }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-icon"
                                        title="Delete"
                                        aria-label="Delete {{ $customer->name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCustomerModal"
                                        data-delete-url="{{ route('admin.customers.destroy', $customer) }}"
                                        data-customer-name="{{ $customer->name }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $customers->links() }}
            </div>
        @endif
    @endif
</div>

@include('customers._delete-modal')
@endsection
