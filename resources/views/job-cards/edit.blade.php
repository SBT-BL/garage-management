@extends('layouts.app')

@section('title', 'Edit '.$jobCard->job_card_number.' — Garage Management')
@section('page_title', 'Edit Job Card')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.job-cards.show', $jobCard) }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Back to Job Card
    </a>
</div>

<div class="d-none d-lg-block mb-4">
    <h1 class="page-header-title">Edit Job Card</h1>
    <p class="page-header-subtitle">{{ $jobCard->job_card_number }}</p>
</div>

<div class="app-card p-3 p-md-4">
    <form
        method="POST"
        action="{{ route('admin.job-cards.update', $jobCard) }}"
        id="job-card-form"
    >
        @csrf
        @method('PUT')
        @include('job-cards._form')

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.job-cards.show', $jobCard) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">Update Job Card</button>
        </div>
    </form>
</div>
@endsection

@include('job-cards.partials.form-assets')
