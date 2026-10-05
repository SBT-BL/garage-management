@extends('layouts.app')

@section('title', $jobCard->job_card_number.' — Garage Management')
@section('page_title', 'Job Card Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.job-cards.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Back to Job Cards
    </a>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <h1 class="page-header-title mb-0">{{ $jobCard->job_card_number }}</h1>
            @include('job-cards.partials.status-badge', ['status' => $jobCard->status])
        </div>
        <p class="page-header-subtitle mb-0">{{ $jobCard->date->format('M j, Y') }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2 page-actions">
        <a href="{{ route('admin.job-cards.edit', $jobCard) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <button
            type="button"
            class="btn btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteJobCardModal"
            data-delete-url="{{ route('admin.job-cards.destroy', $jobCard) }}"
            data-job-card-number="{{ $jobCard->job_card_number }}"
        >
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="app-card p-4 h-100">
            <h2 class="h6 text-uppercase text-muted mb-3">Job Card Summary</h2>

            <div class="mb-3">
                <div class="small text-muted mb-1">Customer</div>
                <div class="fw-medium">
                    <a href="{{ route('admin.customers.show', $jobCard->customer) }}" class="text-decoration-none">
                        {{ $jobCard->customer->name }}
                    </a>
                </div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Vehicle</div>
                <div class="fw-medium">{{ $jobCard->vehicle->optionLabel() }}</div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Status</div>
                <div class="fw-medium">{{ $jobCard->status->value }}</div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Remark</div>
                <div class="fw-medium">{{ $jobCard->remark ?: 'No remark' }}</div>
            </div>

            <div>
                <div class="small text-muted mb-1">Grand Total</div>
                <div class="fs-4 fw-semibold">{{ number_format((float) $jobCard->grand_total, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="app-card p-4 h-100">
            <h2 class="h6 text-uppercase text-muted mb-3">Services</h2>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Remark</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobCard->jobCardServices as $line)
                            <tr>
                                <td>{{ $line->service->name }}</td>
                                <td>{{ $line->remark ?: '—' }}</td>
                                <td class="text-end text-nowrap">{{ number_format((float) $line->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Grand Total</th>
                            <th class="text-end">{{ number_format((float) $jobCard->grand_total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@include('job-cards._delete-modal')
@endsection
