@extends('layouts.app')

@section('title', $customer->name.' — Garage Management')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.customers.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <span class="avatar-initial avatar-initial-lg">{{ $customer->initial() }}</span>
        <div>
            <h1 class="page-header-title mb-1">{{ $customer->name }}</h1>
            <p class="page-header-subtitle">Customer since {{ $customer->created_at->format('M Y') }}</p>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2 page-actions">
        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <button
            type="button"
            class="btn btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteCustomerModal"
            data-delete-url="{{ route('admin.customers.destroy', $customer) }}"
            data-customer-name="{{ $customer->name }}"
        >
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="app-card p-4 h-100">
            <h2 class="h6 text-uppercase text-muted mb-3">Customer Summary</h2>

            <div class="mb-3">
                <div class="small text-muted mb-1">WhatsApp</div>
                <div class="fw-medium">{{ $customer->whatsapp_number }}</div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Address</div>
                <div class="fw-medium">{{ $customer->address ?: 'No address provided' }}</div>
            </div>

            <div>
                <div class="small text-muted mb-1">Added</div>
                <div class="fw-medium">{{ $customer->created_at->format('M j, Y g:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="app-card p-4 h-100">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <h2 class="h6 text-uppercase text-muted mb-0">Vehicles</h2>
                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Coming soon">
                    <i class="bi bi-plus-lg me-1"></i> Add Vehicle
                </button>
            </div>

            <div class="empty-state py-4">
                <div class="empty-state-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <h3 class="h6 mb-2">No vehicles added yet</h3>
                <p class="text-muted mb-0 small">
                    Vehicles belonging to this customer will appear here.
                </p>
            </div>
        </div>
    </div>
</div>

@include('customers._delete-modal')
@endsection
