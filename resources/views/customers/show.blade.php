@extends('layouts.app')

@section('title', $customer->name.' — Garage Management')
@section('page_title', 'Customer Details')

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
        <a
            href="#"
            class="btn btn-outline-secondary"
            data-ajax-popup="true"
            data-size="md"
            data-title="Edit Customer"
            data-url="{{ route('admin.customers.edit', $customer) }}"
        >
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
                <a
                    href="#"
                    class="btn btn-sm btn-success"
                    data-ajax-popup="true"
                    data-size="md"
                    data-title="Add Vehicle"
                    data-url="{{ route('admin.customers.vehicles.create', $customer) }}"
                >
                    <i class="bi bi-plus-lg me-1"></i> Add Vehicle
                </a>
            </div>

            @include('vehicles.partials.list', ['customer' => $customer])
        </div>
    </div>
</div>

@include('customers._delete-modal')
@include('vehicles._delete-modal')
@endsection
