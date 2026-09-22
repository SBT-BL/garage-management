@extends('layouts.app')

@section('title', 'Customers — Garage Management')
@section('page_title', 'Customers')

@push('styles')
    @include('partials.datatable-styles')
@endpush

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div>
        <h1 class="page-header-title">Customers</h1>
        <p class="page-header-subtitle">Manage your garage customers</p>
    </div>
    <div class="page-actions">
        <a
            href="#"
            class="btn btn-success"
            data-ajax-popup="true"
            data-size="md"
            data-title="Create New Customer"
            data-url="{{ route('admin.customers.create') }}"
            title="Create"
        >
            <i class="bi bi-plus-lg me-1"></i> Add Customer
        </a>
    </div>
</div>

<div class="app-card">
    <div class="p-3 p-md-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <h2 class="h5 mb-0">Customers List</h2>
        </div>

        {!! $dataTable->table(['class' => 'table table-hover table-customers w-100 mb-0']) !!}
    </div>
</div>

@include('customers._delete-modal')
@endsection

@push('scripts')
    @include('partials.datatable-scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'text/javascript']) !!}
@endpush
