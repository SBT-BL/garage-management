@extends('layouts.app')

@section('title', 'Job Cards — Garage Management')
@section('page_title', 'Job Cards')

@push('styles')
    @include('partials.datatable-styles')
@endpush

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-3 mb-lg-4">
    <div class="d-none d-lg-block">
        <h1 class="page-header-title">Job Cards</h1>
        <p class="page-header-subtitle">Track repair jobs from intake to delivery</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.job-cards.create') }}" class="btn btn-success" title="Create">
            <i class="bi bi-plus-lg me-1"></i> Add Job Card
        </a>
    </div>
</div>

<x-mobile-card-list
    :url="route('admin.job-cards.cards')"
    search-placeholder="Search job cards..."
    aria-label="Job cards"
    data-filter-form="#job-card-filter-form"
>
    <x-slot:filters>
        <button
            type="button"
            class="btn btn-dark"
            data-bs-toggle="offcanvas"
            data-bs-target="#jobCardFilters"
            aria-controls="jobCardFilters"
        >
            Filters
        </button>
    </x-slot:filters>
</x-mobile-card-list>

<div class="app-card d-none d-lg-block">
    <div class="p-3 p-md-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <h2 class="h5 mb-0">Job Cards List</h2>
            <button
                type="button"
                class="btn btn-dark btn-sm"
                data-bs-toggle="offcanvas"
                data-bs-target="#jobCardFilters"
                aria-controls="jobCardFilters"
            >
                Filters
            </button>
        </div>

        {!! $dataTable->table(['class' => 'table table-hover table-customers dt-responsive nowrap w-100 mb-0']) !!}
    </div>
</div>

<div
    class="offcanvas offcanvas-bottom job-card-filters"
    tabindex="-1"
    id="jobCardFilters"
    aria-labelledby="jobCardFiltersLabel"
>
    <div class="offcanvas-header">
        <h2 class="offcanvas-title h5" id="jobCardFiltersLabel">Filters</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="job-card-filter-form" class="d-flex flex-column gap-3">
            <div>
                <label class="form-label" for="job-card-filter-start">Start Date</label>
                <input
                    type="date"
                    class="form-control"
                    id="job-card-filter-start"
                    name="start_date"
                    value="{{ $filterStartDate }}"
                >
            </div>
            <div>
                <label class="form-label" for="job-card-filter-end">End Date</label>
                <input
                    type="date"
                    class="form-control"
                    id="job-card-filter-end"
                    name="end_date"
                    value="{{ $filterEndDate }}"
                >
            </div>
            <div>
                <label class="form-label" for="job-card-filter-status">Status</label>
                <select class="form-select" id="job-card-filter-status" name="status">
                    <option value="open" selected>Pending &amp; In Progress</option>
                    <option value="all">All</option>
                    @foreach (\App\Enums\JobCardStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" for="job-card-filter-customer">Customer</label>
                <select class="form-select" id="job-card-filter-customer" name="customer_id">
                    <option value="">All customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" for="job-card-filter-service">Service</label>
                <select class="form-select" id="job-card-filter-service" name="service_id">
                    <option value="">All services</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    <div class="offcanvas-footer">
        <button type="submit" class="btn btn-primary w-100" form="job-card-filter-form">Apply Filters</button>
    </div>
</div>

@include('job-cards._delete-modal')
@endsection

@push('scripts')
    @include('partials.datatable-scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'text/javascript']) !!}
    <script>
        document.getElementById('job-card-filter-form')?.addEventListener('submit', function (event) {
            event.preventDefault();

            const panel = document.getElementById('jobCardFilters');

            if (panel && window.bootstrap) {
                window.bootstrap.Offcanvas.getOrCreateInstance(panel).hide();
            }

            document.querySelector('[data-mobile-card-list]')?.dispatchEvent(new Event('mobile-card-list:reload'));
            window.LaravelDataTables?.['job-cards-table']?.draw();
        });
    </script>
    <script src="{{ asset('js/job-card-status.js') }}?v={{ filemtime(public_path('js/job-card-status.js')) }}"></script>
@endpush
