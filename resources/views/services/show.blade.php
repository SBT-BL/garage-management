@extends('layouts.app')

@section('title', $service->name.' — Garage Management')
@section('page_title', 'Service Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.services.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Back to Services
    </a>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <span class="avatar-initial avatar-initial-lg">{{ $service->initial() }}</span>
        <div>
            <h1 class="page-header-title mb-1">{{ $service->name }}</h1>
            <p class="page-header-subtitle">Service since {{ $service->created_at->format('M Y') }}</p>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2 page-actions">
        <a
            href="#"
            class="btn btn-outline-secondary"
            data-ajax-popup="true"
            data-size="md"
            data-title="Edit Service"
            data-url="{{ route('admin.services.edit', $service) }}"
        >
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <button
            type="button"
            class="btn btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteServiceModal"
            data-delete-url="{{ route('admin.services.destroy', $service) }}"
            data-service-name="{{ $service->name }}"
        >
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </div>
</div>

<div class="app-card p-4">
    <h2 class="h6 text-uppercase text-muted mb-3">Service Summary</h2>

    <div class="mb-3">
        <div class="small text-muted mb-1">Service Name</div>
        <div class="fw-medium">{{ $service->name }}</div>
    </div>

    <div>
        <div class="small text-muted mb-1">Added</div>
        <div class="fw-medium">{{ $service->created_at->format('M j, Y g:i A') }}</div>
    </div>
</div>

@include('services._delete-modal')
@endsection
